<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'correo' => 'No se pudo iniciar sesión con Google. Intenta nuevamente.',
            ]);
        }

        $correo = $googleUser->getEmail();

        if (!$correo) {
            return redirect()->route('login')->withErrors([
                'correo' => 'Google no devolvió un correo válido.',
            ]);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('correo', $correo)
            ->first();

        if ($user) {
            if (($user->estado ?? 'ACTIVA') !== 'ACTIVA') {
                return redirect()->route('login')->withErrors([
                    'correo' => 'Tu cuenta no está activa.',
                ]);
            }

            $user->google_id = $googleUser->getId();
            $user->auth_provider = 'GOOGLE';
            $user->google_avatar = $googleUser->getAvatar();
            $user->email_verified_at = $user->email_verified_at ?? now();

            if (empty($user->nombre) && $googleUser->getName()) {
                $user->nombre = $googleUser->getName();
            }

            $user->save();
        } else {
            $user = User::create([
                'nombre' => $googleUser->getName() ?: 'Usuario Google',
                'correo' => $correo,
                'telefono' => null,
                'password_hash' => null,
                'rol' => 'USUARIO',
                'estado' => 'ACTIVA',
                'google_id' => $googleUser->getId(),
                'auth_provider' => 'GOOGLE',
                'google_avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user);
    }

    private function redirectAfterLogin(User $user)
    {
        $rol = strtoupper((string) ($user->rol ?? ''));

        // Administrador
        if (in_array($rol, ['ADMIN', 'ADMIN_GENERAL'])) {
            if (Route::has('admin.dashboard')) {
                return redirect()->route('admin.dashboard')
                    ->with('success', '¡Bienvenido Administrador!');
            }

            if (Route::has('admin.usuarios.index')) {
                return redirect()->route('admin.usuarios.index')
                    ->with('success', '¡Bienvenido Administrador!');
            }
        }

        // Veterinaria o refugio
        if (in_array($rol, ['VETERINARIA', 'REFUGIO'])) {
            $organizacion = DB::table('organizaciones')
                ->where('usuario_dueno_id', $user->id_usuario)
                ->latest('id_organizacion')
                ->first();

            if (!$organizacion) {
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'correo' => 'Tu cuenta institucional no tiene una organización vinculada.',
                ]);
            }

            if (($organizacion->estado_revision ?? null) === 'PENDIENTE') {
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'correo' => 'Tu solicitud aún está pendiente de revisión por el administrador.',
                ]);
            }

            if (($organizacion->estado_revision ?? null) === 'RECHAZADA') {
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'correo' => 'Tu solicitud fue rechazada. Contacta al administrador.',
                ]);
            }

            if (($organizacion->tipo ?? null) === 'VETERINARIA' && Route::has('veterinaria.dashboard')) {
                return redirect()->route('veterinaria.dashboard')
                    ->with('success', '¡Bienvenido al panel de veterinaria!');
            }

            if (($organizacion->tipo ?? null) === 'REFUGIO' && Route::has('refugio.dashboard')) {
                return redirect()->route('refugio.dashboard')
                    ->with('success', '¡Bienvenido al panel de refugio!');
            }
        }

        // Usuario normal
        // CAMBIO: ya no lo mandamos a /perfil porque tu perfil
        // necesita más datos y ahorita puede fallar.
        if (empty($user->telefono)) {
            return redirect()->route('inicio')
                ->with('success', '¡Bienvenido!')
                ->with('warning', 'Tu cuenta se creó con Google. Después puedes completar tu perfil.');
        }

        return redirect()->route('inicio')->with('success', '¡Bienvenido!');
    }
}