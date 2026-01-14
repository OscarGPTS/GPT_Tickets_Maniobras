<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\Survey;
use App\Exports\TicketsExport;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketAssignedToWarehouseNotification;
use App\Notifications\TicketCompletedNotification;
use App\Mail\TicketCompletedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        /* $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAlmacen() && !Auth::user()->isAdmin()) {
                abort(403, 'No tienes acceso al panel de almacén.');
            }
            return $next($request);
        }); */
    }

    /**
     * Listar tickets pendientes
     */
    public function pending()
    {
        $tickets = Ticket::pendientes()
            ->with(['user', 'solicitudImages'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Si el usuario es admin, obtener lista de usuarios de almacén para asignación
        $almacenUsers = null;
        if (Auth::user()->isAdmin()) {
            $almacenUsers = \App\Models\User::role('almacen')->orderBy('name')->get();
        }

        return view('almacen.pending-tickets', compact('tickets', 'almacenUsers'));
    }

    /**
     * Mis tickets asignados (historial completo)
     */
    public function mine(Request $request)
    {
        $query = Ticket::where('assigned_to', Auth::id())
            ->with(['user', 'images', 'survey']);

        // Filtro por estado
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Priorizar los que están en proceso y luego ordenar por más recientes
        $tickets = $query
            ->orderByRaw("CASE WHEN status = 'en_proceso' THEN 0 ELSE 1 END")
            ->orderByDesc('assigned_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        // Estadísticas del usuario
        $stats = [
            'total' => Ticket::where('assigned_to', Auth::id())->whereNotIn('status', ['cancelado'])->count(),
            'en_proceso' => Ticket::where('assigned_to', Auth::id())->where('status', 'en_proceso')->count(),
            'completados' => Ticket::where('assigned_to', Auth::id())->where('status', 'finalizado')->count(),
            'promedio_calificacion' => round(
                \App\Models\Survey::whereHas('ticket', function($q) {
                    $q->where('assigned_to', Auth::id());
                })->whereNotNull('completed_at')->avg('rating') ?? 0, 
                1
            ),
        ];

        return view('almacen.my-tickets-table', compact('tickets', 'stats'));
    }

    /**
     * Ver detalle de ticket
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'images', 'survey']);
        
        // Si el usuario es admin, obtener lista de usuarios de almacén para asignación
        $almacenUsers = null;
        if (Auth::user()->isAdmin()) {
            $almacenUsers = \App\Models\User::role('almacen')->orderBy('name')->get();
        }

        return view('almacen.ticket', compact('ticket', 'almacenUsers'));
    }

    /**
     * Asignar ticket
     * - Personal de almacén: se asigna a sí mismo
     * - Administrador: asigna a un usuario de almacén específico
     */
    public function assign(Request $request, Ticket $ticket)
    {
        // Verificar que el ticket no esté cancelado
        if ($ticket->status === 'cancelado') {
            return back()->withErrors(['error' => 'No se puede asignar un ticket cancelado.']);
        }
        
        if (!$ticket->isPendiente()) {
            return back()->withErrors(['error' => 'Este ticket ya ha sido asignado.']);
        }

        DB::beginTransaction();
        try {
            // Si es admin, debe especificar a quién asignar
            if (Auth::user()->isAdmin()) {
                $request->validate([
                    'assigned_to' => 'required|exists:users,id',
                ]);
                
                $assignedUser = \App\Models\User::findOrFail($request->assigned_to);
                
                // Verificar que el usuario tenga rol de almacén
                if (!$assignedUser->hasRole('almacen')) {
                    return back()->withErrors(['error' => 'Solo puedes asignar tickets a personal de almacén.']);
                }
                
                $ticket->assignTo($assignedUser);
            } else {
                // Personal de almacén se asigna a sí mismo
                $ticket->assignTo(Auth::user());
            }

            // Notificar al usuario solicitante
            try {
                $ticket->user->notify(new TicketAssignedNotification($ticket));
                Log::info('Notificación de asignación enviada al usuario #' . $ticket->user_id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de ticket asignado: ' . $e->getMessage());
            }

            // Notificar a la persona asignada de almacén
            try {
                $ticket->assignedTo->notify(new TicketAssignedToWarehouseNotification($ticket));
                Log::info('Notificación de asignación enviada al miembro de almacén #' . $ticket->assignedTo->id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación al miembro de almacén asignado: ' . $e->getMessage());
            }

            DB::commit();

            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Ticket asignado exitosamente a ' . $ticket->assignedTo->name);
            } else {
                return redirect()->route('almacen.tickets.mine')
                    ->with('success', 'Ticket asignado exitosamente.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al asignar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Agregar progreso
     */
    public function addProgress(Request $request, Ticket $ticket)
    {
        // Verificar que es el ticket asignado al usuario actual
        if ($ticket->assigned_to !== Auth::id()) {
            abort(403, 'No puedes agregar progreso a este ticket.');
        }

        $request->validate([
            'progress_comment' => 'required|string|max:1000',
            'progress_images' => 'nullable|array|max:3',
            'progress_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Subir imágenes de progreso
            if ($request->hasFile('progress_images')) {
                foreach ($request->file('progress_images') as $image) {
                    $path = $image->store('tickets/progress', 'public');
                    
                    $ticket->images()->create([
                        'file_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'type' => 'progreso'
                    ]);
                }
            }

            // Actualizar estado a en_proceso si está pendiente
            if ($ticket->status === 'pendiente') {
                $ticket->update(['status' => 'en_proceso']);
            }

            DB::commit();

            return redirect()->route('almacen.tickets.show', $ticket)
                ->with('success', 'Progreso agregado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al agregar progreso: ' . $e->getMessage()]);
        }
    }

    /**
     * Completar ticket
     */
    public function complete(Request $request, Ticket $ticket)
    {
        // Verificar que el ticket no esté cancelado
        if ($ticket->status === 'cancelado') {
            return back()->withErrors(['error' => 'No se puede completar un ticket cancelado.']);
        }
        
        // Verificar permisos
        if ($ticket->assigned_to !== Auth::id() || !$ticket->isEnProceso()) {
            abort(403, 'No puedes completar este ticket.');
        }

        $request->validate([
            'work_evidence' => 'required|string',
            'evidence_images' => 'nullable|array|max:5',
            'evidence_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar ticket
            $ticket->update([
                'work_evidence' => $request->work_evidence,
                'status' => Ticket::STATUS_FINALIZADO,
                'completed_at' => now(),
            ]);

            // Procesar imágenes de evidencia
            if ($request->hasFile('evidence_images')) {
                foreach ($request->file('evidence_images') as $image) {
                    $path = $image->store('tickets/' . $ticket->id . '/evidence', 'public');
                    
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'uploaded_by' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'type' => TicketImage::TYPE_EVIDENCIA,
                    ]);
                }
            }

            // Crear encuesta de satisfacción
            Survey::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'rating' => 0,
                'comments' => null,
            ]);

            // Notificar al usuario solicitante con CC a jrlara@gptservices.com y al asignado
            try {
                Mail::to($ticket->user->email)
                    ->send(new TicketCompletedMail($ticket, $ticket->user));
                
                // También enviar notificación de base de datos
                $ticket->user->notify(new TicketCompletedNotification($ticket));
                
                Log::info('Notificación de ticket completado enviada al usuario #' . $ticket->user_id . ' con CC a jrlara@gptservices.com y al asignado para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de ticket completado: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('almacen.tickets.mine')
                ->with('success', 'Ticket completado exitosamente. Se ha notificado al usuario y se ha creado la encuesta de satisfacción.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al completar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Exportar tickets a Excel
     */
    public function export(Request $request)
    {
        $filters = [
            'status' => $request->status,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        // Si no es admin, solo mostrar tickets asignados al usuario
        if (!Auth::user()->isAdmin()) {
            $filters['assigned_to'] = Auth::id();
        }

        $filename = 'tickets_almacen_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new TicketsExport($filters), $filename);
    }
}
