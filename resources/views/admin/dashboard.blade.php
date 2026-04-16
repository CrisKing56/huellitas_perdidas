@extends('admin.layout')

@section('title', 'Dashboard Admin')
@section('top_title', 'Panel Administrador')

@section('content')
@php
    $stats = [
        ['titulo' => 'Usuarios registrados', 'valor' => '1,247', 'icono' => 'users', 'color' => 'bg-blue-50 text-blue-600'],
        ['titulo' => 'Mascotas extraviadas activas', 'valor' => '87', 'icono' => 'search', 'color' => 'bg-orange-50 text-orange-500'],
        ['titulo' => 'Publicaciones en adopción activas', 'valor' => '124', 'icono' => 'heart', 'color' => 'bg-green-50 text-green-600'],
        ['titulo' => 'Reportes pendientes', 'valor' => '15', 'icono' => 'alert', 'color' => 'bg-red-50 text-red-500'],
        ['titulo' => 'Comentarios reportados', 'valor' => '8', 'icono' => 'comment', 'color' => 'bg-yellow-50 text-yellow-600'],
        ['titulo' => 'Veterinarias registradas', 'valor' => '43', 'icono' => 'building', 'color' => 'bg-purple-50 text-purple-600'],
    ];

    $actividad = [
        ['titulo' => 'Max - Perro perdido', 'sub' => 'Publicado en Barrio Lindavista', 'time' => 'Hace 2 horas', 'estado' => 'Activa', 'estadoColor' => 'bg-yellow-100 text-yellow-700', 'img' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=200&q=60'],
        ['titulo' => 'Luna - Gato en adopción', 'sub' => 'Gatita cariñosa de 2 años', 'time' => 'Hace 4 horas', 'estado' => 'Publicada', 'estadoColor' => 'bg-green-100 text-green-700', 'img' => 'https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=200&q=60'],
        ['titulo' => 'María González', 'sub' => 'Nuevo usuario registrado', 'time' => 'Hace 5 horas', 'estado' => 'Verificado', 'estadoColor' => 'bg-blue-100 text-blue-700', 'img' => null],
        ['titulo' => 'Comentario reportado', 'sub' => 'En publicación: Rocky - Beagle perdido', 'time' => 'Hace 1 día', 'estado' => 'Pendiente', 'estadoColor' => 'bg-red-100 text-red-700', 'img' => null],
        ['titulo' => 'Simba - Gato en adopción', 'sub' => 'Gato tranquilo y cariñoso', 'time' => 'Hace 1 día', 'estado' => 'Publicada', 'estadoColor' => 'bg-green-100 text-green-700', 'img' => 'https://images.unsplash.com/photo-1511044568932-338cba0ad803?auto=format&fit=crop&w=200&q=60'],
    ];

    $quickActions = [
        ['texto' => 'Agregar Usuario', 'color' => 'bg-purple-50 text-purple-600 border-purple-100'],
        ['texto' => 'Agregar Administrador', 'color' => 'bg-blue-50 text-blue-600 border-blue-100'],
        ['texto' => 'Revisar reportes pendientes', 'color' => 'bg-red-50 text-red-600 border-red-100'],
        ['texto' => 'Ver todas las publicaciones', 'color' => 'bg-orange-50 text-orange-600 border-orange-100'],
    ];

    function adminIcon($tipo) {
        return match($tipo) {
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10H2v10h5m10 0v-2a4 4 0 10-8 0v2m8 0H7m10-10V6a4 4 0 10-8 0v4m8 0H7"/>',
            'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>',
            'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
            'alert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18h16.68a1 1 0 00.87-1.14l-7.5-13a1 1 0 00-1.74 0z"/>',
            'comment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.2-3.2A7.773 7.773 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            'building' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 12h.01M9 15h.01M13 12h.01M13 15h.01"/>',
            default => '',
        };
    }
@endphp

<div class="space-y-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Resumen del panel</h1>
        <p class="text-gray-500 text-sm sm:text-base">Estadísticas generales del sistema.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($stats as $item)
            <div class="bg-white rounded-3xl border border-gray-100 shadow-soft p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500 leading-6">{{ $item['titulo'] }}</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $item['valor'] }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-2xl flex items-center justify-center {{ $item['color'] }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! adminIcon($item['icono']) !!}
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráficas --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">Publicaciones por mes</h2>
                <p class="text-sm text-gray-500 mt-1">Extravíos y adopciones</p>
            </div>

            <div class="h-72 flex items-end justify-between gap-3 sm:gap-4">
                @php
                    $chartData = [
                        ['mes' => 'Ene', 'adop' => 18, 'extra' => 11],
                        ['mes' => 'Feb', 'adop' => 22, 'extra' => 14],
                        ['mes' => 'Mar', 'adop' => 20, 'extra' => 9],
                        ['mes' => 'Abr', 'adop' => 25, 'extra' => 17],
                        ['mes' => 'May', 'adop' => 28, 'extra' => 13],
                        ['mes' => 'Jun', 'adop' => 30, 'extra' => 19],
                    ];
                    $max = 32;
                @endphp

                @foreach($chartData as $bar)
                    <div class="flex-1 flex flex-col items-center justify-end h-full">
                        <div class="w-full flex items-end justify-center gap-2 h-full">
                            <div class="w-4 sm:w-5 rounded-t-xl bg-orange-400"
                                 style="height: {{ ($bar['extra'] / $max) * 100 }}%"></div>
                            <div class="w-4 sm:w-5 rounded-t-xl bg-emerald-500"
                                 style="height: {{ ($bar['adop'] / $max) * 100 }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 mt-3">{{ $bar['mes'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-6 mt-5 text-xs">
                <div class="flex items-center gap-2 text-gray-500">
                    <span class="h-3 w-3 rounded bg-emerald-500 inline-block"></span>
                    Adopciones
                </div>
                <div class="flex items-center gap-2 text-gray-500">
                    <span class="h-3 w-3 rounded bg-orange-400 inline-block"></span>
                    Extraviados
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-soft p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">Estados de publicaciones</h2>
                <p class="text-sm text-gray-500 mt-1">Distribución general</p>
            </div>

            <div class="flex flex-col items-center justify-center min-h-[288px]">
                <div class="h-56 w-56 rounded-full"
                     style="background: conic-gradient(#f58239 0 56%, #1fbf84 56% 94%, #f4bf2a 94% 100%);">
                    <div class="h-full w-full flex items-center justify-center">
                        <div class="h-28 w-28 rounded-full bg-white"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 text-sm w-full">
                    <div class="text-center">
                        <p class="font-semibold text-orange-500">Activa 56%</p>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-emerald-500">Resuelta 38%</p>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-yellow-500">En revisión 6%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actividad + acciones --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-soft p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Actividad reciente</h2>
                    <p class="text-sm text-gray-500 mt-1">Últimas acciones del sistema</p>
                </div>

                <button type="button" class="text-sm font-medium text-orange-500 hover:text-orange-600 transition">
                    Ver todo
                </button>
            </div>

            <div class="space-y-4">
                @foreach($actividad as $item)
                    <div class="flex items-center gap-4 rounded-2xl p-3 hover:bg-gray-50 transition">
                        <div class="h-14 w-14 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center flex-shrink-0">
                            @if($item['img'])
                                <img src="{{ $item['img'] }}" alt="" class="h-full w-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.2-3.2A7.773 7.773 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $item['titulo'] }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $item['sub'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $item['time'] }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $item['estadoColor'] }}">
                                {{ $item['estado'] }}
                            </span>

                            <button type="button" class="text-orange-400 hover:text-orange-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-soft p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Acciones rápidas</h2>
                    <p class="text-sm text-gray-500 mt-1">Accesos directos</p>
                </div>

                <div class="space-y-3">
                    @foreach($quickActions as $action)
                        <button type="button"
                                class="w-full text-left rounded-2xl border px-4 py-3 font-medium transition hover:scale-[1.01] {{ $action['color'] }}">
                            {{ $action['texto'] }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl bg-amber-50 border border-amber-100 p-4">
                    <p class="text-sm font-semibold text-amber-700 mb-1">Consejo del día</p>
                    <p class="text-sm text-amber-700/90">
                        Revisa diariamente los reportes pendientes para mantener la calidad del contenido.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection