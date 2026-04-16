<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    // Mostrar vista de registro
    public function showRegister()
    {
        return view('auth.registro');
    }

    // Procesar registro
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|unique:usuarios,correo|max:120',
            'telefono' => 'required|digits:10',
            'password' => 'required|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 120 caracteres.',

            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
            'correo.unique' => 'Ese correo ya está registrado.',
            'correo.max' => 'El correo no debe exceder los 120 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'password_hash' => Hash::make($request->password),
            'rol' => 'USUARIO',
            'estado' => 'ACTIVA',
            'auth_provider' => 'LOCAL',
        ]);

        Auth::login($user);

        return redirect()->route('inicio')->with('success', '¡Bienvenido a Huellitas Perdidas!');
    }

    // Mostrar vista de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar login tradicional
    public function login(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('correo', $request->correo)->first();

        if ($user && ($user->auth_provider ?? 'LOCAL') === 'GOOGLE' && empty($user->password_hash)) {
            return back()->withErrors([
                'correo' => 'Esta cuenta fue registrada con Google. Inicia sesión usando Google.',
            ])->withInput();
        }

        if (Auth::attempt([
            'correo' => $request->correo,
            'password' => $request->password
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();

            $user = Auth::user();
            $rol = strtoupper((string) ($user->rol ?? ''));

            // Validación para cuentas institucionales
            if (in_array($rol, ['VETERINARIA', 'REFUGIO'])) {
                $organizacion = DB::table('organizaciones')
                    ->where('usuario_dueno_id', $user->id_usuario)
                    ->latest('id_organizacion')
                    ->first();

                if (!$organizacion) {
                    Auth::logout();

                    return back()->withErrors([
                        'correo' => 'Tu cuenta no tiene una organización vinculada.',
                    ])->withInput();
                }

                if ($organizacion->estado_revision === 'PENDIENTE') {
                    Auth::logout();

                    return back()->withErrors([
                        'correo' => 'Tu solicitud aún está pendiente de revisión por el administrador.',
                    ])->withInput();
                }

                if ($organizacion->estado_revision === 'RECHAZADA') {
                    Auth::logout();

                    return back()->withErrors([
                        'correo' => 'Tu solicitud fue rechazada. Contacta al administrador o vuelve a intentarlo más adelante.',
                    ])->withInput();
                }

                if ($organizacion->tipo === 'VETERINARIA' && Route::has('veterinaria.dashboard')) {
                    return redirect()->route('veterinaria.dashboard')
                        ->with('success', '¡Bienvenido al panel de veterinaria!');
                }

                if ($organizacion->tipo === 'REFUGIO' && Route::has('refugio.dashboard')) {
                    return redirect()->route('refugio.dashboard')
                        ->with('success', '¡Bienvenido al panel de refugio!');
                }
            }

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

            // Usuario normal
            return redirect()->route('inicio')->with('success', '¡Bienvenido!');
        }

        return back()->withErrors([
            'correo' => 'El correo o la contraseña son incorrectos.',
        ])->withInput();
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}