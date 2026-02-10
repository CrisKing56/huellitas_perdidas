<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExtravioController;
use App\Http\Controllers\AdopcionController;

// ✅ Admin
use App\Http\Controllers\Admin\AdminUsuarioController;

// ========================
// RUTAS PÚBLICAS
// ========================
Route::get('/', function () {
    return view('home');
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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ------------------------
// MASCOTAS PERDIDAS
// ------------------------
Route::get('/mascotas-perdidas', [ExtravioController::class, 'index2'])->name('mascotas.index2');
Route::get('/mascota/{id}', [ExtravioController::class, 'show'])->name('extravios.show');

// ------------------------
// CUIDADO ANIMAL (PÚBLICO)
// ------------------------
Route::get('/veterinarias', function () {

    $veterinarias = [
        [
            'imagen' => 'https://images.unsplash.com/photo-1581888227599-779811939961?auto=format&fit=crop&w=900&q=60',
            'nombre' => 'Veterinaria Ocosingo',
            'direccion' => 'Av. Central #123, Ocosingo',
            'telefono' => '919 123 4567',
            'horario' => 'Lun - Sáb 9:00 a 18:00',
            'abierto' => true,
        ],
        [
            'imagen' => 'https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&w=900&q=60',
            'nombre' => 'Clínica Animal Selva',
            'direccion' => 'Col. Centro, Ocosingo',
            'telefono' => '919 765 4321',
            'horario' => 'Lun - Dom 10:00 a 20:00',
            'abierto' => false,
        ],
    ];

    return view('veterinarias.index', compact('veterinarias'));
})->name('veterinarias.index');

Route::get('/refugios', function () {
    return view('refugios.index');
})->name('refugios.index');

Route::get('/consejos', function () {
    return view('consejos.index');
})->name('consejos.index');

// ------------------------
// ADOPCIONES
// ------------------------
Route::get('/adopciones', [AdopcionController::class, 'index'])->name('adopciones.index');
Route::get('/adopciones/{id}', [AdopcionController::class, 'show'])->name('adopciones.show');

// ✅ CREAR / GUARDAR (solo logueados)
Route::middleware(['auth'])->group(function () {
    Route::get('/adopciones/create', [AdopcionController::class, 'create'])->name('adopciones.create');
    Route::post('/adopciones', [AdopcionController::class, 'store'])->name('adopciones.store');
});

// ========================
// RUTAS PROTEGIDAS
// ========================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/mis-reportes', [ExtravioController::class, 'index'])->name('extravios.index');

    Route::get('/reportar-mascota', [ExtravioController::class, 'create'])->name('mascotas.create');
    Route::post('/reportar-mascota', [ExtravioController::class, 'store'])->name('mascotas.store');

    // ✅ ADMIN (solo admins) - CRUD usuarios con BD (YA NO usa usuarios-admin.blade.php)
    Route::middleware(['admin'])->group(function () {

        // (Opcional) si tienes dashboard admin
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
    });
});
