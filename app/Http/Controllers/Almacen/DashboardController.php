<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
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
        return redirect()->route('almacen.tickets.mine');
    }
}
