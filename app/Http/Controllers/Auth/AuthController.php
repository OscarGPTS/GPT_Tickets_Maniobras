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
            $email = $auth0User->getEmail();

            if (!str_ends_with(strtolower($email), '@gptservices.com')) {
                return redirect()->route('login')
                    ->withErrors(['error' => 'Solo se permiten cuentas del dominio @gptservices.com']);
            }
            
            $user = User::where('provider_id', $auth0User->getId())
                ->orWhere('email', $email)
                ->first();

            $isNewUser = !$user;

            if (!$user) {
                $user = new User();
                $user->email = $email;
            }

            $user->fill([
                'name' => $auth0User->getName(),
                'email' => $email,
                'provider_id' => $auth0User->getId(),
                'avatar' => $auth0User->getAvatar(),
                'provider' => 'auth0',
                'last_login_at' => now(),
            ]);

            $user->save();

            if ($isNewUser || $user->roles()->count() === 0) {
                $user->assignRole('solicitante');
            }

            Auth::login($user);

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
        return redirect()->route('home');
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
