<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Redirigir al dashboard correspondiente según el rol del usuario
     * 
     * Prioridad de redirección:
     * 1. Admin - Panel administrativo completo
     * 2. Almacén - Panel de gestión de tickets
     * 3. Solicitante - Panel de seguimiento de tickets
     */
    public function index()
    {
        $user = Auth::user();
        
        // Prioridad: admin > almacén > solicitante
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->isAlmacen()) {
            return redirect()->route('almacen.dashboard');
        }
        
        // Por defecto, redirigir al dashboard de solicitante
        return redirect()->route('solicitante.dashboard');
    }
}
