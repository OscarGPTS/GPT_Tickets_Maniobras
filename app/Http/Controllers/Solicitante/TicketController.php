<?php

namespace App\Http\Controllers\Solicitante;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar tickets del usuario
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->tickets()
            ->with(['assignedTo', 'images', 'survey'])
            ->orderByRaw("CASE WHEN status = 'pendiente' THEN 0 WHEN status = 'en_proceso' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at'); // Más recientes primero

        // Filtrar por estado si se proporciona
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Paginación con parámetros persistentes
        $tickets = $query->paginate(15)->appends($request->except('page'));

        // Encuestas pendientes
        $pendingSurveys = \App\Models\Survey::whereHas('ticket', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereNull('completed_at')
        ->with('ticket.assignedTo')
        ->get();

        // Verificar si puede crear un nuevo ticket
        $canCreateTicket = $user->canCreateTicket();

        return view('solicitante.tickets.index', compact('tickets', 'pendingSurveys', 'canCreateTicket'));
    }

    /**
     * Formulario para crear ticket
     */
    public function create()
    {
        // Verificar si el usuario puede crear un nuevo ticket
        if (!Auth::user()->canCreateTicket()) {
            return redirect()->route('solicitante.tickets.index')
                ->with('error', 'Debes completar las encuestas pendientes antes de crear un nuevo ticket.');
        }

        return view('solicitante.tickets.create');
    }

    /**
     * Guardar nuevo ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verificar si el usuario puede crear un ticket
        if (!Auth::user()->canCreateTicket()) {
            return back()->withErrors(['error' => 'Debes completar las encuestas pendientes antes de crear un nuevo ticket.']);
        }

        DB::beginTransaction();
        try {
            // Crear el ticket
            $ticket = Ticket::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'status' => Ticket::STATUS_PENDIENTE,
            ]);

            // Procesar imágenes si las hay
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('tickets/' . $ticket->id, 'public');
                    
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'uploaded_by' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'type' => TicketImage::TYPE_SOLICITUD,
                    ]);
                }
            }

            // Notificar a todos los usuarios de almacén
            try {
                $almacenUsers = User::role('almacen')->get();
                if ($almacenUsers->count() > 0) {
                    Notification::send($almacenUsers, new TicketCreatedNotification($ticket));
                    Log::info('Notificación enviada a ' . $almacenUsers->count() . ' usuarios de almacén para ticket #' . $ticket->id);
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar notificaciones de ticket creado: ' . $e->getMessage());
            }
            
            DB::commit();
            return redirect()->route('solicitante.tickets.index')
                ->with('success', 'Ticket creado exitosamente. Se ha notificado al equipo de almacén.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver detalle de un ticket
     */
    public function show(Ticket $ticket)
    {
        // Verificar que el usuario puede ver este ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        $ticket->load(['user', 'assignedTo', 'images', 'survey']);

        return view('solicitante.tickets.show', compact('ticket'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Ticket $ticket)
    {
        // Solo el propietario puede editar si el ticket está pendiente
        if ($ticket->user_id !== Auth::id() || !$ticket->isPendiente()) {
            abort(403, 'No puedes editar este ticket.');
        }

        return view('solicitante.tickets.edit', compact('ticket'));
    }

    /**
     * Actualizar ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        // Solo el propietario puede editar si el ticket está pendiente
        if ($ticket->user_id !== Auth::id() || !$ticket->isPendiente()) {
            abort(403, 'No puedes editar este ticket.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $ticket->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Procesar nuevas imágenes si las hay
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('tickets/' . $ticket->id, 'public');
                    
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'uploaded_by' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'type' => TicketImage::TYPE_SOLICITUD,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('solicitante.tickets.show', $ticket)
                ->with('success', 'Ticket actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar ticket
     */
    public function destroy(Ticket $ticket)
    {
        // Solo el propietario puede eliminar si el ticket está pendiente
        if ($ticket->user_id !== Auth::id() || !$ticket->isPendiente()) {
            abort(403, 'No puedes eliminar este ticket.');
        }

        try {
            // Eliminar imágenes del almacenamiento
            foreach ($ticket->images as $image) {
                Storage::disk('public')->delete($image->file_path);
            }

            $ticket->delete();

            return redirect()->route('solicitante.tickets.index')
                ->with('success', 'Ticket eliminado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar imagen específica del ticket
     */
    public function deleteImage(TicketImage $image)
    {
        $ticket = $image->ticket;
        
        // Verificar permisos
        if ($ticket->user_id !== Auth::id() || !$ticket->isPendiente()) {
            abort(403, 'No puedes eliminar esta imagen.');
        }

        try {
            Storage::disk('public')->delete($image->file_path);
            $image->delete();

            return back()->with('success', 'Imagen eliminada exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la imagen: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancelar un ticket
     */
    public function cancel(Request $request, Ticket $ticket)
    {
        // Verificar que el usuario es el propietario
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'No puedes cancelar este ticket.');
        }

        // Verificar que el ticket puede ser cancelado
        if (!$ticket->canBeCancelled()) {
            return back()->withErrors(['error' => 'Este ticket no puede ser cancelado en su estado actual.']);
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $cancellationReason = $request->cancellation_reason ?: 'Cancelado';
            $ticket->cancel($cancellationReason);

            // Notificar al miembro de almacén si estaba asignado
            if ($ticket->assigned_to) {
                try {
                    $ticket->assignedTo->notify(new TicketCancelledNotification($ticket));
                    Log::info('Notificación de cancelación enviada al usuario #' . $ticket->assigned_to . ' para ticket #' . $ticket->id);
                } catch (\Exception $e) {
                    Log::error('Error al enviar notificación de ticket cancelado: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('solicitante.tickets.index')
                ->with('success', 'Ticket cancelado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al cancelar el ticket: ' . $e->getMessage()]);
        }
    }
}
