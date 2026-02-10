@extends('layout.app')

@section('title', 'Mascotas en Adopción')

@section('content')
<div class="container mx-auto px-6 py-8">
    
    <h1 class="text-2xl font-bold text-gray-900 mb-6 uppercase">Mascotas en Adopción</h1>

    <form action="{{ route('adopciones.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 mb-8">
        
        <div class="relative flex-grow">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   class="w-full py-2.5 pl-10 pr-4 text-gray-700 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent shadow-sm placeholder-gray-400" 
                   placeholder="Buscar mascotas...">
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:w-auto">
            <select name="especie" class="w-full lg:w-40 py-2.5 px-3 bg-white border border-gray-200 rounded-lg text-gray-600 text-sm focus:outline-none focus:border-green-500 cursor-pointer shadow-sm">
                <option value="">Especie</option>
                <option value="1">Perro</option>
                <option value="2">Gato</option>
            </select>

            <button type="submit" class="w-full lg:w-auto px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-sm">
                Filtrar
            </button>
        </div>
    </form>

    <div class="flex justify-between items-center mb-8">

        {{-- ✅ FIX: si está logueado va a publicar, si no está logueado va a login --}}
        @auth
            <a href="{{ route('adopciones.create') }}" class="text-sm font-medium text-gray-800 hover:text-green-600 transition flex items-center gap-1 group">
                Publicar mascota en adopción 
                <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold group-hover:bg-green-600 group-hover:text-white transition">+</span>
            </a>
        @endauth

        @guest
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-800 hover:text-green-600 transition flex items-center gap-1 group">
                Inicia sesión para publicar
                <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold group-hover:bg-green-600 group-hover:text-white transition">+</span>
            </a>
        @endguest

        <span class="text-sm text-gray-500">{{ $adopciones->total() }} mascotas encontradas</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        @forelse($adopciones as $adopcion)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col group h-full">
                <a href="{{route('adopciones.show', $adopcion->id_publicacion)}}">                        
                       
                <div class="relative h-64 overflow-hidden">

                    @if($adopcion->fotoPrincipal)
                        <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    
                    <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                        {{ str_replace('_', ' ', $adopcion->estado) }}
                    </span>
                    
                    <button class="absolute top-3 right-3 bg-white p-2 rounded-full text-gray-400 hover:text-red-500 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </button>
                </div>
                </a>  

                <div class="p-5 flex-grow flex flex-col">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="font-bold text-gray-900 text-lg truncate pr-2">{{ $adopcion->nombre }}</h3>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $adopcion->edad_anios }} años</span>
                    </div>
                    
                    <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">
                        {{ $adopcion->especie_id == 1 ? 'Perro' : 'Gato' }}
                        @if($adopcion->otra_raza) • {{ Str::limit($adopcion->otra_raza, 15) }} @endif
                    </p>
                    
                    <p class="text-sm text-gray-600 mb-4 flex-grow line-clamp-2">
                        {{ $adopcion->descripcion }}
                    </p>
                    
                    <div class="flex items-center gap-1 text-xs text-gray-500 mb-6">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="truncate">{{ $adopcion->colonia_barrio }}</span>
                    </div>
                    
                    <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}" class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-green-600 transition group mt-auto pt-4 border-t border-gray-50">
                        ¡Adoptar!
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>

                </div>

            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-12">
                <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No hay mascotas disponibles</h3>
                <p class="text-gray-500 mt-2">Actualmente no tenemos mascotas en adopción que coincidan con tu búsqueda.</p>
            </div>
        @endforelse

    </div>

    <div class="mt-10">
        {{ $adopciones->links() }}
    </div>

</div>
@endsection
