<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class GoogleController extends Controller
{
     public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }


    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $allowedDomain = 'gptservices.com';
        $domain = substr(strrchr($googleUser->getEmail(), "@"), 1);

        if ($domain !== $allowedDomain) {
            return redirect('/login')->withErrors([
                'email' => 'Solo usuarios de la organización pueden acceder.',
            ]);
        }

        $user = User::where('email', $googleUser->getEmail())->first();
        if ($user) {
            $user->update([
                'google_id'    => Socialite::driver('google')->user()->getId(),
                'google_image' => Socialite::driver('google')->user()->getAvatar(),
                'name'         => $googleUser->getName(), 
            ]);
        } else {
            $user = User::create([
                'name'         => $googleUser->getName(),
                'email'        => $googleUser->getEmail(),
                'password'     => bcrypt(str()->random(16)),
                'google_id'    => $googleUser->getId(),
                'google_image' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user);

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/'); 
    }
}
