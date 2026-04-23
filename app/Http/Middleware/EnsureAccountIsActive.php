<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $usuario = Auth::user();
        $estado = strtoupper((string) ($usuario->estado ?? ''));

        if (in_array($estado, ['SUSPENDIDA', 'ELIMINADA'], true)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'correo' => $estado === 'SUSPENDIDA'
                        ? 'Tu cuenta está suspendida. No puedes acceder a la plataforma.'
                        : 'Tu cuenta ya no está disponible.',
                ]);
        }

        return $next($request);
    }
}