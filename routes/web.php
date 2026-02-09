<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ExtravioController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/a', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('home');
})->name('inicio');

// Route::get('/perdidas', function () {
//     return view('mascotas-perdidas');
// })->name('mascotas.perdidas');

Route::get('/adopcion', function () {
    return view('mascotas-adopcion');
})->name('mascotas.adopcion');

Route::get('/detalle', function () {
    return view('mascota-detalle');
})->name('detalle');

Route::get('/reportar', function () {
    return view('reportar-mascota');
})->name('reportar.mascota');

Route::get('/publicar-adopcion', function () {
    return view('publicar-adopcion');
})->name('pub.adopcion');


Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro.usuario');

// Ruta para enviar los datos (POST)
Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

// Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Formulario para reportar (GET)
Route::get('/reportar-mascota', [ExtravioController::class, 'create'])->name('mascotas.create');

// Recibir los datos del formulario (POST)
Route::post('/reportar-mascota', [ExtravioController::class, 'store'])->name('mascotas.store');

// Ver el catálogo (GET)
Route::get('/perdidas', [ExtravioController::class, 'index'])->name('mascotas.index');

// Catálogo público
Route::get('/mascotas-perdidas', [ExtravioController::class, 'index'])->name('mascotas.index');

// Formulario (requiere login)
// Route::middleware('auth')->group(function () {
//     Route::get('/reportar-mascota', [ExtravioController::class, 'create'])->name('mascotas.create');
//     Route::post('/reportar-mascota', [ExtravioController::class, 'store'])->name('mascotas.store');
// });


// Ruta para ver el formulario (GET)
// Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// // Ruta para enviar los datos (POST)
// Route::post('/login', [LoginController::class, 'login']);

// Ruta para salir (POST es más seguro)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Asegúrate de tener estas dos rutas para el login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login'); // Ver formulario
Route::post('/login', [AuthController::class, 'login'])->name('login.store'); // Recibir datos