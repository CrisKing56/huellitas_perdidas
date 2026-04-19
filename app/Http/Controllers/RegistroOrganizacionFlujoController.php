<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegistroOrganizacionFlujoController extends Controller
{
    public function enviado(Request $request)
    {
        $correo = session('correo_verificacion');
        $tipo = session('tipo_registro');

        return view('auth.registro-organizacion-enviado', compact('correo', 'tipo'));
    }

    public function verificado(Request $request)
    {
        $correo = session('correo_verificacion');
        $tipo = session('tipo_registro');

        return view('auth.registro-organizacion-verificado', compact('correo', 'tipo'));
    }

    public function pendiente(Request $request)
    {
        $correo = session('correo_verificacion');
        $tipo = session('tipo_registro');

        return view('auth.registro-organizacion-pendiente', compact('correo', 'tipo'));
    }

    public function rechazada(Request $request)
    {
        $correo = session('correo_verificacion');
        $tipo = session('tipo_registro');

        return view('auth.registro-organizacion-rechazada', compact('correo', 'tipo'));
    }

    public function reenviar(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ]);

        $user = User::where('correo', $request->correo)
            ->whereIn('rol', ['VETERINARIA', 'REFUGIO'])
            ->first();

        if (!$user) {
            return back()->withErrors([
                'correo' => 'No encontramos una solicitud institucional con ese correo.',
            ]);
        }

        if (!is_null($user->email_verified_at)) {
            return back()->with('success', 'Ese correo ya fue verificado. Ahora solo debes esperar la revisión del administrador.');
        }

        event(new Registered($user));

        return back()->with('success', 'Te enviamos nuevamente el correo de verificación.');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Enlace de verificación inválido.');
        }

        if (is_null($user->email_verified_at)) {
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        $tipo = strtolower($user->rol);

        return redirect()->route('registro.organizacion.verificado')->with([
            'correo_verificacion' => $user->correo,
            'tipo_registro' => $tipo,
            'success' => 'Correo verificado correctamente.',
        ]);
    }
}