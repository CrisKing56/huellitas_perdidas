@extends('admin.layout')

@section('title', 'Dashboard Admin')
@section('top_title', 'Panel Administrador')

@section('content')
@php
    function adminIcon($tipo) {
        return match($tipo) {
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10H2v10h5m10 0v-2a4 4 0 10-8 0v2m8 0H7m10-10V6a4 4 0 10-8 0v4m8 0H7"/>',
            'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>',
            'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
            'alert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18h16.68a1 1 0 00.87-1.14l-7.5-13a1 1 0 00-1.74 0z"/>',
            'comment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.2-3.2A7.773 7.773 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            'building' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 12h.01M9 15h.01M13 12h.01M13 15h.01"/>',
            'book' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13M12 6.253C10.832 5.477 9.246 5 7.5 5 4.462 5 2 6.462 2 8.267v8.466C2 18.538 4.462 20 7.5 20c1.746 0 3.332-.477 4.5-1.253m0-12.494C13.168 5.477 14.754 5 16.5 5 19.538 5 22 6.462 22 8.267v8.466C22 18.538 19.538 20 16.5 20c-1.746 0-3.332-.477-4.5-1.253"/>',
            'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7 9 7v9a2 2 0 01-2 2h-4v-6H9v6H5a2 2 0 01-2-2v-9z"/>',
            default => '',
        };
    }

    $corteActivas = $estadoResumen['activas_pct'];
    $corteFinalizadas = $estadoResumen['activas_pct'] + $estadoResumen['finalizadas_pct'];
@endphp

<div class="space-y-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Resumen del panel</h1>
        <p class="text-gray-500 text-sm sm:text-base">Estadísticas reales del sistema.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
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
                <p class="text-sm text-gray-500 mt-1">Extravíos y adopciones en los últimos 6 meses</p>
            </div>

            <div class="h-72 flex items-end justify-between gap-3 sm:gap-4">
                @foreach($chartData as $bar)
                    <div class="flex-1 flex flex-col items-center justify-end h-full">
                        <div class="w-full flex items-end justify-center gap-2 h-full">
                            <div class="w-4 sm:w-5 rounded-t-xl bg-orange-400"
                                 style="height: {{ ($bar['extra'] / $maxChart) * 100 }}%"></div>
                            <div class="w-4 sm:w-5 rounded-t-xl bg-emerald-500"
                                 style="height: {{ ($bar['adop'] / $maxChart) * 100 }}%"></div>
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
                <h2 class="text-xl font-bold text-gray-900">Estados generales</h2>
                <p class="text-sm text-gray-500 mt-1">Publicaciones y consejos</p>
            </div>

            <div class="flex flex-col items-center justify-center min-h-[288px]">
                <div class="h-56 w-56 rounded-full"
                     style="background:
                        conic-gradient(
                            #f58239 0 {{ $estadoResumen['activas_pct'] }}%,
                            #1fbf84 {{ $estadoResumen['activas_pct'] }}% {{ $corteFinalizadas }}%,
                            #f4bf2a {{ $corteFinalizadas }}% 100%
                        );">
                    <div class="h-full w-full flex items-center justify-center">
                        <div class="h-28 w-28 rounded-full bg-white"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 text-sm w-full">
                    <div class="text-center">
                        <p class="font-semibold text-orange-500">Activas {{ $estadoResumen['activas_pct'] }}%</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $estadoResumen['activas'] }} registros</p>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-emerald-500">Finalizadas {{ $estadoResumen['finalizadas_pct'] }}%</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $estadoResumen['finalizadas'] }} registros</p>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-yellow-500">Pendientes {{ $estadoResumen['pendientes_pct'] }}%</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $estadoResumen['pendientes'] }} registros</p>
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
                    <p class="text-sm text-gray-500 mt-1">Últimos movimientos reales del sistema</p>
                </div>
            </div>

            @if($actividad->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-200 p-8 text-center text-gray-500">
                    Aún no hay actividad reciente para mostrar.
                </div>
            @else
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

                                @if($item['url'])
                                    <a href="{{ $item['url'] }}" class="text-orange-400 hover:text-orange-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-soft p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Acciones rápidas</h2>
                    <p class="text-sm text-gray-500 mt-1">Accesos directos reales</p>
                </div>

                <div class="space-y-3">
                    @foreach($quickActions as $action)
                        <a href="{{ $action['ruta'] }}"
                           class="block w-full text-left rounded-2xl border px-4 py-3 font-medium transition hover:scale-[1.01] {{ $action['color'] }}">
                            {{ $action['texto'] }}
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl bg-amber-50 border border-amber-100 p-4">
                    <p class="text-sm font-semibold text-amber-700 mb-1">Sugerencia</p>
                    <p class="text-sm text-amber-700/90">
                        Prioriza primero los reportes pendientes y luego revisa las publicaciones activas más recientes.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection