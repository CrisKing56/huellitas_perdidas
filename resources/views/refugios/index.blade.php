@extends('layout.app')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Refugios y Albergues</h1>
            <p class="text-gray-500 mt-2">Conoce a las organizaciones que dedican su vida a rescatar peluditos.</p>
            <a href="{{ route('registro.refugio') }}"
               class="text-gray-600 hover:text-orange-500 text-sm font-medium flex items-center gap-1">
                Subir Refugio
                <span class="text-lg leading-none">+</span>
            </a>
        </div>

        @if($refugios->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($refugios as $refugio)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                        
                        <div class="h-48 bg-gray-200 relative overflow-hidden group">
                            @if($refugio->fotos->count() > 0)
                                <img src="{{ asset('storage/' . $refugio->fotos->first()->url) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                            @endif
                            <div class="absolute bottom-3 left-3">
                                <span class="px-3 py-1 text-xs font-bold bg-gray-900/80 text-white rounded-full shadow-sm flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                    {{ $refugio->direccion->ciudad }}, {{ $refugio->direccion->estado }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $refugio->nombre }}</h2>
                            <p class="text-gray-600 text-sm flex-1 line-clamp-3 mb-4">{{ $refugio->descripcion }}</p>
                            
                            <a href="{{ route('refugios.show', $refugio->id_organizacion) }}" class="w-full text-center bg-orange-50 text-orange-600 font-semibold py-2 rounded-lg hover:bg-orange-100 transition">
                                Ver perfil del refugio
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">{{ $refugios->links() }}</div>
        @else
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <p class="text-gray-500">Aún no hay refugios registrados en la plataforma.</p>
            </div>
        @endif

    </div>

@endsection
