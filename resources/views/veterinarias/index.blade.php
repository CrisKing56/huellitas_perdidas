@extends('layout.app')

@section('content')
@php
    $totalVeterinarias = method_exists($veterinarias, 'total')
        ? $veterinarias->total()
        : count($veterinarias ?? []);
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
                    Encuentra veterinarias registradas en la plataforma para consultar información, ubicación y medios de contacto.
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

        {{-- Barra superior estilo consistente --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">
                        <span id="contador-veterinarias">{{ $totalVeterinarias }}</span> veterinarias encontradas
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Explora perfiles institucionales disponibles dentro del directorio.
                    </p>
                </div>

                <div class="w-full lg:max-w-md">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>

                        <input
                            type="text"
                            id="buscador-veterinarias"
                            placeholder="Buscar por nombre, dirección, ciudad o descripción..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                        >
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid de cards --}}
        <div id="grid-veterinarias" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse(($veterinarias ?? []) as $vet)
                @php
                    $direccion = collect([
                        $vet->calle_numero ?? null,
                        $vet->colonia ?? null,
                        $vet->ciudad ?? null,
                    ])->filter()->implode(', ');

                    $imagen = !empty($vet->imagen)
                        ? asset('storage/' . ltrim($vet->imagen, '/'))
                        : 'https://images.unsplash.com/photo-1581888227599-779811939961?auto=format&fit=crop&w=900&q=60';

                    $textoBusqueda = strtolower(
                        trim(
                            ($vet->nombre ?? '') . ' ' .
                            ($direccion ?? '') . ' ' .
                            ($vet->descripcion ?? '') . ' ' .
                            ($vet->telefono ?? '')
                        )
                    );
                @endphp

                <article
                    class="card-veterinaria bg-white rounded-3xl shadow-sm hover:shadow-lg transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full"
                    data-search="{{ $textoBusqueda }}"
                >
                    <div class="relative h-56 overflow-hidden bg-gray-100">
                        <img src="{{ $imagen }}"
                             alt="{{ $vet->nombre }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-105">

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
                        <div class="mb-3">
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                {{ $vet->nombre }}
                            </h3>
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
                    No hay veterinarias registradas por el momento.
                </div>
            @endforelse
        </div>

        {{-- Estado vacío del buscador --}}
        <div id="sin-resultados" class="hidden mt-8 bg-white rounded-3xl border border-dashed border-gray-200 p-10 text-center text-gray-500">
            No se encontraron veterinarias con ese criterio de búsqueda.
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
        const buscador = document.getElementById('buscador-veterinarias');
        const cards = Array.from(document.querySelectorAll('.card-veterinaria'));
        const contador = document.getElementById('contador-veterinarias');
        const vacio = document.getElementById('sin-resultados');

        if (!buscador || !cards.length || !contador) return;

        buscador.addEventListener('input', function () {
            const termino = this.value.toLowerCase().trim();
            let visibles = 0;

            cards.forEach(card => {
                const texto = card.dataset.search || '';
                const coincide = texto.includes(termino);

                card.style.display = coincide ? '' : 'none';

                if (coincide) visibles++;
            });

            contador.textContent = visibles;

            if (vacio) {
                vacio.classList.toggle('hidden', visibles > 0);
            }
        });
    });
</script>
@endsection