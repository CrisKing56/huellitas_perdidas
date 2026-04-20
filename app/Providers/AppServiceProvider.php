<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // 1. Forzar HTTPS por seguridad
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        URL::forceScheme('https');
    }

    // 2. Forzar a Laravel a usar el dominio público de Ngrok para las imágenes
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        URL::forceRootUrl('https://' . $_SERVER['HTTP_X_FORWARDED_HOST']);
    }
}
}
