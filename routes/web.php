<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// =========================
// CONTROLLERS
// =========================
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroOrganizacionFlujoController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ExtravioController;
use App\Http\Controllers\AdopcionController;
use App\Http\Controllers\SolicitudAdopcionController;
use App\Http\Controllers\ConsejoController;
use App\Http\Controllers\ReporteConsejoController;

use App\Http\Controllers\VeterinariaController;
use App\Http\Controllers\RefugioController;

use App\Http\Controllers\VeterinariaRegistroController;
use App\Http\Controllers\RefugioRegistroController;

use App\Http\Controllers\Admin\AdminUsuarioController;
use App\Http\Controllers\Admin\AdminVeterinariaController;
use App\Http\Controllers\Admin\AdminRefugioController;
use App\Http\Controllers\Admin\AdminReporteController;
use App\Http\Controllers\Admin\AdminConsejoController;
use App\Http\Controllers\Admin\AdminReporteConsejoController;
use App\Http\Controllers\Admin\AdminExtravioController;
use App\Http\Controllers\Admin\AdminAdopcionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBackupController;

use App\Http\Controllers\ContactoController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ReporteController;

use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion;



Route::middleware([
    \App\Http\Middleware\EnsureUserIsVerified::class,
    \App\Http\Middleware\EnsureAccountIsActive::class,
])->group(function () {

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

    Route::view('/app-movil', 'app-movil')->name('app.movil');

    Route::get('/reportar', function () {
        return view('reportar-mascota');
    })->name('reportar.mascota');

    Route::get('/contactanos', [ContactoController::class, 'index'])->name('contactanos');
    Route::post('/contactanos', [ContactoController::class, 'enviar'])
        ->middleware('throttle:5,1')
        ->name('contactanos.enviar');


    Route::get('/olvide-contrasena', [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

    Route::post('/olvide-contrasena', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/restablecer-contrasena/{token}', [PasswordResetController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/restablecer-contrasena', [PasswordResetController::class, 'reset'])
        ->name('password.update');

    /*
    |--------------------------------------------------------------------------
    | AUTENTICACIÓN
    |--------------------------------------------------------------------------
    */

    Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro.usuario');
    Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | FLUJO DE REGISTRO INSTITUCIONAL
    |--------------------------------------------------------------------------
    */

    Route::get('/registro-organizacion/enviado', [RegistroOrganizacionFlujoController::class, 'enviado'])
        ->name('registro.organizacion.enviado');

    Route::get('/registro-organizacion/verificado', [RegistroOrganizacionFlujoController::class, 'verificado'])
        ->name('registro.organizacion.verificado');

    Route::get('/registro-organizacion/pendiente', [RegistroOrganizacionFlujoController::class, 'pendiente'])
        ->name('registro.organizacion.pendiente');

    Route::get('/registro-organizacion/rechazada', [RegistroOrganizacionFlujoController::class, 'rechazada'])
        ->name('registro.organizacion.rechazada');

    Route::post('/registro-organizacion/reenviar-verificacion', [RegistroOrganizacionFlujoController::class, 'reenviar'])
        ->name('registro.organizacion.reenviar');

    /*
    |--------------------------------------------------------------------------
    | VERIFICACIÓN DE CORREO
    |--------------------------------------------------------------------------
    */

    Route::get('/correo/verificar/{id}/{hash}', [RegistroOrganizacionFlujoController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth')->group(function () {
        Route::get('/correo/verificar', [AuthController::class, 'showVerifyNotice'])
            ->name('verification.notice');

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
    | LOGIN CON FACEBOOK
    |--------------------------------------------------------------------------
    */

    Route::get('/auth/facebook', [SocialController::class, 'redirectFacebook'])->name('facebook.login');
    Route::get('/auth/facebook/callback', [SocialController::class, 'callbackFacebook']);

    /*
    |--------------------------------------------------------------------------
    | REGISTRO DE ORGANIZACIONES
    |--------------------------------------------------------------------------
    */

    Route::get('/registro-veterinaria', function () {
        return view('veterinarias.alta-veterinaria');
    })->name('registro.veterinaria');

    Route::post('/registro-veterinaria', [VeterinariaRegistroController::class, 'store'])
        ->name('registro.veterinaria.store');

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
    Route::get('/adopciones/{id}', [AdopcionController::class, 'show'])
        ->whereNumber('id')
        ->name('adopciones.show');

    /*
    |--------------------------------------------------------------------------
    | CONSEJOS - PÚBLICO
    |--------------------------------------------------------------------------
    */

    Route::get('/consejos', [ConsejoController::class, 'index'])->name('consejos.index');
    Route::get('/consejos/{id}', [ConsejoController::class, 'show'])
        ->whereNumber('id')
        ->name('consejos.show');

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
            return redirect()->route('inicio');
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
        | SOLICITUDES DE ADOPCIÓN
        |--------------------------------------------------------------------------
        */

        Route::get('/adopciones/{id}/solicitar', [SolicitudAdopcionController::class, 'create'])
            ->whereNumber('id')
            ->name('adopciones.solicitudes.create');

        Route::post('/adopciones/{id}/solicitar', [SolicitudAdopcionController::class, 'store'])
            ->whereNumber('id')
            ->name('adopciones.solicitudes.store');

        Route::get('/adopciones/solicitudes/enviadas', [SolicitudAdopcionController::class, 'enviadas'])
            ->name('adopciones.solicitudes.enviadas');

        Route::get('/adopciones/solicitudes/recibidas', [SolicitudAdopcionController::class, 'recibidas'])
            ->name('adopciones.solicitudes.recibidas');

        Route::patch('/adopciones/solicitudes/{id}/estado', [SolicitudAdopcionController::class, 'updateEstado'])
            ->whereNumber('id')
            ->name('adopciones.solicitudes.updateEstado');

        Route::patch('/adopciones/{id}/marcar-adoptada', [AdopcionController::class, 'marcarAdoptada'])
            ->whereNumber('id')
            ->name('adopciones.marcarAdoptada');

        Route::patch('/adopciones/{id}/volver-en-proceso', [AdopcionController::class, 'volverEnProceso'])
            ->whereNumber('id')
            ->name('adopciones.volverEnProceso');

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
        Route::patch('/reportar-mascota/{id}/resolver', [ExtravioController::class, 'marcarResuelta'])->name('extravios.resolve');
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
        | AVISTAMIENTOS EN PUBLICACIONES DE EXTRAVÍO
        |--------------------------------------------------------------------------
        */

        Route::post('/mascota/{id}/avistamientos', [ExtravioController::class, 'storeSighting'])
            ->name('extravios.avistamientos.store');

        Route::post('/avistamientos/{id}/visto', [ExtravioController::class, 'markSightingSeen'])
            ->name('extravios.avistamientos.visto');

        Route::post('/avistamientos/{id}/descartar', [ExtravioController::class, 'discardSighting'])
            ->name('extravios.avistamientos.descartar');

        /*
        |--------------------------------------------------------------------------
        | REPORTAR PUBLICACIÓN DE EXTRAVÍO
        |--------------------------------------------------------------------------
        */

        Route::post('/mascota/{id}/reportar', [ExtravioController::class, 'storeReport'])
            ->name('extravios.reportar');

        /*
        |--------------------------------------------------------------------------
        | CONSEJOS - CRUD INSTITUCIONAL
        |--------------------------------------------------------------------------
        */

        Route::get('/consejos/publicar', [ConsejoController::class, 'create'])->name('consejos.create');
        Route::post('/consejos/guardar', [ConsejoController::class, 'store'])->name('consejos.store');

        Route::get('/consejos/mis-consejos', [ConsejoController::class, 'misConsejos'])
            ->name('consejos.mis-consejos');

        Route::get('/consejos/{id}/editar', [ConsejoController::class, 'edit'])
            ->whereNumber('id')
            ->name('consejos.edit');

        Route::put('/consejos/{id}', [ConsejoController::class, 'update'])
            ->whereNumber('id')
            ->name('consejos.update');

        Route::delete('/consejos/{id}', [ConsejoController::class, 'destroy'])
            ->whereNumber('id')
            ->name('consejos.destroy');

        Route::post('/consejos/{id}/reportar', [ReporteConsejoController::class, 'store'])
            ->whereNumber('id')
            ->name('consejos.reportar');

        /*
        |--------------------------------------------------------------------------
        | RESEÑAS DE VETERINARIAS
        |--------------------------------------------------------------------------
        */

        Route::post('/veterinarias/{id}/resenas', [VeterinariaController::class, 'storeResena'])
            ->whereNumber('id')
            ->name('veterinarias.resenas.store');

        Route::delete('/veterinarias/{id}/resenas/{resenaId}', [VeterinariaController::class, 'destroyResena'])
            ->whereNumber('id')
            ->whereNumber('resenaId')
            ->name('veterinarias.resenas.destroy');

        /*
        |--------------------------------------------------------------------------
        | PANEL ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        Route::middleware(['admin'])->group(function () {

            Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

            // Usuarios
            Route::get('/admin/usuarios', [AdminUsuarioController::class, 'index'])->name('admin.usuarios.index');
            Route::get('/admin/usuarios/create', [AdminUsuarioController::class, 'create'])->name('admin.usuarios.create');
            Route::post('/admin/usuarios', [AdminUsuarioController::class, 'store'])->name('admin.usuarios.store');
            Route::get('/admin/usuarios/{id_usuario}/edit', [AdminUsuarioController::class, 'edit'])->name('admin.usuarios.edit');
            Route::put('/admin/usuarios/{id_usuario}', [AdminUsuarioController::class, 'update'])->name('admin.usuarios.update');
            Route::patch('/admin/usuarios/{id_usuario}/activar', [AdminUsuarioController::class, 'activate'])->name('admin.usuarios.activate');
            Route::patch('/admin/usuarios/{id_usuario}/suspender', [AdminUsuarioController::class, 'suspend'])->name('admin.usuarios.suspend');
            Route::delete('/admin/usuarios/{id_usuario}', [AdminUsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');

            // Respaldos
            Route::get('/admin/backups', [AdminBackupController::class, 'index'])->name('admin.backups.index');
            Route::post('/admin/backups', [AdminBackupController::class, 'store'])->name('admin.backups.store');
            Route::get('/admin/backups/{file}/download', [AdminBackupController::class, 'download'])
                ->where('file', '.*')
                ->name('admin.backups.download');
            Route::delete('/admin/backups/{file}', [AdminBackupController::class, 'destroy'])
                ->where('file', '.*')
                ->name('admin.backups.destroy');

            // Veterinarias
            Route::get('/admin/veterinarias', [AdminVeterinariaController::class, 'index'])->name('admin.veterinarias.index');
            Route::get('/admin/veterinarias/{id}', [AdminVeterinariaController::class, 'show'])->name('admin.veterinarias.show');
            Route::post('/admin/veterinarias/{id}/aprobar', [AdminVeterinariaController::class, 'aprobar'])->name('admin.veterinarias.aprobar');
            Route::post('/admin/veterinarias/{id}/rechazar', [AdminVeterinariaController::class, 'rechazar'])->name('admin.veterinarias.rechazar');
            Route::post('/admin/veterinarias/{id}/suspender', [AdminVeterinariaController::class, 'suspender'])->name('admin.veterinarias.suspender');
            Route::post('/admin/veterinarias/{id}/reactivar', [AdminVeterinariaController::class, 'reactivar'])->name('admin.veterinarias.reactivar');

            // Refugios
            Route::get('/admin/refugios', [AdminRefugioController::class, 'index'])->name('admin.refugios.index');
            Route::get('/admin/refugios/{id}', [AdminRefugioController::class, 'show'])->name('admin.refugios.show');
            Route::post('/admin/refugios/{id}/aprobar', [AdminRefugioController::class, 'aprobar'])->name('admin.refugios.aprobar');
            Route::post('/admin/refugios/{id}/rechazar', [AdminRefugioController::class, 'rechazar'])->name('admin.refugios.rechazar');
            Route::post('/admin/refugios/{id}/suspender', [AdminRefugioController::class, 'suspender'])->name('admin.refugios.suspender');
            Route::post('/admin/refugios/{id}/activar', [AdminRefugioController::class, 'activar'])->name('admin.refugios.activar');

            // Reportes
            Route::get('/admin/reportes', [AdminReporteController::class, 'index'])->name('admin.reportes.index');
            Route::get('/admin/reportes/{id}', [AdminReporteController::class, 'show'])->name('admin.reportes.show');
            Route::post('/admin/reportes/{id}/en-revision', [AdminReporteController::class, 'marcarEnRevision'])->name('admin.reportes.enRevision');
            Route::post('/admin/reportes/{id}/resolver', [AdminReporteController::class, 'resolver'])->name('admin.reportes.resolver');

            // Consejos
            Route::get('/admin/consejos', [AdminConsejoController::class, 'index'])->name('admin.consejos.index');
            Route::get('/admin/consejos/{id}', [AdminConsejoController::class, 'show'])->whereNumber('id')->name('admin.consejos.show');
            Route::post('/admin/consejos/{id}/aprobar', [AdminConsejoController::class, 'aprobar'])->whereNumber('id')->name('admin.consejos.aprobar');
            Route::post('/admin/consejos/{id}/rechazar', [AdminConsejoController::class, 'rechazar'])->whereNumber('id')->name('admin.consejos.rechazar');

            // Reportes de consejos
            Route::get('/admin/reportes-consejos', [AdminReporteConsejoController::class, 'index'])->name('admin.reportes-consejos.index');
            Route::get('/admin/reportes-consejos/{id}', [AdminReporteConsejoController::class, 'show'])->whereNumber('id')->name('admin.reportes-consejos.show');
            Route::post('/admin/reportes-consejos/{id}/en-revision', [AdminReporteConsejoController::class, 'marcarEnRevision'])->whereNumber('id')->name('admin.reportes-consejos.en-revision');
            Route::post('/admin/reportes-consejos/{id}/resolver', [AdminReporteConsejoController::class, 'resolver'])->whereNumber('id')->name('admin.reportes-consejos.resolver');

            // Publicaciones de extravío
            Route::get('/admin/extravios', [AdminExtravioController::class, 'index'])->name('admin.extravios.index');
            Route::get('/admin/extravios/{id}', [AdminExtravioController::class, 'show'])->whereNumber('id')->name('admin.extravios.show');
            Route::post('/admin/extravios/{id}/ocultar', [AdminExtravioController::class, 'ocultar'])->whereNumber('id')->name('admin.extravios.ocultar');
            Route::post('/admin/extravios/{id}/reactivar', [AdminExtravioController::class, 'reactivar'])->whereNumber('id')->name('admin.extravios.reactivar');

            // Publicaciones de adopción
            Route::get('/admin/adopciones', [AdminAdopcionController::class, 'index'])->name('admin.adopciones.index');
            Route::get('/admin/adopciones/{id}', [AdminAdopcionController::class, 'show'])->whereNumber('id')->name('admin.adopciones.show');
            Route::post('/admin/adopciones/{id}/pausar', [AdminAdopcionController::class, 'pausar'])->whereNumber('id')->name('admin.adopciones.pausar');
            Route::post('/admin/adopciones/{id}/reactivar', [AdminAdopcionController::class, 'reactivar'])->whereNumber('id')->name('admin.adopciones.reactivar');

            // PDFs de reportes
            Route::get('/admin/reportes/mascotas/pdf', [ReporteController::class, 'generarReporteMascotas'])
                ->name('reportes.mascotas.pdf');

            Route::get('/admin/reportes/adopciones/pdf', [ReporteController::class, 'generarReporteAdopciones'])
                ->name('reportes.adopciones.pdf');

            Route::get('/admin/reportes/veterinarias/pdf', [ReporteController::class, 'generarReporteVeterinarias'])
                ->name('reportes.veterinarias.pdf');

            Route::get('/admin/reportes/refugios/pdf', [ReporteController::class, 'generarReporteRefugios'])
                ->name('reportes.refugios.pdf');

            Route::get('/admin/reportes/usuarios/pdf', [ReporteController::class, 'generarReporteUsuarios'])
                ->name('reportes.usuarios.pdf');
        });
    });
});