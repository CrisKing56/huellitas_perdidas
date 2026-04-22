@extends('layout.app')

@section('content')
@php
    $filtros = $filtros ?? [
        'q' => request('q'),
        'costo' => request('costo'),
        'orden' => request('orden', 'recientes'),
        'latitud' => request('latitud'),
        'longitud' => request('longitud'),
    ];

    $totalVeterinarias = method_exists($veterinarias, 'total')
        ? $veterinarias->total()
        : count($veterinarias ?? []);

    $hayCercaniaActiva = !empty($filtros['latitud']) && !empty($filtros['longitud']) && ($filtros['orden'] === 'cercanas');
@endphp

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-500 mb-2">
                    Cuidado animal
                </p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Catálogo de veterinarias
                </h1>
                <p class="text-sm text-gray-500 mt-2 max-w-2xl">
                    Encuentra veterinarias registradas en la plataforma para consultar información, ubicación, costos y medios de contacto.
                </p>
            </div>

            <a href="{{ route('registro.veterinaria') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition w-full md:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Publicar veterinaria
            </a>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5 mb-8">
            <form id="form-filtros-veterinarias" action="{{ route('veterinarias.index') }}" method="GET" class="space-y-4">
                <input type="hidden" name="latitud" id="latitud" value="{{ $filtros['latitud'] }}">
                <input type="hidden" name="longitud" id="longitud" value="{{ $filtros['longitud'] }}">

                <div class="flex flex-col xl:flex-row xl:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>

                            <input
                                type="text"
                                name="q"
                                value="{{ $filtros['q'] }}"
                                placeholder="Nombre, ciudad, colonia, teléfono o descripción..."
                                class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                            >
                        </div>
                    </div>

                    <div class="w-full xl:w-56">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filtrar por costo</label>
                        <select name="costo"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                            <option value="">Todos</option>
                            <option value="economico" {{ $filtros['costo'] === 'economico' ? 'selected' : '' }}>Económicas (hasta $300)</option>
                            <option value="medio" {{ $filtros['costo'] === 'medio' ? 'selected' : '' }}>$301 a $700</option>
                            <option value="alto" {{ $filtros['costo'] === 'alto' ? 'selected' : '' }}>Más de $700</option>
                            <option value="con_costos" {{ $filtros['costo'] === 'con_costos' ? 'selected' : '' }}>Solo con costos publicados</option>
                        </select>
                    </div>

                    <div class="w-full xl:w-56">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ordenar por</label>
                        <select name="orden" id="orden"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                            <option value="recientes" {{ $filtros['orden'] === 'recientes' ? 'selected' : '' }}>Más recientes</option>
                            <option value="nombre" {{ $filtros['orden'] === 'nombre' ? 'selected' : '' }}>Nombre A-Z</option>
                            <option value="costo_menor" {{ $filtros['orden'] === 'costo_menor' ? 'selected' : '' }}>Menor costo</option>
                            <option value="costo_mayor" {{ $filtros['orden'] === 'costo_mayor' ? 'selected' : '' }}>Mayor costo</option>
                            <option value="cercanas" {{ $filtros['orden'] === 'cercanas' ? 'selected' : '' }}>Más cercanas</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition">
                            Aplicar
                        </button>

                        <a href="{{ route('veterinarias.index') }}"
                           class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                            Limpiar
                        </a>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <button type="button"
                            id="btn-ubicacion"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-orange-200 bg-orange-50 text-orange-700 font-semibold hover:bg-orange-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .552-.224 1.052-.586 1.414A1.994 1.994 0 0110 13a2 2 0 100 4h4a2 2 0 100-4 1.994 1.994 0 01-1.414-.586A1.994 1.994 0 0112 11z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2m0 16v2m8-10h2M2 12H4m12.95 6.95l1.414 1.414M4.636 4.636L6.05 6.05m10.9-1.414L15.536 6.05M6.05 17.95l-1.414 1.414"></path>
                        </svg>
                        Usar mi ubicación para ordenar por cercanía
                    </button>

                    @if($hayCercaniaActiva)
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-green-50 text-green-700 text-sm font-medium border border-green-100">
                            Cercanía activada
                        </span>
                    @endif
                </div>
            </form>
        </div>

        {{-- Resumen --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">
                    {{ $totalVeterinarias }} veterinarias encontradas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Resultados actualizados según tus filtros.
                </p>
            </div>

            @if($filtros['q'] || $filtros['costo'] || $hayCercaniaActiva)
                <div class="flex flex-wrap gap-2">
                    @if($filtros['q'])
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-orange-50 text-orange-700 text-sm font-medium border border-orange-100">
                            "{{ $filtros['q'] }}"
                        </span>
                    @endif

                    @if($filtros['costo'])
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-orange-50 text-orange-700 text-sm font-medium border border-orange-100">
                            Costo: {{ ucfirst(str_replace('_', ' ', $filtros['costo'])) }}
                        </span>
                    @endif

                    @if($hayCercaniaActiva)
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-orange-50 text-orange-700 text-sm font-medium border border-orange-100">
                            Ordenadas por cercanía
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Grid de cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse(($veterinarias ?? []) as $vet)
                @php
                    $direccion = collect([
                        $vet->calle_numero ?? null,
                        $vet->colonia ?? null,
                        $vet->ciudad ?? null,
                    ])->filter()->implode(', ');

                    $imagen = !empty($vet->imagen)
                        ? asset('storage/' . ltrim($vet->imagen, '/'))
                        : null;

                    $rangoCosto = null;
                    if (!is_null($vet->costo_minimo)) {
                        if (!is_null($vet->costo_maximo) && (float) $vet->costo_maximo > (float) $vet->costo_minimo) {
                            $rangoCosto = '$' . number_format((float) $vet->costo_minimo, 2) . ' - $' . number_format((float) $vet->costo_maximo, 2);
                        } else {
                            $rangoCosto = 'Desde $' . number_format((float) $vet->costo_minimo, 2);
                        }
                    }
                @endphp

                <article class="bg-white rounded-3xl shadow-sm hover:shadow-lg transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                    <div class="relative h-56 overflow-hidden bg-gray-100">
                        @if($imagen)
                            <img src="{{ $imagen }}"
                                 alt="{{ $vet->nombre }}"
                                 class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <svg class="w-14 h-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>

                        <span class="absolute top-4 left-4 bg-green-100 text-green-700 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide shadow-sm">
                            Disponible
                        </span>

                        <span class="absolute top-4 right-4 bg-white/90 text-gray-700 text-[11px] font-semibold px-3 py-1 rounded-full shadow-sm backdrop-blur-sm">
                            Veterinaria
                        </span>

                        <div class="absolute bottom-3 right-3 flex items-center gap-2 px-3 py-2 rounded-xl bg-black/25 backdrop-blur-sm">
                            <img src="{{ asset('img/logo1.png') }}"
                                 alt="Huellitas Perdidas"
                                 class="h-6 w-6 object-contain brightness-0 invert opacity-95">
                            <span class="text-white text-[11px] font-bold tracking-wide">
                                Huellitas Perdidas
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                {{ $vet->nombre }}
                            </h3>

                            @if($rangoCosto)
                                <span class="flex-shrink-0 text-[11px] font-bold px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-100">
                                    {{ $rangoCosto }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm text-gray-500 mb-5">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $direccion ?: 'Dirección no registrada' }}</span>
                            </div>

                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>{{ $vet->telefono ?: 'Teléfono no registrado' }}</span>
                            </div>

                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8m-8 4h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                                </svg>
                                <span>{{ \Illuminate\Support\Str::limit($vet->descripcion ?: 'Sin descripción disponible.', 95) }}</span>
                            </div>

                            @if(!is_null($vet->distancia_km) && $hayCercaniaActiva)
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"></path>
                                    </svg>
                                    <span>Aproximadamente a {{ number_format((float) $vet->distancia_km, 1) }} km de tu ubicación</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <a href="{{ route('veterinarias.show', $vet->id_organizacion) }}"
                               class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-50 text-orange-600 font-semibold py-2.5 border border-orange-100 group-hover:bg-orange-500 group-hover:text-white group-hover:border-orange-500 transition">
                                Ver detalles
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-dashed border-gray-200 p-10 text-center text-gray-500">
                    No se encontraron veterinarias con los filtros seleccionados.
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if(method_exists($veterinarias, 'links'))
            <div class="mt-8">
                {{ $veterinarias->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonUbicacion = document.getElementById('btn-ubicacion');
        const inputLatitud = document.getElementById('latitud');
        const inputLongitud = document.getElementById('longitud');
        const inputOrden = document.getElementById('orden');
        const form = document.getElementById('form-filtros-veterinarias');

        if (!botonUbicacion || !inputLatitud || !inputLongitud || !inputOrden || !form) {
            return;
        }

        botonUbicacion.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Tu navegador no permite obtener la ubicación.');
                return;
            }

            botonUbicacion.disabled = true;
            botonUbicacion.classList.add('opacity-70', 'cursor-not-allowed');
            botonUbicacion.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Obteniendo ubicación...
            `;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    inputLatitud.value = position.coords.latitude;
                    inputLongitud.value = position.coords.longitude;
                    inputOrden.value = 'cercanas';
                    form.submit();
                },
                function () {
                    botonUbicacion.disabled = false;
                    botonUbicacion.classList.remove('opacity-70', 'cursor-not-allowed');
                    botonUbicacion.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .552-.224 1.052-.586 1.414A1.994 1.994 0 0110 13a2 2 0 100 4h4a2 2 0 100-4 1.994 1.994 0 01-1.414-.586A1.994 1.994 0 0112 11z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2m0 16v2m8-10h2M2 12H4m12.95 6.95l1.414 1.414M4.636 4.636L6.05 6.05m10.9-1.414L15.536 6.05M6.05 17.95l-1.414 1.414"></path>
                        </svg>
                        Usar mi ubicación para ordenar por cercanía
                    `;
                    alert('No se pudo obtener tu ubicación. Revisa los permisos del navegador.');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        });
    });
</script>
@endsection