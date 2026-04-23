<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobilePublicacionController;
use App\Http\Controllers\Api\MobileOrganizacionController;
use App\Http\Controllers\Api\MobilePerfilController;
use App\Http\Controllers\Api\MobileSolicitudAdopcionController;
use App\Http\Controllers\Api\MobileAdopcionController;
use App\Http\Controllers\Api\MobileNotificacionController;

Route::prefix('mobile')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'ok' => true,
            'message' => 'API móvil operativa'
        ]);
    });

    // =====================
    // AUTH
    // =====================
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/google', [AuthController::class, 'loginGoogle']);
    Route::post('/register', [AuthController::class, 'register']);

    // =====================
    // CATÁLOGOS
    // =====================
    Route::get('/especies', [MobilePublicacionController::class, 'especies']);
    Route::get('/razas', [MobilePublicacionController::class, 'razas']);

    // =====================
    // EXTRAVÍOS
    // =====================
    Route::get('/extravios', [MobilePublicacionController::class, 'extravios']);
    Route::get('/mis-extravios/{idUsuario}', [MobilePublicacionController::class, 'misExtravios']);
    Route::get('/extravios/{id}', [MobilePublicacionController::class, 'detalleExtravio']);
    Route::post('/extravios', [MobilePublicacionController::class, 'storeExtravio']);
    Route::post('/extravios/{id}/update', [MobilePublicacionController::class, 'updateExtravio']);
    Route::post('/extravios/{id}/delete', [MobilePublicacionController::class, 'deleteExtravio']);

    // =====================
    // ADOPCIONES
    // =====================
    Route::get('/adopciones', [MobilePublicacionController::class, 'adopciones']);
    Route::get('/mis-adopciones/{idUsuario}', [MobilePublicacionController::class, 'misAdopciones']);

    Route::post('/adopciones/{id}/solicitudes', [MobileSolicitudAdopcionController::class, 'store']);
    Route::get('/adopciones/solicitudes/enviadas/{idUsuario}', [MobileSolicitudAdopcionController::class, 'enviadas']);
    Route::get('/adopciones/solicitudes/recibidas/{idUsuario}', [MobileSolicitudAdopcionController::class, 'recibidas']);
    Route::post('/adopciones/solicitudes/{id}/estado', [MobileSolicitudAdopcionController::class, 'updateEstado']);

    Route::post('/adopciones/{id}/marcar-adoptada', [MobileAdopcionController::class, 'marcarAdoptada']);
    Route::post('/adopciones/{id}/volver-en-proceso', [MobileAdopcionController::class, 'volverEnProceso']);

    Route::get('/adopciones/{id}', [MobileAdopcionController::class, 'detalle']);
    Route::post('/adopciones', [MobileAdopcionController::class, 'store']);

    // =====================
    // ORGANIZACIONES
    // =====================
    Route::get('/organizaciones', [MobileOrganizacionController::class, 'index']);
    Route::get('/organizaciones/{id}', [MobileOrganizacionController::class, 'show']);

    // =====================
    // PERFIL
    // =====================
    Route::get('/profile/{idUsuario}', [MobilePerfilController::class, 'show']);
    Route::post('/profile/{idUsuario}/update', [MobilePerfilController::class, 'update']);
    Route::post('/profile/{idUsuario}/settings', [MobilePerfilController::class, 'settings']);

        // =====================
    // NOTIFICACIONES
    // =====================
    Route::get('/notificaciones/{idUsuario}', [MobileNotificacionController::class, 'index']);
    Route::get('/notificaciones/{idUsuario}/count', [MobileNotificacionController::class, 'count']);
    Route::post('/notificaciones/{id}/leer', [MobileNotificacionController::class, 'leer']);
    Route::post('/notificaciones/{idUsuario}/leer-todas', [MobileNotificacionController::class, 'leerTodas']);
});