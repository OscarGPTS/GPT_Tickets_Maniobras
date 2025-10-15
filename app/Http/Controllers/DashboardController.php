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

        // Estadísticas generales
        $totalSurveys = Survey::count();
        $completedSurveys = Survey::whereNotNull('completed_at')->count();
        $averageRating = Survey::whereNotNull('completed_at')->avg('rating') ?? 0;
        $satisfactionCount = Survey::whereNotNull('completed_at')->where('rating', '>=', 4)->count();
        $satisfactionPercentage = $completedSurveys > 0 ? round(($satisfactionCount / $completedSurveys) * 100) : 0;

        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_tickets' => Ticket::count(),
            'pending_tickets' => Ticket::where('status', Ticket::STATUS_PENDIENTE)->count(),
            'in_progress_tickets' => Ticket::where('status', Ticket::STATUS_EN_PROCESO)->count(),
            'completed_tickets' => Ticket::where('status', Ticket::STATUS_FINALIZADO)->count(),
            'total_surveys' => $totalSurveys,
            'pending_surveys' => Survey::whereNull('completed_at')->count(),
            'completed_surveys' => $completedSurveys,
            'average_rating' => round($averageRating, 1),
            'satisfaction_percentage' => $satisfactionPercentage,
        ];

        // Usuarios por rol
        $usersByRole = [
            'admin' => \App\Models\User::role('admin')->count(),
            'almacen' => \App\Models\User::role('almacen')->count(),
            'solicitante' => \App\Models\User::whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'almacen']);
            })->count(),
        ];

        // Usuarios recientes
        $recentUsers = \App\Models\User::with('roles')
            ->latest()
            ->limit(5)
            ->get();

        // Tickets recientes
        $recentTickets = Ticket::with(['user', 'assignedTo'])
            ->latest()
            ->limit(5)
            ->get();

        // Estadísticas por miembro de almacén
        $almacenUsers = \App\Models\User::role('almacen')->get();
        $almacenStats = [];

        foreach ($almacenUsers as $user) {
            $totalTickets = Ticket::where('assigned_to', $user->id)->count();
            $completedTickets = Ticket::where('assigned_to', $user->id)
                ->where('status', Ticket::STATUS_FINALIZADO)
                ->count();
            
            $surveys = Survey::whereHas('ticket', function($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })->whereNotNull('completed_at')->get();

            $totalSurveysForUser = $surveys->count();
            $avgRating = $totalSurveysForUser > 0 ? round($surveys->avg('rating'), 1) : 0;
            $satisfactionCount = $surveys->where('rating', '>=', 4)->count();
            $satisfactionPercentage = $totalSurveysForUser > 0 
                ? round(($satisfactionCount / $totalSurveysForUser) * 100) 
                : 0;

            $almacenStats[] = [
                'name' => $user->name,
                'email' => $user->email,
                'total_tickets' => $totalTickets,
                'completed_tickets' => $completedTickets,
                'total_surveys' => $totalSurveysForUser,
                'average_rating' => $avgRating,
                'satisfaction_percentage' => $satisfactionPercentage,
            ];
        }

        // Todos los tickets con paginación
        $allTickets = Ticket::with(['user', 'assignedTo', 'survey'])
            ->latest()
            ->paginate(15);

        return view('admin.dashboard-new', compact(
            'stats', 
            'usersByRole', 
            'recentUsers', 
            'recentTickets', 
            'almacenStats', 
            'allTickets'
        ));
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
