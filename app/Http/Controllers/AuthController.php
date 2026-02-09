<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar la vista de registro
    public function showRegister()
    {
        return view('auth.registro');
    }

    // Procesar el formulario
    public function register(Request $request)
    {
        // 1. VALIDACIÓN
        // Verificamos que los datos sean correctos antes de guardar
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            // OJO: 'unique:usuarios,correo' verifica en tu tabla 'usuarios' columna 'correo'
            'correo' => 'required|email|unique:usuarios,correo|max:120', 
            'telefono' => 'required|digits:10',
            'password' => 'required|min:8|confirmed', // 'confirmed' busca el campo password_confirmation
        ]);

        // 2. CREACIÓN DEL USUARIO
        // Mapeamos los inputs del formulario a las columnas de tu BD
        $user = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'password_hash' => Hash::make($request->password), // Encriptamos la contraseña
            'rol' => 'USUARIO', // Rol por defecto
            'estado' => 'ACTIVA',
        ]);

        // 3. MANEJO DE SESIÓN
        // Aquí es donde Laravel "recuerda" al usuario.
        // Auth::login crea la cookie de sesión automáticamente.
        Auth::login($user);

        // 4. REDIRECCIÓN
        // Lo mandamos a la página principal o panel
        return redirect()->route('inicio')->with('success', '¡Bienvenido a Huellitas Perdidas!');
    }
    
    // Método para Cerrar Sesión (Logout)
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showLogin()
    {
        return view('auth.login'); // Asegúrate que el archivo esté en resources/views/auth/login.blade.php
    }

    // 2. Procesar el inicio de sesión
    public function login(Request $request)
    {
        // Validamos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentamos loguear
        // Laravel buscará el correo en la columna 'email' por defecto. 
        // Como tu columna es 'correo', hay que especificarlo así:
        if (Auth::attempt(['correo' => $request->email, 'password' => $request->password])) {
            
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', '¡Bienvenido!');
        }

        // Si falla
        return back()->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ]);
    }
}