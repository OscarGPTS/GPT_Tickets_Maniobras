<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Constructor - Asegurar acceso solo para almacén
     */
    public function __construct()
    {
        $this->middleware('auth');
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
    public function index()
    {
        $pendingTickets = Ticket::pendientes()
            ->with(['user', 'solicitudImages'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');

        $myTickets = Ticket::where('assigned_to', Auth::id())
            ->where('status', 'en_proceso')
            ->with(['user', 'images'])
            ->orderBy('assigned_at', 'desc')
            ->paginate(10, ['*'], 'my_page');

        $completedToday = Ticket::where('assigned_to', Auth::id())
            ->where('status', 'finalizado')
            ->whereDate('completed_at', today())
            ->count();

        return view('almacen.dashboard-table', compact('pendingTickets', 'myTickets', 'completedToday'));
    }
}
