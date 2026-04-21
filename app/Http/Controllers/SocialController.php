<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    // 1. Manda al usuario a la pantalla de Facebook
    public function redirectFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // 2. Recibe al usuario de regreso desde Facebook
    public function callbackFacebook()
{
    try {
        $facebookUser = Socialite::driver('facebook')->user();
        
        // EL TRUCO: Si no hay correo, armamos uno falso pero único usando su ID
        $correoSeguro = $facebookUser->email ?? $facebookUser->id . '@huellitas-facebook.com';
        
        $user = User::where('facebook_id', $facebookUser->id)
                    ->orWhere('correo', $correoSeguro)
                    ->first();

        if ($user) {
            // Si la cuenta ya existe, actualizamos su ID de Facebook
            $user->update(['facebook_id' => $facebookUser->id]);
        } else {
            // Si no existe, creamos el registro nuevo
            $user = User::create([
                'nombre' => $facebookUser->name,
                'correo' => $correoSeguro, 
                'facebook_id' => $facebookUser->id,
                'password_hash' => null,   
                'auth_provider' => 'facebook' 
            ]);
        }

        // UN SOLO LOGIN PARA AMBOS CASOS (Con el "true" activado)
        Auth::login($user, true);

        // Lo mandamos a la página principal
        return redirect('/');

    } catch (\Exception $e) {
        // Modo Detective: Si vuelve a fallar, nos dirá el nuevo error
        dd($e->getMessage()); 
    }
}
}