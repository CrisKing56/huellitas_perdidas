<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// =========================
// CONTROLLERS
// =========================
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\ExtravioController;
use App\Http\Controllers\AdopcionController;
use App\Http\Controllers\ConsejoController;

use App\Http\Controllers\VeterinariaController;
use App\Http\Controllers\RefugioController;

use App\Http\Controllers\VeterinariaRegistroController;
use App\Http\Controllers\RefugioRegistroController;

use App\Http\Controllers\Admin\AdminUsuarioController;
use App\Http\Controllers\Admin\AdminVeterinariaController;
use App\Http\Controllers\Admin\AdminRefugioController;

// =========================
// MODELOS
// =========================
use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS GENERALES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $mascotasRecientes = PublicacionExtravio::with('fotoPrincipal')
        ->where('estado', '!=', 'RESUELTA')
        ->orderBy('id_publicacion', 'desc')
        ->take(4)
        ->get();

    $adopcionesRecientes = PublicacionAdopcion::with('fotoPrincipal')
        ->where('estado', 'DISPONIBLE')
        ->orderBy('id_publicacion', 'desc')
        ->take(4)
        ->get();

    return view('home', compact('mascotasRecientes', 'adopcionesRecientes'));
})->name('inicio');

Route::get('/detalle', function () {
    return view('mascota-detalle');
})->name('detalle');

Route::get('/reportar', function () {
    return view('reportar-mascota');
})->name('reportar.mascota');

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro.usuario');
Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| VERIFICACIÓN DE CORREO
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/correo/verificar', [AuthController::class, 'showVerifyNotice'])
        ->name('verification.notice');

    Route::get('/correo/verificar/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/correo/reenviar-verificacion', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| LOGIN CON GOOGLE
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| REGISTRO DE ORGANIZACIONES
|--------------------------------------------------------------------------
*/

// Veterinaria
Route::get('/registro-veterinaria', function () {
    return view('veterinarias.alta-veterinaria');
})->name('registro.veterinaria');

Route::post('/registro-veterinaria', [VeterinariaRegistroController::class, 'store'])
    ->name('registro.veterinaria.store');

// Refugio
Route::get('/registro-refugio', [RefugioRegistroController::class, 'create'])->name('registro.refugio');
Route::post('/registro-refugio', [RefugioRegistroController::class, 'store'])->name('registro.refugio.store');

/*
|--------------------------------------------------------------------------
| MASCOTAS PERDIDAS - PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/mascotas-perdidas', [ExtravioController::class, 'index2'])->name('mascotas.index2');
Route::get('/mascota/{id}', [ExtravioController::class, 'show'])->name('extravios.show');

/*
|--------------------------------------------------------------------------
| ADOPCIONES - PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/adopciones', [AdopcionController::class, 'index'])->name('adopciones.index');
Route::get('/adopciones/{id}', [AdopcionController::class, 'show'])->name('adopciones.show');

/*
|--------------------------------------------------------------------------
| CONSEJOS - PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/consejos', [ConsejoController::class, 'index'])->name('consejos.index');
Route::get('/consejos/{id}', [ConsejoController::class, 'show'])->name('consejos.show');

/*
|--------------------------------------------------------------------------
| VETERINARIAS - PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/veterinarias', [VeterinariaController::class, 'index'])->name('veterinarias.index');
Route::get('/veterinarias/{id}', [VeterinariaController::class, 'show'])->name('veterinarias.show');

/*
|--------------------------------------------------------------------------
| REFUGIOS - PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/refugios', [RefugioController::class, 'index'])->name('refugios.index');
Route::get('/refugios/{id}', [RefugioController::class, 'show'])->name('refugios.show');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS - SOLO AUTH
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/nosotros', function () {
        return view('nosotros');
    })->name('nosotros');

    Route::get('/perfil', [UserController::class, 'perfil'])->name('perfil');
    Route::post('/perfil/actualizar', [UserController::class, 'update'])->name('perfil.update');
    Route::post('/perfil/foto', [UserController::class, 'updatePhoto'])->name('perfil.photo');
    Route::post('/perfil/configuracion', [UserController::class, 'updateSettings'])->name('perfil.settings');

    Route::get('/veterinaria/panel', [VeterinariaController::class, 'dashboard'])->name('veterinaria.dashboard');
    Route::get('/refugio/panel', [RefugioController::class, 'dashboard'])->name('refugio.dashboard');
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS - AUTH + VERIFIED
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADOPCIONES - CRUD PRIVADO
    |--------------------------------------------------------------------------
    */

    Route::get('/adopciones/create', [AdopcionController::class, 'create'])->name('adopciones.create');
    Route::get('/adopciones/mis-adopciones', [AdopcionController::class, 'misAdopciones'])->name('adopciones.mis-adopciones');
    Route::post('/adopciones', [AdopcionController::class, 'store'])->name('adopciones.store');

    Route::get('/adopciones/{id}/editar', [AdopcionController::class, 'edit'])->name('adopciones.edit');
    Route::put('/adopciones/{id}', [AdopcionController::class, 'update'])->name('adopciones.update');
    Route::delete('/adopciones/{id}', [AdopcionController::class, 'destroy'])->name('adopciones.destroy');

    /*
    |--------------------------------------------------------------------------
    | EXTRAVÍOS - CRUD PRIVADO
    |--------------------------------------------------------------------------
    */

    Route::get('/mis-reportes', [ExtravioController::class, 'index'])->name('extravios.index');

    Route::get('/reportar-mascota', [ExtravioController::class, 'create'])->name('mascotas.create');
    Route::post('/reportar-mascota', [ExtravioController::class, 'store'])->name('mascotas.store');

    Route::get('/reportar-mascota/{id}/editar', [ExtravioController::class, 'edit'])->name('extravios.edit');
    Route::put('/reportar-mascota/{id}', [ExtravioController::class, 'update'])->name('extravios.update');
    Route::delete('/reportar-mascota/{id}', [ExtravioController::class, 'destroy'])->name('extravios.destroy');

    /*
    |--------------------------------------------------------------------------
    | COMENTARIOS EN PUBLICACIONES DE EXTRAVÍO
    |--------------------------------------------------------------------------
    */

    Route::post('/mascota/{id}/comentarios', [ExtravioController::class, 'storeComment'])
        ->name('extravios.comentarios.store');

    Route::put('/mascota/{id}/comentarios/{comentarioId}', [ExtravioController::class, 'updateComment'])
        ->name('extravios.comentarios.update');

    Route::delete('/mascota/{id}/comentarios/{comentarioId}', [ExtravioController::class, 'destroyComment'])
        ->name('extravios.comentarios.destroy');

    /*
    |--------------------------------------------------------------------------
    | REPORTAR PUBLICACIÓN DE EXTRAVÍO
    |--------------------------------------------------------------------------
    */

    Route::post('/mascota/{id}/reportar', [ExtravioController::class, 'storeReport'])
        ->name('extravios.reportar');

    /*
    |--------------------------------------------------------------------------
    | CONSEJOS - CREAR/GUARDAR
    |--------------------------------------------------------------------------
    */

    Route::get('/consejos/publicar', [ConsejoController::class, 'create'])->name('consejos.create');
    Route::post('/consejos/guardar', [ConsejoController::class, 'store'])->name('consejos.store');

    /*
    |--------------------------------------------------------------------------
    | PANEL ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    Route::middleware(['admin'])->group(function () {

        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Usuarios
        Route::get('/admin/usuarios', [AdminUsuarioController::class, 'index'])->name('admin.usuarios.index');
        Route::get('/admin/usuarios/create', [AdminUsuarioController::class, 'create'])->name('admin.usuarios.create');
        Route::post('/admin/usuarios', [AdminUsuarioController::class, 'store'])->name('admin.usuarios.store');
        Route::get('/admin/usuarios/{id_usuario}/edit', [AdminUsuarioController::class, 'edit'])->name('admin.usuarios.edit');
        Route::put('/admin/usuarios/{id_usuario}', [AdminUsuarioController::class, 'update'])->name('admin.usuarios.update');
        Route::delete('/admin/usuarios/{id_usuario}', [AdminUsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');

        // Veterinarias
        Route::get('/admin/veterinarias', [AdminVeterinariaController::class, 'index'])->name('admin.veterinarias.index');
        Route::get('/admin/veterinarias/{id}', [AdminVeterinariaController::class, 'show'])->name('admin.veterinarias.show');
        Route::post('/admin/veterinarias/{id}/aprobar', [AdminVeterinariaController::class, 'aprobar'])->name('admin.veterinarias.aprobar');
        Route::post('/admin/veterinarias/{id}/rechazar', [AdminVeterinariaController::class, 'rechazar'])->name('admin.veterinarias.rechazar');

        // Refugios
        Route::get('/admin/refugios', [AdminRefugioController::class, 'index'])->name('admin.refugios.index');
        Route::get('/admin/refugios/{id}', [AdminRefugioController::class, 'show'])->name('admin.refugios.show');
        Route::post('/admin/refugios/{id}/aprobar', [AdminRefugioController::class, 'aprobar'])->name('admin.refugios.aprobar');
        Route::post('/admin/refugios/{id}/rechazar', [AdminRefugioController::class, 'rechazar'])->name('admin.refugios.rechazar');
    });
});