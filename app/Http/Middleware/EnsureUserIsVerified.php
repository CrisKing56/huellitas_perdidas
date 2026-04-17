<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Las cuentas de Google ya entran verificadas
        if (($user->auth_provider ?? 'LOCAL') !== 'LOCAL') {
            return $next($request);
        }

        // Si ya verificó, sigue normal
        if (!is_null($user->email_verified_at)) {
            return $next($request);
        }

        // Rutas permitidas mientras no verifica
        if ($request->routeIs([
            'verification.notice',
            'verification.verify',
            'verification.send',
            'logout',
        ])) {
            return $next($request);
        }

        return redirect()
            ->route('verification.notice')
            ->with('warning', 'Debes verificar tu correo antes de continuar.');
    }
}