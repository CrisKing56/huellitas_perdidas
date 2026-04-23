<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.olvidar-contrasena');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email', 'exists:usuarios,correo'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
            'correo.exists' => 'No encontramos una cuenta registrada con ese correo.',
        ]);

        $status = Password::broker()->sendResetLink([
            'correo' => $request->correo,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Te enviamos un enlace para restablecer tu contraseña. Revisa tu correo y también la bandeja de spam.');
        }

        return back()->withErrors([
            'correo' => 'No se pudo enviar el enlace de recuperación. Intenta nuevamente.',
        ])->withInput();
    }

    public function showResetForm(Request $request, string $token)
    {
        $correo = $request->query('correo') ?? $request->query('email');

        return view('auth.restablecer-contrasena', [
            'token' => $token,
            'correo' => $correo,
        ]);
    }

    public function reset(Request $request)
    {
        $correo = $request->input('correo') ?: $request->input('email');

        $request->merge([
            'correo' => $correo,
        ]);

        $request->validate([
            'token' => ['required'],
            'correo' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], [
            'token.required' => 'El token de recuperación es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $status = Password::broker()->reset(
            [
                'correo' => $request->correo,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function (User $user, string $password) {
                $user->password_hash = Hash::make($password);
                $user->remember_token = Str::random(60);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Tu contraseña fue actualizada correctamente. Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('correo'))
            ->withErrors([
                'correo' => 'El enlace de recuperación es inválido o ya expiró.',
            ]);
    }
}