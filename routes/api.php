<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobilePublicacionController;
use App\Http\Controllers\Api\MobileOrganizacionController;

Route::prefix('mobile')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/especies', [MobilePublicacionController::class, 'especies']);
    Route::get('/razas', [MobilePublicacionController::class, 'razas']);

    Route::get('/extravios', [MobilePublicacionController::class, 'extravios']);
    Route::get('/mis-extravios/{idUsuario}', [MobilePublicacionController::class, 'misExtravios']);
    Route::get('/extravios/{id}', [MobilePublicacionController::class, 'detalleExtravio']);
    Route::post('/extravios', [MobilePublicacionController::class, 'storeExtravio']);
    Route::post('/extravios/{id}/update', [MobilePublicacionController::class, 'updateExtravio']);
    Route::post('/extravios/{id}/delete', [MobilePublicacionController::class, 'deleteExtravio']);

    Route::get('/adopciones', [MobilePublicacionController::class, 'adopciones']);

    Route::get('/organizaciones', [MobileOrganizacionController::class, 'index']);
    Route::get('/organizaciones/{id}', [MobileOrganizacionController::class, 'show']);
});