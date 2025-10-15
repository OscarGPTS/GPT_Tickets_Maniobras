<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\Survey;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Ver detalle de un ticket específico (vista unificada)
     */
    public function showTicket(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'images', 'survey']);

        return view('almacen.ticket', compact('ticket'));
    }

    /**
     * Asignar un ticket a sí mismo
     */
    public function assignTicket(Request $request, Ticket $ticket)
    {
        if (!$ticket->isPendiente()) {
            return back()->withErrors(['error' => 'Este ticket ya ha sido asignado.']);
        }

        DB::beginTransaction();
        try {
            $ticket->assignTo(Auth::user());

            // Notificar al usuario solicitante
            try {
                $ticket->user->notify(new TicketAssignedNotification($ticket));
                Log::info('Notificación de asignación enviada al usuario #' . $ticket->user_id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de ticket asignado: ' . $e->getMessage());
                // No detenemos el proceso si falla la notificación
            }

            DB::commit();

            return redirect()->route('almacen.tickets.mine')
                ->with('success', 'Ticket asignado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al asignar el ticket: ' . $e->getMessage()]);
        }
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
            'progress_comment' => 'required|string|max:1000',
            'progress_images' => 'nullable|array|max:3',
            'progress_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Subir imágenes de progreso si hay
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

            // Actualizar estado a en_progreso si está pendiente
            if ($ticket->status === 'pendiente') {
                $ticket->update(['status' => 'en_progreso']);
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
            try {
                $ticket->user->notify(new TicketCompletedNotification($ticket));
                Log::info('Notificación de ticket completado enviada al usuario #' . $ticket->user_id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de ticket completado: ' . $e->getMessage());
                // No detenemos el proceso si falla la notificación
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
