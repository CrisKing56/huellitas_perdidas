@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h1 class="text-gray-500 uppercase tracking-wide text-sm font-semibold mb-4">VETERINARIAS EN OCOSINGO</h1>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            
            <div class="flex items-center gap-2 w-full md:max-w-xl">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm shadow-sm" 
                           placeholder="Buscar veterinarias...">
                </div>
                
                <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-full text-sm font-medium transition shadow-sm">
                    Refugios
                </button>
            </div>

            <a href="#" class="text-gray-600 hover:text-orange-500 text-sm font-medium flex items-center gap-1">
                Publicar veterinaria
                <span class="text-lg leading-none">+</span>
            </a>
        </div>

        <p class="text-gray-500 text-sm mb-6">{{ count($veterinarias) }} veterinarias encontradas</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($veterinarias as $vet)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition duration-300">
                
                <div class="relative h-48">
                    <img src="{{ $vet['imagen'] }}" alt="{{ $vet['nombre'] }}" class="w-full h-full object-cover">
                    
                    @if($vet['abierto'])
                        <span class="absolute top-4 left-4 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200 shadow-sm">
                            Abierto ahora
                        </span>
                    @else
                        <span class="absolute top-4 left-4 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full border border-red-200 shadow-sm">
                            Cerrado
                        </span>
                    @endif
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $vet['nombre'] }}</h3>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-start text-gray-500 text-sm">
                            <svg class="w-5 h-5 mr-2 text-orange-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="line-clamp-1">{{ $vet['direccion'] }}</span>
                        </div>

                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="w-5 h-5 mr-2 text-orange-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ $vet['telefono'] }}</span>
                        </div>

                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="w-5 h-5 mr-2 text-orange-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $vet['horario'] }}</span>
                        </div>
                    </div>

                    <a href="#" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center font-medium py-2.5 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        Ver detalles
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <button class="bg-white border border-gray-300 text-gray-700 font-medium px-8 py-2.5 rounded-full hover:bg-gray-50 transition shadow-sm">
                Cargar más
            </button>
        </div>

    </div>
</div>
@endsection