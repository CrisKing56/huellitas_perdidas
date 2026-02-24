<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();

            // Buscamos por 'correo' (el nombre real en tu tabla)
            $user = User::where('correo', $googleUser->email)->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->id]);
            } else {
                $user = User::create([
                    'nombre'    => $googleUser->name,
                    'correo'    => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'rol'       => 'USUARIO', 
                    'estado'    => 'ACTIVA',  
                    'password_hash' => null, 
                ]);
            }

            Auth::login($user);
            return redirect('/')->with('success', '¡Bienvenido a Huellitas Perdidas!');

        } catch (\Exception $e) {
            \Log::error("Error detallado: " . $e->getMessage());
            return redirect('/login')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
}