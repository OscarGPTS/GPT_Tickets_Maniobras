<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirigir al proveedor Auth0
     */
    public function redirectToAuth0()
    {
        return Socialite::driver('auth0')->redirect();
    }

    /**
     * Manejar el callback de Auth0
     */
    public function handleAuth0Callback()
    {
        try {
            $auth0User = Socialite::driver('auth0')->user();
            
            // Buscar o crear usuario
            $user = User::updateOrCreate(
                ['provider_id' => $auth0User->getId()],
                [
                    'name' => $auth0User->getName(),
                    'email' => $auth0User->getEmail(),
                    'provider_id' => $auth0User->getId(),
                    'avatar' => $auth0User->getAvatar(),
                    'provider' => 'auth0',
                    'last_login_at' => now(),
                ]
            );

            // Autenticar usuario
            Auth::login($user);

            // Redirigir según el rol
            return $this->redirectByRole($user);

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Error en la autenticación: ' . $e->getMessage()]);
        }
    }

    /**
     * Redirigir según el rol del usuario
     */
    private function redirectByRole(User $user)
    {
        return match($user->role) {
            'almacen' => redirect()->route('almacen.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    /**
     * Mostrar página de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Verificar el estado de autenticación
     */
    public function check()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }
}
