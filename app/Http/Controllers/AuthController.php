<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'password_hash' => Hash::make($request->password),
            'rol' => 'USUARIO',
            'estado' => 'ACTIVA',
        ]);

        Auth::login($user);

        return redirect()->route('inicio')->with('success', '¡Bienvenido a Huellitas Perdidas!');
    }

    // Mostrar vista de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar login
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt(['correo' => $request->email, 'password' => $request->password])) {

        $request->session()->regenerate();

        // ✅ Redirección por rol
        if (Auth::user()->rol === 'ADMIN') {
            return redirect()->route('admin.usuarios.index')->with('success', '¡Bienvenido Administrador!');
        }

        return redirect()->route('inicio')->with('success', '¡Bienvenido!');
    }

    return back()->withErrors([
        'email' => 'El correo o la contraseña son incorrectos.',
    ]);
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
