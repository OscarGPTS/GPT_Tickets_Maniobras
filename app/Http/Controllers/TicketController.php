<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\User;
use App\Models\Survey;
use App\Notifications\TicketCreated;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the user's tickets.
     */
    public function index()
    {
        $tickets = Auth::user()->tickets()
            ->with(['assignedTo', 'images', 'survey'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        // Verificar si el usuario puede crear un nuevo ticket
        if (!Auth::user()->canCreateTicket()) {
            return redirect()->route('tickets.index')
                ->with('error', 'Debes completar las encuestas pendientes antes de crear un nuevo ticket.');
        }

        return view('tickets.create');
    }

    /**
     * Store a newly created ticket in storage.
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
            $almacenUsers = User::where('role', 'almacen')->get();
            Notification::send($almacenUsers, new TicketCreated($ticket));

            DB::commit();

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket creado exitosamente. Se ha notificado al equipo de almacén.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        // Verificar que el usuario puede ver este ticket
        if ($ticket->user_id !== Auth::id() && !Auth::user()->isAlmacen() && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        $ticket->load(['user', 'assignedTo', 'images', 'survey']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Ticket $ticket)
    {
        // Solo el propietario puede editar si el ticket está pendiente
        if ($ticket->user_id !== Auth::id() || !$ticket->isPendiente()) {
            abort(403, 'No puedes editar este ticket.');
        }

        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified ticket in storage.
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

            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified ticket from storage.
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

            return redirect()->route('tickets.index')
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
}
