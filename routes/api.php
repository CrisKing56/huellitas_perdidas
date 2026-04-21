<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobilePublicacionController;
use App\Http\Controllers\Api\MobileOrganizacionController;
use App\Http\Controllers\Api\MobilePerfilController;
use App\Http\Controllers\Api\MobileSolicitudAdopcionController;

Route::prefix('mobile')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'ok' => true,
            'message' => 'API móvil operativa'
        ]);
    });

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
    Route::get('/adopciones/{id}', [MobilePublicacionController::class, 'detalleAdopcion']);
    Route::get('/mis-adopciones/{idUsuario}', [MobilePublicacionController::class, 'misAdopciones']);
    Route::post('/adopciones/{id}/solicitudes', [MobileSolicitudAdopcionController::class, 'store']);
    Route::get('/adopciones/solicitudes/enviadas/{idUsuario}', [MobileSolicitudAdopcionController::class, 'enviadas']);
    Route::get('/adopciones/solicitudes/recibidas/{idUsuario}', [MobileSolicitudAdopcionController::class, 'recibidas']);
    Route::post('/adopciones/solicitudes/{id}/estado', [MobileSolicitudAdopcionController::class, 'updateEstado']);
    Route::post('/adopciones/{id}/marcar-adoptada', [MobileSolicitudAdopcionController::class, 'marcarAdoptada']);
    Route::post('/adopciones/{id}/volver-en-proceso', [MobileSolicitudAdopcionController::class, 'volverEnProceso']);

    Route::get('/organizaciones', [MobileOrganizacionController::class, 'index']);
    Route::get('/organizaciones/{id}', [MobileOrganizacionController::class, 'show']);

    Route::get('/profile/{idUsuario}', [MobilePerfilController::class, 'show']);
    Route::post('/profile/{idUsuario}/update', [MobilePerfilController::class, 'update']);
    Route::post('/profile/{idUsuario}/settings', [MobilePerfilController::class, 'settings']);

    
});