<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobilePublicacionController;
use App\Http\Controllers\Api\MobileOrganizacionController;

Route::prefix('mobile')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/extravios', [MobilePublicacionController::class, 'extravios']);
    Route::get('/adopciones', [MobilePublicacionController::class, 'adopciones']);

    Route::get('/organizaciones', [MobileOrganizacionController::class, 'index']);
});