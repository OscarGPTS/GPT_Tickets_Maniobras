<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirigir al proveedor Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Manejar el callback de Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar usuario existente
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            $isNewUser = !$existingUser;
            
            // Buscar o crear usuario
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'provider_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'provider' => 'google',
                    'last_login_at' => now(),
                ]
            );

            // Asignar rol usando Spatie Permission si es un usuario nuevo o no tiene roles
            if ($isNewUser || $user->roles()->count() === 0) {
                $user->assignRole('solicitante');
            }

            // Autenticar usuario
            Auth::login($user);

            // Redirigir según el rol
            return $this->redirectByRole($user);

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Error en la autenticación con Google: ' . $e->getMessage()]);
        }
    }

    /**
     * Redirigir según el rol del usuario
     */
    private function redirectByRole(User $user)
    {
        // Redirigir a la ruta raíz que maneja la lógica de roles
        return redirect()->route('home');
    }
}