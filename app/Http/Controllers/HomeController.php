<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Mostrar el dashboard principal del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirigir según el rol del usuario (prioridad: admin > almacen > solicitante)
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAlmacen()) {
            return redirect()->route('almacen.dashboard');
        } else {
            return $this->userDashboard();
        }
    }

    /**
     * Dashboard para usuarios normales
     */
    private function userDashboard()
    {
        $user = Auth::user();
        
        // Tickets activos (pendientes + en proceso)
        $activeTickets = Ticket::where('user_id', $user->id)
            ->with(['assignedTo', 'images'])
            ->whereIn('status', [Ticket::STATUS_PENDIENTE, Ticket::STATUS_EN_PROCESO])
            ->latest()
            ->limit(10)
            ->get();

        // Tickets completados recientes
        $completedTickets = Ticket::where('user_id', $user->id)
            ->with(['assignedTo', 'images', 'survey'])
            ->where('status', Ticket::STATUS_FINALIZADO)
            ->latest('completed_at')
            ->limit(5)
            ->get();
        
        // Verificar si hay encuestas pendientes
        $pendingSurveys = Survey::whereHas('ticket', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereNull('completed_at')
        ->with('ticket.assignedTo')
        ->get();
        
        // Estadísticas completas
        $stats = [
            'total_tickets' => Ticket::where('user_id', $user->id)->count(),
            'pending_tickets' => Ticket::where('user_id', $user->id)->where('status', Ticket::STATUS_PENDIENTE)->count(),
            'in_progress_tickets' => Ticket::where('user_id', $user->id)->where('status', Ticket::STATUS_EN_PROCESO)->count(),
            'completed_tickets' => Ticket::where('user_id', $user->id)->where('status', Ticket::STATUS_FINALIZADO)->count(),
        ];
        
        // Verificar si puede crear un nuevo ticket (solo si no tiene encuestas pendientes)
        $canCreateTicket = $user->canCreateTicket();
        
        return view('dashboard-new', compact('activeTickets', 'completedTickets', 'pendingSurveys', 'stats', 'canCreateTicket'));
    }
}
