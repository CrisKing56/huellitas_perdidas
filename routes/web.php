<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ConsejoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExtravioController;
use App\Http\Controllers\AdopcionController;
use App\Http\Controllers\RefugioController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\VeterinariaRegistroController;
use App\Http\Controllers\RefugioRegistroController;
use App\Http\Controllers\Admin\AdminUsuarioController;
use App\Http\Controllers\Admin\AdminVeterinariaController;
use App\Http\Controllers\Admin\AdminRefugioController;
use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion;

// ========================
// RUTAS PÚBLICAS
// ========================
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

// ------------------------
// AUTH
// ------------------------
Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro.usuario');
Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ------------------------
// REGISTRO VETERINARIA
// ------------------------
Route::get('/registro-veterinaria', function () {
    return view('veterinarias.alta-veterinaria');
})->name('registro.veterinaria');

Route::post('/registro-veterinaria', [VeterinariaRegistroController::class, 'store'])
    ->name('registro.veterinaria.store');

// ------------------------
// MASCOTAS PERDIDAS
// ------------------------
Route::get('/mascotas-perdidas', [ExtravioController::class, 'index2'])->name('mascotas.index2');
Route::get('/mascota/{id}', [ExtravioController::class, 'show'])->name('extravios.show');

// ------------------------
// CUIDADO ANIMAL (PÚBLICO)
// ------------------------
Route::get('/veterinarias', function () {
    $veterinarias = DB::table('organizaciones as o')
        ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
        ->leftJoin('organizacion_fotos as f', function ($join) {
            $join->on('f.organizacion_id', '=', 'o.id_organizacion')
                 ->where('f.orden', '=', 1);
        })
        ->where('o.tipo', 'VETERINARIA')
        ->where('o.estado_revision', 'APROBADA')
        ->select(
            'o.id_organizacion',
            'o.nombre',
            'o.descripcion',
            'o.telefono',
            'd.calle_numero',
            'd.colonia',
            'd.ciudad',
            'd.estado as estado_direccion',
            'f.url as imagen'
        )
        ->orderByDesc('o.id_organizacion')
        ->get();

    return view('veterinarias.index', compact('veterinarias'));
})->name('veterinarias.index');

Route::get('/veterinarias/{id}', function ($id) {
    $veterinaria = DB::table('organizaciones as o')
        ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
        ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
        ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id')
        ->leftJoin('veterinaria_detalle as vd', 'vd.organizacion_id', '=', 'o.id_organizacion')
        ->where('o.tipo', 'VETERINARIA')
        ->where('o.estado_revision', 'APROBADA')
        ->where('o.id_organizacion', $id)
        ->select(
            'o.*',
            'u.correo',
            'u.whatsapp',
            'd.calle_numero',
            'd.colonia',
            'd.codigo_postal',
            'd.ciudad',
            'd.estado as estado_direccion',
            'ub.latitud',
            'ub.longitud',
            'vd.medico_responsable',
            'vd.cedula_profesional',
            'vd.num_veterinarios',
            'vd.otros_servicios'
        )
        ->first();

    abort_if(!$veterinaria, 404);

    $horarios = DB::table('horarios_atencion')
        ->where('organizacion_id', $id)
        ->orderBy('dia_semana')
        ->get();

    $servicios = DB::table('organizacion_servicio as os')
        ->join('servicios as s', 's.id_servicio', '=', 'os.servicio_id')
        ->where('os.organizacion_id', $id)
        ->pluck('s.nombre');

    $costos = DB::table('organizacion_costo_servicio as ocs')
        ->join('servicios as s', 's.id_servicio', '=', 'ocs.servicio_id')
        ->where('ocs.organizacion_id', $id)
        ->select('s.nombre', 'ocs.precio', 'ocs.moneda')
        ->get();

    $fotos = DB::table('organizacion_fotos')
        ->where('organizacion_id', $id)
        ->orderBy('orden')
        ->get();

    return view('veterinarias.show', compact('veterinaria', 'horarios', 'servicios', 'costos', 'fotos'));
})->name('veterinarias.show');

Route::get('/refugios', function () {
    return view('refugios.index');
})->name('refugios.index');

Route::get('/consejos', function () {
    return view('consejos.index');
})->name('consejos.index');

// ✅ CREAR / GUARDAR (solo logueados)
Route::middleware(['auth'])->group(function () {
    Route::get('/adopciones/create', [AdopcionController::class, 'create'])->name('adopciones.create');
    Route::get('/adopciones/mis-adopciones', [AdopcionController::class, 'misAdopciones'])->name('adopciones.mis-adopciones');
    Route::post('/adopciones', [AdopcionController::class, 'store'])->name('adopciones.store');

    Route::get('/adopciones/{id}/editar', [AdopcionController::class, 'edit'])->name('adopciones.edit');
    Route::put('/adopciones/{id}', [AdopcionController::class, 'update'])->name('adopciones.update');
    Route::delete('/adopciones/{id}', [AdopcionController::class, 'destroy'])->name('adopciones.destroy');
});

// ------------------------
// ADOPCIONES
// ------------------------
Route::get('/adopciones', [AdopcionController::class, 'index'])->name('adopciones.index');
Route::get('/adopciones/{id}', [AdopcionController::class, 'show'])->name('adopciones.show');

// ========================
// RUTAS PROTEGIDAS
// ========================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/mis-reportes', [ExtravioController::class, 'index'])->name('extravios.index');
    Route::get('/reportar-mascota/{id}/editar', [ExtravioController::class, 'edit'])->name('extravios.edit');
    Route::put('/reportar-mascota/{id}', [ExtravioController::class, 'update'])->name('extravios.update');
    Route::delete('/reportar-mascota/{id}', [ExtravioController::class, 'destroy'])->name('extravios.destroy');

    Route::get('/reportar-mascota', [ExtravioController::class, 'create'])->name('mascotas.create');
    Route::post('/reportar-mascota', [ExtravioController::class, 'store'])->name('mascotas.store');

    Route::post('/mascota/{id}/comentarios', [ExtravioController::class, 'storeComment'])
        ->name('extravios.comentarios.store');

    // ✅ ADMIN (solo admins)
    Route::middleware(['admin'])->group(function () {

        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // CRUD Usuarios
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
Route::get('/consejos', [ConsejoController::class, 'index'])->name('consejos.index');
Route::get('/consejos/publicar', [ConsejoController::class, 'create'])->name('consejos.create');
Route::post('/consejos/guardar', [ConsejoController::class, 'store'])->name('consejos.store');
Route::get('/consejos/{id}', [ConsejoController::class, 'show'])->name('consejos.show');

Route::get('/registro-refugio', [RefugioRegistroController::class, 'create'])->name('registro.refugio');
Route::post('/registro-refugio', [RefugioRegistroController::class, 'store'])->name('registro.refugio.store');

Route::get('/refugios', [RefugioController::class, 'index'])->name('refugios.index');
Route::get('/refugios/{id}', [RefugioController::class, 'show'])->name('refugios.show');