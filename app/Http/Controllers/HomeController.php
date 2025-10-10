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
        
        // Obtener tickets del usuario
        $myTickets = Ticket::where('user_id', $user->id)
            ->with(['assignedTo', 'survey'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Verificar si hay encuestas pendientes
        $pendingSurveys = Survey::whereHas('ticket', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('completed_at', null)->get();
        
        // Estadísticas básicas
        $stats = [
            'total_tickets' => Ticket::where('user_id', $user->id)->count(),
            'pending_tickets' => Ticket::where('user_id', $user->id)->where('status', 'pendiente')->count(),
            'in_progress_tickets' => Ticket::where('user_id', $user->id)->where('status', 'en_proceso')->count(),
            'completed_tickets' => Ticket::where('user_id', $user->id)->where('status', 'finalizado')->count(),
            'pending_surveys' => $pendingSurveys->count(),
        ];
        
        // Verificar si puede crear un nuevo ticket
        $canCreateTicket = $user->canCreateTicket();
        
        return view('dashboard', compact('myTickets', 'pendingSurveys', 'stats', 'canCreateTicket'));
    }
}
