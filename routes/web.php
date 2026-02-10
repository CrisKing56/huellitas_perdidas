<?php

use App\Http\Controllers\AdopcionController;
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

Route::get('/detalle', function () {
    return view('mascota-detalle');
})->name('detalle');

Route::get('/reportar', function () {
    return view('reportar-mascota');
})->name('reportar.mascota');


Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro.usuario');

Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/mascotas-perdidas', [ExtravioController::class, 'index'])->name('mascotas.index');
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
    


    //Adopciones
    Route::get('/mis-adopciones', [AdopcionController::class, 'misAdopciones'])->name('adopciones.mis-adopciones');
    
    Route::get('/publicar-adopcion', [AdopcionController::class, 'create'])->name('adopciones.create');
    Route::post('/adopciones', [AdopcionController::class, 'store'])->name('adopciones.store');
    
    Route::get('/adopciones/{id}/editar', [AdopcionController::class, 'edit'])->name('adopciones.edit');
    Route::put('/adopciones/{id}', [AdopcionController::class, 'update'])->name('adopciones.update');
    Route::delete('/adopciones/{id}', [AdopcionController::class, 'destroy'])->name('adopciones.destroy');
});


Route::get('/adopciones', [AdopcionController::class, 'index'])->name('adopciones.index');

Route::get('/adopciones/{id}', [AdopcionController::class, 'show'])->name('adopciones.show');
