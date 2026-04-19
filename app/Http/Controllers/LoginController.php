<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'correo' => 'Las credenciales no coinciden con nuestros registros.',
            ])->onlyInput('correo');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->estado !== 'ACTIVA') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($user->estado === 'SUSPENDIDA') {
                return back()->withErrors([
                    'correo' => 'Tu cuenta fue suspendida temporalmente por el administrador. Si consideras que esto es un error, contacta al administrador.',
                ])->onlyInput('correo');
            }

            if ($user->estado === 'ELIMINADA') {
                return back()->withErrors([
                    'correo' => 'Tu cuenta ya no está disponible para iniciar sesión. Contacta al administrador si necesitas más información.',
                ])->onlyInput('correo');
            }

            return back()->withErrors([
                'correo' => 'Tu cuenta no se encuentra activa. Contacta al administrador.',
            ])->onlyInput('correo');
        }

        if (in_array($user->rol, ['VETERINARIA', 'REFUGIO'])) {
            $tipo = strtolower($user->rol);

            if (is_null($user->email_verified_at)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('registro.organizacion.enviado')->with([
                    'correo_verificacion' => $user->correo,
                    'tipo_registro' => $tipo,
                    'error' => 'Debes verificar tu correo electrónico antes de iniciar sesión.',
                ]);
            }

            $organizacion = DB::table('organizaciones')
                ->where('usuario_dueno_id', $user->id_usuario)
                ->where('tipo', $user->rol)
                ->first();

            if (!$organizacion) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'correo' => 'No se encontró la organización vinculada a esta cuenta.',
                ])->onlyInput('correo');
            }

            if ($organizacion->estado_revision === 'PENDIENTE') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('registro.organizacion.pendiente')->with([
                    'correo_verificacion' => $user->correo,
                    'tipo_registro' => $tipo,
                ]);
            }

            if ($organizacion->estado_revision === 'RECHAZADA') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('registro.organizacion.rechazada')->with([
                    'correo_verificacion' => $user->correo,
                    'tipo_registro' => $tipo,
                    'motivo_rechazo' => $organizacion->motivo_rechazo,
                ]);
            }
        }

        if ($user->rol === 'ADMIN') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->rol === 'VETERINARIA') {
            return redirect()->intended(route('veterinaria.dashboard'));
        }

        if ($user->rol === 'REFUGIO') {
            return redirect()->intended(route('refugio.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}