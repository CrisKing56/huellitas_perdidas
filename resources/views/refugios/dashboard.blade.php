@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-orange-100 text-orange-600">
                    Panel institucional
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-green-100 text-green-700">
                    Refugio
                </span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900">{{ $organizacion->nombre }}</h1>
            <p class="text-gray-500 mt-2">
                Administra adopciones, consejos y la información institucional de tu refugio.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500">Adopciones</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['adopciones'] }}</h3>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500">Consejos publicados</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['consejos'] }}</h3>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500">Mascotas activas</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['mascotas_activas'] }}</h3>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500">Pendientes</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pendientes'] }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Acciones principales</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <a href="{{ route('refugios.show', $organizacion->id_organizacion) }}"
                           class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:bg-green-50 hover:border-green-200 transition">
                            <h3 class="font-bold text-gray-900 group-hover:text-green-700">Ver ficha pública</h3>
                            <p class="text-sm text-gray-500 mt-2">
                                Revisa cómo aparece tu refugio en el directorio.
                            </p>
                        </a>

                        <a href="{{ route('adopciones.create') }}"
                           class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:bg-green-50 hover:border-green-200 transition">
                            <h3 class="font-bold text-gray-900 group-hover:text-green-700">Publicar adopción</h3>
                            <p class="text-sm text-gray-500 mt-2">
                                Registra mascotas disponibles bajo el cuidado del refugio.
                            </p>
                        </a>

                        <a href="{{ route('consejos.create') }}"
                           class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:bg-green-50 hover:border-green-200 transition">
                            <h3 class="font-bold text-gray-900 group-hover:text-green-700">Publicar consejo</h3>
                            <p class="text-sm text-gray-500 mt-2">
                                Comparte recomendaciones de bienestar animal y adopción responsable.
                            </p>
                        </a>

                        <a href="{{ route('adopciones.mis-adopciones') }}"
                           class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:bg-green-50 hover:border-green-200 transition">
                            <h3 class="font-bold text-gray-900 group-hover:text-green-700">Gestionar adopciones</h3>
                            <p class="text-sm text-gray-500 mt-2">
                                Consulta tus publicaciones y revisa su estado actual.
                            </p>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Resumen institucional</h2>

                    <div class="space-y-4">
                        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                            <p class="text-sm text-gray-400 uppercase font-semibold">Responsable</p>
                            <p class="font-semibold text-gray-900 mt-1">{{ $organizacion->nombre_responsable ?: 'No registrado' }}</p>
                        </div>

                        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                            <p class="text-sm text-gray-400 uppercase font-semibold">Ubicación</p>
                            <p class="font-semibold text-gray-900 mt-1">
                                {{ $organizacion->colonia ?: 'Sin colonia' }},
                                {{ $organizacion->ciudad ?: 'Sin ciudad' }},
                                {{ $organizacion->estado_direccion ?: 'Sin estado' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                            <p class="text-sm text-gray-400 uppercase font-semibold">Estado de revisión</p>
                            <p class="font-semibold text-gray-900 mt-1">{{ $organizacion->estado_revision }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Accesos rápidos</h2>

                    <div class="space-y-3">
                        <a href="{{ route('perfil') }}" class="block rounded-xl px-4 py-3 bg-gray-50 hover:bg-green-50 text-gray-700 hover:text-green-700 transition font-medium">
                            Ir a mi perfil
                        </a>

                        <a href="{{ route('adopciones.index') }}" class="block rounded-xl px-4 py-3 bg-gray-50 hover:bg-green-50 text-gray-700 hover:text-green-700 transition font-medium">
                            Ver adopciones públicas
                        </a>

                        <a href="{{ route('consejos.index') }}" class="block rounded-xl px-4 py-3 bg-gray-50 hover:bg-green-50 text-gray-700 hover:text-green-700 transition font-medium">
                            Ver consejos
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection