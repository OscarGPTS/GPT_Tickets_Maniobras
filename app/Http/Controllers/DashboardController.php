<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard principal del usuario
     */
    public function index()
    {
        $user = Auth::user();

        // Redirigir según el rol del usuario
        if ($user->isAlmacen()) {
            return redirect()->route('almacen.dashboard');
        }

        // Dashboard para usuarios normales
        // Tickets activos (pendientes + en proceso)
        $activeTickets = $user->tickets()
            ->with(['assignedTo', 'images'])
            ->whereIn('status', [Ticket::STATUS_PENDIENTE, Ticket::STATUS_EN_PROCESO])
            ->latest()
            ->limit(10)
            ->get();

        // Tickets completados recientes
        $completedTickets = $user->tickets()
            ->with(['assignedTo', 'images', 'survey'])
            ->where('status', Ticket::STATUS_FINALIZADO)
            ->latest('completed_at')
            ->limit(5)
            ->get();

        // Encuestas pendientes
        $pendingSurveys = $user->surveys()
            ->pendientes()
            ->with(['ticket.assignedTo'])
            ->get();

        // Estadísticas completas
        $stats = [
            'total_tickets' => $user->tickets()->count(),
            'pending_tickets' => $user->tickets()->pendientes()->count(),
            'in_progress_tickets' => $user->tickets()->enProceso()->count(),
            'completed_tickets' => $user->tickets()->finalizados()->count(),
        ];

        // Verificar si puede crear un nuevo ticket (solo si no tiene encuestas pendientes)
        $canCreateTicket = $user->canCreateTicket();

        // Redirigir a la vista mejorada
        return view('dashboard-new', compact('activeTickets', 'completedTickets', 'pendingSurveys', 'stats', 'canCreateTicket'));
    }

    /**
     * Dashboard específico para administradores
     */
    public function adminDashboard()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'No tienes acceso al panel de administración.');
        }

        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_tickets' => Ticket::count(),
            'pending_tickets' => Ticket::pendientes()->count(),
            'in_progress_tickets' => Ticket::enProceso()->count(),
            'completed_tickets' => Ticket::finalizados()->count(),
            'pending_surveys' => Survey::pendientes()->count(),
            'completed_surveys' => Survey::completadas()->count(),
            'average_rating' => Survey::whereNotNull('completed_at')->avg('rating'),
        ];

        $recentTickets = Ticket::with(['user', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentSurveys = Survey::with(['user', 'ticket'])
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTickets', 'recentSurveys'));
    }

    /**
     * Notificaciones del usuario
     */
    public function notifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marcar notificación como leída
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
        }

        // Si hay una URL de acción, redirigir ahí
        if ($request->has('action_url')) {
            return redirect($request->action_url);
        }

        return back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }
}
