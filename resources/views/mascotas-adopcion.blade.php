@extends('layout.app')

@section('title', 'Mascotas en Adopción')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mascotas en adopción</h1>
            <p class="text-sm text-gray-500 mt-1">Encuentra compañeros que buscan un hogar lleno de amor.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            @guest
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-sm transition w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Inicia sesión para publicar
                </a>
            @endguest

            @auth
                <a href="{{ route('adopciones.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-sm transition w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Publicar mascota en adopción
                </a>

                <a href="{{ route('adopciones.mis-adopciones') }}"
                   class="inline-flex items-center justify-center px-5 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition w-full md:w-auto">
                    Mis adopciones
                </a>
            @endauth
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- SIDEBAR DE FILTROS --}}
        <aside class="lg:col-span-3">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 lg:sticky lg:top-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Filtros</h2>
                    <a href="{{ route('adopciones.index') }}" class="text-sm text-gray-400 hover:text-green-600 transition">
                        Eliminar filtros
                    </a>
                </div>

                <form method="GET" action="{{ route('adopciones.index') }}" class="space-y-6">
                    <input type="hidden" name="orden" value="{{ $filtros['orden'] }}">

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
                                value="{{ $filtros['q'] }}"
                                placeholder="Nombre, descripción, raza o colonia..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 text-gray-700 placeholder-gray-400 transition"
                            >
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Disponibles</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Mascotas publicadas</span>
                            <span class="text-sm text-gray-400">{{ $conteos['disponibles'] }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Especie</h3>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="especie" value="" {{ $filtros['especie'] === '' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-gray-700">Todas</span>
                                </div>
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="especie" value="1" {{ $filtros['especie'] === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-gray-700">Perros</span>
                                </div>
                                <span class="text-sm text-gray-400">{{ $conteos['perros'] }}</span>
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="especie" value="2" {{ $filtros['especie'] === '2' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-gray-700">Gatos</span>
                                </div>
                                <span class="text-sm text-gray-400">{{ $conteos['gatos'] }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Sexo</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sexo" value="" {{ $filtros['sexo'] === '' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Todos</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sexo" value="MACHO" {{ $filtros['sexo'] === 'MACHO' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Macho</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sexo" value="HEMBRA" {{ $filtros['sexo'] === 'HEMBRA' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Hembra</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="sexo" value="DESCONOCIDO" {{ $filtros['sexo'] === 'DESCONOCIDO' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Desconocido</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Tamaño</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="tamano" value="" {{ $filtros['tamano'] === '' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Todos</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="tamano" value="CHICO" {{ $filtros['tamano'] === 'CHICO' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Chico</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="tamano" value="MEDIANO" {{ $filtros['tamano'] === 'MEDIANO' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Mediano</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="tamano" value="GRANDE" {{ $filtros['tamano'] === 'GRANDE' ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">Grande</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <label class="block text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Colonia</label>
                        <input
                            type="text"
                            name="colonia"
                            value="{{ $filtros['colonia'] }}"
                            placeholder="Ej. Centro"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 text-gray-700 placeholder-gray-400 transition"
                        >
                    </div>

                    <div class="border-t border-gray-100 pt-5 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition shadow-sm">
                            Aplicar filtros
                        </button>

                        <a href="{{ route('adopciones.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-xl transition">
                            Ver todas
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        {{-- CONTENIDO --}}
        <section class="lg:col-span-9">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $adopciones->total() }} resultados</h2>
                    <p class="text-sm text-gray-500 mt-1">Explora mascotas en adopción y encuentra a tu nuevo compañero.</p>
                </div>

                <form method="GET" action="{{ route('adopciones.index') }}" class="w-full md:w-auto">
                    <input type="hidden" name="q" value="{{ $filtros['q'] }}">
                    <input type="hidden" name="especie" value="{{ $filtros['especie'] }}">
                    <input type="hidden" name="sexo" value="{{ $filtros['sexo'] }}">
                    <input type="hidden" name="tamano" value="{{ $filtros['tamano'] }}">
                    <input type="hidden" name="colonia" value="{{ $filtros['colonia'] }}">

                    <select name="orden"
                            onchange="this.form.submit()"
                            class="w-full md:w-auto px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="recientes" {{ $filtros['orden'] === 'recientes' ? 'selected' : '' }}>Ordenar por: Más recientes</option>
                        <option value="antiguos" {{ $filtros['orden'] === 'antiguos' ? 'selected' : '' }}>Ordenar por: Más antiguos</option>
                        <option value="nombre_az" {{ $filtros['orden'] === 'nombre_az' ? 'selected' : '' }}>Ordenar por: Nombre A-Z</option>
                        <option value="nombre_za" {{ $filtros['orden'] === 'nombre_za' ? 'selected' : '' }}>Ordenar por: Nombre Z-A</option>
                    </select>
                </form>
            </div>

            @if($adopciones->isEmpty())
                <div class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-100 border-dashed">
                    <p class="text-gray-500 text-lg">No hay mascotas en adopción que coincidan con los filtros seleccionados.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($adopciones as $adopcion)
                        <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}">
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">
                                
                                <div class="h-64 relative overflow-hidden bg-gray-100">
                                    @if($adopcion->fotoPrincipal)
                                        <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                            <span class="text-sm">Sin imagen</span>
                                        </div>
                                    @endif

                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold shadow-sm uppercase tracking-wide bg-green-100 text-green-700">
                                        {{ ucfirst(strtolower(str_replace('_', ' ', $adopcion->estado))) }}
                                    </span>

                                    <button class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center bg-white/80 rounded-full hover:bg-white text-gray-600 transition backdrop-blur-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-4 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-1 gap-2">
                                        <h3 class="font-bold text-gray-900 text-base">{{ $adopcion->nombre }}</h3>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded whitespace-nowrap">
                                            {{ $adopcion->edad_anios ?? 'N/D' }} años
                                        </span>
                                    </div>
                                    
                                    <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">
                                        {{ $adopcion->especie_id == 1 ? 'Perro' : 'Gato' }}
                                        @if($adopcion->otra_raza)
                                            • {{ \Illuminate\Support\Str::limit($adopcion->otra_raza, 18) }}
                                        @endif
                                    </p>
                                    
                                    <p class="text-sm text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($adopcion->descripcion, 95) }}
                                    </p>
                                    
                                    <div class="mt-auto flex items-center text-gray-500 text-xs font-medium mb-4">
                                        <svg class="w-4 h-4 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $adopcion->colonia_barrio }}
                                    </div>

                                    <div class="pt-3 border-t border-gray-100">
                                        <span class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-green-50 text-green-600 font-semibold py-2.5 border border-green-100 group-hover:bg-green-600 group-hover:text-white group-hover:border-green-600 transition">
                                            ¡Adoptar!
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

                @if($adopciones->hasPages())
                    <div class="mt-8">
                        {{ $adopciones->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>
</div>
@endsection