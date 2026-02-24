@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    
    <div class="mb-8">
        @guest
        <a href="{{ route('login')}}">
            <h2 class="text-xl font-medium text-gray-700 mb-4">Reportar mascota perdida</h2>
        </a>

        @endguest

        @auth
        <a href="{{ route('mascotas.create')}}">
            <h2 class="text-xl font-medium text-gray-700 mb-4">Reportar mascota perdida</h2>
        </a>
        @endauth
        
        
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="relative w-full md:max-w-xl">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Buscar mascotas..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 text-gray-700 placeholder-gray-400 transition">
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <button class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition shadow-sm">
                    Ver todas
                </button>
            @auth
                <a href="{{ route('extravios.index')}}" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition text-center whitespace-nowrap">
                    Mis reportes
                </a>
            @endauth
            </div>
        </div>
    </div>

    @if($mascotas->isEmpty())
        <div class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-100 border-dashed">
            <p class="text-gray-500 text-lg">No hay mascotas reportadas actualmente.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($mascotas as $mascota)
            <a href="{{route('extravios.show', $mascota->id_publicacion)}}">
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">
                
                <div class="h-64 relative overflow-hidden bg-gray-100">
                    @if($mascota->fotos->count() > 0)
                        <img src="{{ asset('storage/' . $mascota->fotos->first()->url) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                            <span class="text-sm">Sin imagen</span>
                        </div>
                    @endif

                    @php
                        $esEncontrada = in_array($mascota->estado, ['Encontrada', 'Resuelta']);
                        $bgClass = $esEncontrada ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600';
                        $textoEstado = $esEncontrada ? 'Encontrada' : 'Perdida';
                    @endphp
                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold shadow-sm uppercase tracking-wide {{ $bgClass }}">
                        {{ $textoEstado }}
                    </span>

                    <button class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center bg-white/80 rounded-full hover:bg-white text-gray-600 transition backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                    </button>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-900 text-base mb-1">{{ $mascota->nombre }}</h3>
                    
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                        {{ $mascota->descripcion }}
                    </p>
                    
                    <div class="mt-auto flex items-center text-gray-500 text-xs font-medium mb-4">
                        <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $mascota->colonia_barrio }}
                    </div>

                    <div class="flex items-center text-gray-400 text-xs pt-3 border-t border-gray-100">
                        <button class="flex items-center gap-1 hover:text-red-500 transition group/like">
                            <svg class="w-5 h-5 group-hover/like:fill-red-500 group-hover/like:text-red-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <span>24</span> </button>
                    </div>
                </div>
            </div>
            </a>
            
            
            @endforeach
        </div>
    @endif
</div>
@endsection