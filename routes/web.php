<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ExtravioController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/a', function () {
    return view('welcome');
});

//ruta publica
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

// Route::get('/mascotas-perdidas', function () {
//     return view('publicar-adopcion');
// })->name('pub.adopcion');


Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro.usuario');

Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/mascotas-perdidas', [ExtravioController::class, 'index2'])->name('mascotas.index2');


Route::middleware('auth')->group(function () {
    Route::get('/reportar-mascota', [ExtravioController::class, 'create'])->name('mascotas.create');
    Route::post('/reportar-mascota', [ExtravioController::class, 'store'])->name('mascotas.store');
});


Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/login', [AuthController::class, 'showLogin'])->name('login'); 
Route::post('/login', [AuthController::class, 'login'])->name('login.store'); 

Route::get('/mascota/{id}', [ExtravioController::class, 'show'])->name('extravios.show');


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/mis-reportes', [ExtravioController::class, 'index'])->name('extravios.index');
   
    Route::post('/reportar', [ExtravioController::class, 'store'])->name('extravios.store');


    Route::get('/mascota/{id}/editar', [ExtravioController::class, 'edit'])->name('extravios.edit');
    Route::put('/mascota/{id}', [ExtravioController::class, 'update'])->name('extravios.update'); 

    Route::delete('/mascota/{id}', [ExtravioController::class, 'destroy'])->name('extravios.destroy');

});