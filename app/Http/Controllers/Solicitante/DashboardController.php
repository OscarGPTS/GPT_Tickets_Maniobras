<?php

namespace App\Http\Controllers\Solicitante;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Survey;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Constructor - Asegurar que solo solicitantes accedan
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal del solicitante - Redirige a tickets
     */
    public function index()
    {
        return redirect()->route('solicitante.tickets.index');
    }
}
