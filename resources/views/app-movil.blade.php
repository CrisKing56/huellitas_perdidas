@extends('layout.app')

@section('title', 'Descargar App Móvil')

@section('content')
@php
    $apkRelativePath = 'downloads/huellitas-perdidas.apk';
    $apkExists = file_exists(public_path($apkRelativePath));
    $apkUrl = asset($apkRelativePath);
@endphp

<div class="bg-white min-h-screen">
    <section class="relative overflow-hidden bg-gradient-to-br from-green-50 via-white to-orange-50">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-0 w-72 h-72 bg-green-100/50 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-orange-100/40 rounded-full blur-3xl translate-x-1/4 translate-y-1/4"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-4 py-1.5 text-xs font-bold tracking-wide uppercase border border-green-200">
                        App móvil Huellitas Perdidas
                    </span>

                    <h1 class="mt-5 text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 leading-tight">
                        Lleva Huellitas Perdidas
                        <span class="text-green-600">en tu celular</span>
                    </h1>

                    <p class="mt-5 text-lg text-gray-600 leading-relaxed max-w-2xl">
                        Consulta mascotas extraviadas, revisa adopciones, administra tu perfil y mantente al tanto
                        desde cualquier lugar con nuestra aplicación móvil.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        @if($apkExists)
                            <a href="{{ $apkUrl }}"
                               download
                               class="inline-flex items-center justify-center gap-2 rounded-2xl bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3.5 shadow-sm transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 8l-3-3m3 3l3-3M5 20h14"/>
                                </svg>
                                Descargar APK
                            </a>
                        @else
                            <button type="button"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gray-100 text-gray-500 font-bold px-6 py-3.5 cursor-not-allowed border border-gray-200">
                                APK no disponible aún
                            </button>
                        @endif

                        <button type="button"
                                class="inline-flex items-center justify-center rounded-2xl bg-gray-100 text-gray-500 font-bold px-6 py-3.5 cursor-not-allowed border border-gray-200">
                            Próximamente en Play Store
                        </button>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3 text-sm text-gray-500">
                        <span class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-4 py-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Android
                        </span>
                        <span class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-4 py-2">
                            <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                            Inicio de sesión
                        </span>
                        <span class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-4 py-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            Extravíos y adopciones
                        </span>
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-green-200/40 blur-3xl rounded-full scale-110"></div>

                        <div class="relative w-[290px] sm:w-[330px] rounded-[2.5rem] border-8 border-gray-900 bg-gray-900 shadow-2xl overflow-hidden">
                            <div class="h-7 bg-gray-900 flex items-center justify-center">
                                <div class="w-28 h-2 bg-gray-800 rounded-full"></div>
                            </div>

                            <div class="bg-white min-h-[620px] p-4">
                                <div class="rounded-3xl bg-gradient-to-r from-green-500 to-green-600 text-white p-5 shadow-sm">
                                    <p class="text-xs uppercase tracking-wider font-semibold opacity-90">Huellitas Perdidas</p>
                                    <h3 class="text-2xl font-extrabold mt-2">Encuentra, ayuda, adopta</h3>
                                    <p class="text-sm mt-2 opacity-90">Accede más rápido desde tu teléfono.</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mt-4">
                                    <div class="rounded-2xl border border-gray-200 p-4">
                                        <p class="text-xs text-gray-400 uppercase font-bold">Mascotas</p>
                                        <p class="text-lg font-extrabold text-gray-900 mt-1">Extraviadas</p>
                                    </div>
                                    <div class="rounded-2xl border border-gray-200 p-4">
                                        <p class="text-xs text-gray-400 uppercase font-bold">Módulo</p>
                                        <p class="text-lg font-extrabold text-gray-900 mt-1">Adopción</p>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                                        <p class="font-semibold text-gray-900">Inicio de sesión</p>
                                        <p class="text-sm text-gray-500 mt-1">Accede con tu cuenta registrada.</p>
                                    </div>

                                    <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                                        <p class="font-semibold text-gray-900">Publicaciones activas</p>
                                        <p class="text-sm text-gray-500 mt-1">Consulta reportes y adopciones fácilmente.</p>
                                    </div>

                                    <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                                        <p class="font-semibold text-gray-900">Perfil</p>
                                        <p class="text-sm text-gray-500 mt-1">Revisa tus datos y actividad.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-14 lg:py-16">
        <div class="text-center mb-12">
            <span class="inline-flex items-center rounded-full bg-orange-100 text-orange-700 px-4 py-1.5 text-xs font-bold tracking-wide uppercase border border-orange-200">
                ¿Qué puedes hacer?
            </span>
            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900">
                Funciones principales de la app
            </h2>
            <p class="mt-3 text-gray-500 max-w-2xl mx-auto">
                Pensada para que puedas consultar información importante desde el móvil de forma rápida y sencilla.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-4.553A2 2 0 0018.139 2H5.86a2 2 0 00-1.414 3.447L9 10m6 0v8a2 2 0 01-2 2h-2a2 2 0 01-2-2v-8m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Consultar extravíos</h3>
                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Revisa publicaciones activas de mascotas extraviadas desde tu celular.
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Ver adopciones</h3>
                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Explora mascotas disponibles para adopción y consulta sus detalles.
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 19a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm13 0a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM8 16h8M7 8l2 8m6-8l-2 8"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Gestionar tu cuenta</h3>
                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Inicia sesión y revisa la información de tu perfil directamente en la app.
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Acceso rápido</h3>
                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Ten la plataforma disponible en tu teléfono sin entrar siempre desde navegador.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-14 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        ¿Cómo instalar la app?
                    </h2>
                    <p class="mt-3 text-gray-500">
                        Si todavía no está publicada en tienda, puedes instalar el archivo APK manualmente.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                            <p class="font-bold text-gray-900">1. Descarga el archivo APK</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Pulsa el botón de descarga desde esta misma página.
                            </p>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                            <p class="font-bold text-gray-900">2. Permite instalaciones externas</p>
                            <p class="text-sm text-gray-500 mt-1">
                                En Android, acepta la instalación desde archivos descargados si el sistema lo solicita.
                            </p>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                            <p class="font-bold text-gray-900">3. Instala y abre la app</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Una vez instalada, podrás iniciar sesión y comenzar a usar Huellitas Perdidas.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-2xl font-extrabold text-gray-900">Información técnica</h3>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center justify-between gap-4 border-b border-gray-100 pb-4">
                            <span class="text-gray-500">Versión</span>
                            <span class="font-bold text-gray-900">1.0.0</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 border-b border-gray-100 pb-4">
                            <span class="text-gray-500">Plataforma</span>
                            <span class="font-bold text-gray-900">Android</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 border-b border-gray-100 pb-4">
                            <span class="text-gray-500">Formato</span>
                            <span class="font-bold text-gray-900">APK</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <span class="text-gray-500">Estado</span>
                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-bold border border-green-200">
                                {{ $apkExists ? 'Disponible' : 'Pendiente' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-8">
                        @if($apkExists)
                            <a href="{{ $apkUrl }}"
                               download
                               class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3.5 shadow-sm transition">
                                Descargar ahora
                            </a>
                        @else
                            <button type="button"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gray-100 text-gray-500 font-bold px-6 py-3.5 cursor-not-allowed border border-gray-200">
                                APK no disponible todavía
                            </button>
                        @endif
                    </div>

                    <p class="mt-4 text-xs text-gray-400 leading-relaxed">
                        Si más adelante publicas la app en Play Store, aquí mismo se puede reemplazar este botón
                        por el enlace oficial sin cambiar todo el diseño.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection