<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\Survey;
use App\Models\User;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAlmacen() && !Auth::user()->isAdmin()) {
                abort(403, 'No tienes acceso al panel de almacén.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard del almacén
     */
    public function dashboard()
    {
        $pendingTickets = Ticket::pendientes()
            ->with(['user', 'solicitudImages'])
            ->orderBy('created_at', 'desc')
            ->get();

        $myTickets = Ticket::where('assigned_to', Auth::id())
            ->enProceso()
            ->with(['user', 'images'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        $completedToday = Ticket::where('assigned_to', Auth::id())
            ->whereDate('completed_at', today())
            ->count();

        return view('almacen.dashboard', compact('pendingTickets', 'myTickets', 'completedToday'));
    }

    /**
     * Ver todos los tickets pendientes
     */
    public function pendingTickets()
    {
        $tickets = Ticket::pendientes()
            ->with(['user', 'solicitudImages'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('almacen.pending-tickets', compact('tickets'));
    }

    /**
     * Ver mis tickets asignados
     */
    public function myTickets()
    {
        $tickets = Ticket::where('assigned_to', Auth::id())
            ->with(['user', 'images'])
            ->orderBy('assigned_at', 'desc')
            ->paginate(15);

        return view('almacen.my-tickets', compact('tickets'));
    }

    /**
     * Ver detalle de un ticket específico
     */
    public function showTicket(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'images', 'survey']);

        return view('almacen.ticket-detail', compact('ticket'));
    }

    /**
     * Asignar un ticket a sí mismo
     */
    public function assignTicket(Request $request, Ticket $ticket)
    {
        if (!$ticket->isPendiente()) {
            return back()->withErrors(['error' => 'Este ticket ya ha sido asignado.']);
        }

        try {
            $ticket->assignTo(Auth::user());

            // Notificar al usuario solicitante
            $ticket->user->notify(new TicketAssigned($ticket));

            return redirect()->route('almacen.tickets.mine')
                ->with('success', 'Ticket asignado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al asignar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario para completar ticket
     */
    public function completeForm(Ticket $ticket)
    {
        // Verificar que es el ticket asignado al usuario actual
        if ($ticket->assigned_to !== Auth::id() || !$ticket->isEnProceso()) {
            abort(403, 'No puedes completar este ticket.');
        }

        return view('almacen.complete-ticket', compact('ticket'));
    }

    /**
     * Agregar progreso a un ticket
     */
    public function addProgress(Request $request, Ticket $ticket)
    {
        // Verificar que es el ticket asignado al usuario actual
        if ($ticket->assigned_to !== Auth::id()) {
            abort(403, 'No puedes agregar progreso a este ticket.');
        }

        $request->validate([
            'progress_comment' => 'nullable|string|max:1000',
            'ticket_status' => 'required|in:en_progreso,esperando_recursos,esperando_aprobacion,completado',
            'progress_images' => 'nullable|array|max:3',
            'progress_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Si el estado es completado, redirigir al formulario de completar
            if ($request->ticket_status === 'completado') {
                return redirect()->route('almacen.tickets.complete.form', $ticket)
                    ->with('info', 'Para marcar como completado, debes proporcionar evidencia del trabajo realizado.');
            }

            // Actualizar estado del ticket si cambió
            if ($ticket->status !== $request->ticket_status) {
                $ticket->update(['status' => $request->ticket_status]);
            }

            // Agregar comentario de progreso si existe
            if ($request->filled('progress_comment')) {
                // Crear un comentario en la tabla de imágenes con tipo 'progreso'
                TicketImage::create([
                    'ticket_id' => $ticket->id,
                    'uploaded_by' => Auth::id(),
                    'file_path' => null, // Sin archivo, solo comentario
                    'original_name' => 'Comentario de Progreso',
                    'mime_type' => 'text/plain',
                    'file_size' => 0,
                    'type' => 'progreso',
                    'description' => $request->progress_comment,
                    'created_at' => now(),
                ]);
            }

            // Procesar imágenes de progreso si las hay
            if ($request->hasFile('progress_images')) {
                foreach ($request->file('progress_images') as $image) {
                    $path = $image->store('tickets/' . $ticket->id . '/progress', 'public');
                    
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'uploaded_by' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'type' => 'progreso',
                    ]);
                }
            }

            // Notificar al usuario solicitante sobre el progreso
            $ticket->user->notify(new \App\Notifications\TicketProgress($ticket, $request->progress_comment));

            DB::commit();

            return redirect()->route('almacen.tickets.show', $ticket)
                ->with('success', 'Progreso agregado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al agregar progreso: ' . $e->getMessage()]);
        }
    }

    /**
     * Completar un ticket
     */
    public function completeTicket(Request $request, Ticket $ticket)
    {
        // Verificar que es el ticket asignado al usuario actual
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

            // Procesar imágenes de evidencia si las hay
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
            $survey = Survey::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'rating' => 0, // Se completará después
                'comments' => null,
            ]);

            // Notificar al usuario solicitante
            $ticket->user->notify(new TicketCompleted($ticket));

            DB::commit();

            return redirect()->route('almacen.tickets.mine')
                ->with('success', 'Ticket completado exitosamente. Se ha notificado al usuario y se ha creado la encuesta de satisfacción.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al completar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Estadísticas del almacén
     */
    public function statistics()
    {
        $stats = [
            'total_pending' => Ticket::pendientes()->count(),
            'total_in_progress' => Ticket::enProceso()->count(),
            'total_completed_today' => Ticket::whereDate('completed_at', today())->count(),
            'total_completed_this_week' => Ticket::whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_completed_this_month' => Ticket::whereMonth('completed_at', now()->month)->count(),
            'my_completed_today' => Ticket::where('assigned_to', Auth::id())->whereDate('completed_at', today())->count(),
            'my_total_completed' => Ticket::where('assigned_to', Auth::id())->finalizados()->count(),
            'average_rating' => Survey::whereNotNull('completed_at')->avg('rating'),
        ];

        $recentCompletedTickets = Ticket::finalizados()
            ->with(['user', 'assignedTo'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        return view('almacen.statistics', compact('stats', 'recentCompletedTickets'));
    }
}
