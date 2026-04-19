@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mascotas perdidas</h1>
            <p class="text-sm text-gray-500 mt-1">Consulta reportes recientes y ayuda a encontrar a su familia.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            @guest
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Reportar mascota perdida
                </a>
            @endguest

            @auth
                <a href="{{ route('mascotas.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Reportar mascota perdida
                </a>

                <a href="{{ route('extravios.index') }}"
                   class="inline-flex items-center justify-center px-5 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition w-full md:w-auto">
                    Mis reportes
                </a>
            @endauth
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <aside class="lg:col-span-3">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)] flex flex-col overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Filtros</h2>
                    <a href="{{ route('mascotas.index2') }}" class="text-sm text-gray-400 hover:text-orange-500 transition">
                        Eliminar filtros
                    </a>
                </div>

                <form method="GET" action="{{ route('mascotas.index2') }}" class="flex flex-col min-h-0">
                    <input type="hidden" name="orden" value="{{ $filtros['orden'] ?? 'recientes' }}">

                    <div class="p-5 space-y-6 overflow-y-auto min-h-0 lg:max-h-[calc(100vh-15rem)]">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $filtros['q'] ?? '' }}"
                                    placeholder="Nombre, especie, raza o colonia..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 text-gray-700 placeholder-gray-400 transition"
                                >
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Estado</h3>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="estado" value="" {{ ($filtros['estado'] ?? '') === '' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                        <span class="text-gray-700">Todos</span>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $conteos['todas'] ?? 0 }}</span>
                                </label>

                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="estado" value="ACTIVA" {{ ($filtros['estado'] ?? '') === 'ACTIVA' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                        <span class="text-gray-700">Perdidas</span>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $conteos['activas'] ?? 0 }}</span>
                                </label>

                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="estado" value="RESUELTA" {{ ($filtros['estado'] ?? '') === 'RESUELTA' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                        <span class="text-gray-700">Encontradas</span>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $conteos['resueltas'] ?? 0 }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <label class="block text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Especie</label>
                            <select
                                name="especie"
                                onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 text-gray-700 transition"
                            >
                                <option value="">Todas las especies</option>
                                @foreach($especies as $especie)
                                    <option value="{{ $especie->id_especie }}" {{ ($filtros['especie'] ?? '') == $especie->id_especie ? 'selected' : '' }}>
                                        {{ $especie->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <label class="block text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Raza</label>
                            <select
                                name="raza"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 text-gray-700 transition"
                            >
                                <option value="">
                                    {{ ($filtros['especie'] ?? '') ? 'Todas las razas' : 'Primero selecciona una especie' }}
                                </option>

                                @foreach($razas as $raza)
                                    <option value="{{ $raza->id_raza }}" {{ ($filtros['raza'] ?? '') == $raza->id_raza ? 'selected' : '' }}>
                                        {{ $raza->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Sexo</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="sexo" value="" {{ ($filtros['sexo'] ?? '') === '' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Todos</span>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="sexo" value="MACHO" {{ ($filtros['sexo'] ?? '') === 'MACHO' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Macho</span>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="sexo" value="HEMBRA" {{ ($filtros['sexo'] ?? '') === 'HEMBRA' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Hembra</span>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="sexo" value="DESCONOCIDO" {{ ($filtros['sexo'] ?? '') === 'DESCONOCIDO' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Desconocido</span>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Tamaño</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="tamano" value="" {{ ($filtros['tamano'] ?? '') === '' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Todos</span>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="tamano" value="CHICO" {{ ($filtros['tamano'] ?? '') === 'CHICO' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Chico</span>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="tamano" value="MEDIANO" {{ ($filtros['tamano'] ?? '') === 'MEDIANO' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Mediano</span>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="tamano" value="GRANDE" {{ ($filtros['tamano'] ?? '') === 'GRANDE' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                    <span class="text-gray-700">Grande</span>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <label class="block text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Colonia</label>
                            <input
                                type="text"
                                name="colonia"
                                value="{{ $filtros['colonia'] ?? '' }}"
                                placeholder="Ej. Centro"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 text-gray-700 placeholder-gray-400 transition"
                            >
                        </div>
                    </div>

                    <div class="p-5 border-t border-gray-100 bg-white shrink-0">
                        <div class="flex flex-col gap-3">
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition shadow-sm">
                                Aplicar filtros
                            </button>

                            <a href="{{ route('mascotas.index2') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-xl transition">
                                Ver todas
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        <section class="lg:col-span-9">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $mascotas->total() }} resultados</h2>
                    <p class="text-sm text-gray-500 mt-1">Explora las publicaciones y filtra por características.</p>
                </div>

                <form method="GET" action="{{ route('mascotas.index2') }}" class="w-full md:w-auto">
                    <input type="hidden" name="q" value="{{ $filtros['q'] ?? '' }}">
                    <input type="hidden" name="estado" value="{{ $filtros['estado'] ?? '' }}">
                    <input type="hidden" name="especie" value="{{ $filtros['especie'] ?? '' }}">
                    <input type="hidden" name="raza" value="{{ $filtros['raza'] ?? '' }}">
                    <input type="hidden" name="sexo" value="{{ $filtros['sexo'] ?? '' }}">
                    <input type="hidden" name="tamano" value="{{ $filtros['tamano'] ?? '' }}">
                    <input type="hidden" name="colonia" value="{{ $filtros['colonia'] ?? '' }}">

                    <select name="orden"
                            onchange="this.form.submit()"
                            class="w-full md:w-auto px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="recientes" {{ ($filtros['orden'] ?? 'recientes') === 'recientes' ? 'selected' : '' }}>Ordenar por: Más recientes</option>
                        <option value="antiguos" {{ ($filtros['orden'] ?? '') === 'antiguos' ? 'selected' : '' }}>Ordenar por: Más antiguos</option>
                        <option value="nombre_az" {{ ($filtros['orden'] ?? '') === 'nombre_az' ? 'selected' : '' }}>Ordenar por: Nombre A-Z</option>
                        <option value="nombre_za" {{ ($filtros['orden'] ?? '') === 'nombre_za' ? 'selected' : '' }}>Ordenar por: Nombre Z-A</option>
                    </select>
                </form>
            </div>

            @if($mascotas->isEmpty())
                <div class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-100 border-dashed">
                    <p class="text-gray-500 text-lg">No hay mascotas que coincidan con los filtros seleccionados.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($mascotas as $mascota)
                        @php
                            $esEncontrada = in_array($mascota->estado, ['ENCONTRADA', 'RESUELTA']);
                            $bgClass = $esEncontrada ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600';
                            $textoEstado = $esEncontrada ? 'Encontrada' : 'Perdida';

                            $razaVisible = $mascota->otra_raza ?: ($mascota->raza_nombre ?? null);
                            $especieVisible = $mascota->especie_nombre ?: 'Sin especie';
                        @endphp

                        <a href="{{ route('extravios.show', $mascota->id_publicacion) }}">
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">

                                <div class="h-64 relative overflow-hidden bg-gray-100">
                                    @if($mascota->fotoPrincipal)
                                        <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                            <span class="text-sm">Sin imagen</span>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent"></div>

                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold shadow-sm uppercase tracking-wide {{ $bgClass }}">
                                        {{ $textoEstado }}
                                    </span>

                                    <div class="absolute bottom-3 right-3 flex items-center gap-2 px-3 py-2 rounded-xl bg-black/20 backdrop-blur-[2px]">
                                        <img src="{{ asset('img/logo1.png') }}"
                                             alt="Huellitas Perdidas"
                                             class="h-7 w-7 object-contain brightness-0 invert opacity-90">
                                        <span class="text-white/90 text-xs font-bold tracking-wide">
                                            Huellitas Perdidas
                                        </span>
                                    </div>
                                </div>

                                <div class="p-4 flex-1 flex flex-col">
                                    <div class="mb-2 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-1 text-[11px] font-semibold text-orange-600 border border-orange-100">
                                            {{ $especieVisible }}
                                        </span>

                                        @if($mascota->sexo && $mascota->sexo !== 'DESCONOCIDO')
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600 border border-gray-200">
                                                {{ ucfirst(strtolower($mascota->sexo)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-bold text-gray-900 text-base mb-1">{{ $mascota->nombre }}</h3>

                                    <p class="text-sm text-gray-500 mb-3 leading-relaxed">
                                        @if($razaVisible)
                                            {{ $razaVisible }}
                                        @else
                                            {{ \Illuminate\Support\Str::limit($mascota->descripcion, 60) }}
                                        @endif
                                    </p>

                                    <div class="mt-auto flex items-center text-gray-500 text-xs font-medium mb-4">
                                        <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $mascota->colonia_barrio }}
                                    </div>

                                    <div class="pt-3 border-t border-gray-100">
                                        <span class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-50 text-orange-600 font-semibold py-2.5 border border-orange-100 group-hover:bg-orange-500 group-hover:text-white group-hover:border-orange-500 transition">
                                            Ver detalle
                                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($mascotas->hasPages())
                    <div class="mt-8">
                        {{ $mascotas->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>
</div>
@endsection