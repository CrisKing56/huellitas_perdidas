<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- ¡IMPORTANTE! No olvides esto

class LoginController extends Controller
{
    // 1. Mostrar la VISTA del formulario
    public function showLoginForm() {
        return view('auth.login'); // Asegúrate que tu archivo blade esté en resources/views/auth/login.blade.php
    }
    

    // 2. Procesar los datos (La lógica del CONTROLADOR)
    public function login(Request $request) {
        // A. Validar que no manden campos vacíos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // B. Intentar loguear (Aquí el controlador habla con el MODELO User automáticamente)
        if (Auth::attempt($credentials)) {
            
            // C. Si entra, regeneramos la sesión por seguridad
            $request->session()->regenerate();

            // D. Redirigir al usuario a su panel (CAMBIA 'dashboard' por tu ruta principal)
            return redirect()->intended('dashboard');
        }

        // E. Si falla, devolverlo al formulario con un error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    // 3. Cerrar Sesión
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}