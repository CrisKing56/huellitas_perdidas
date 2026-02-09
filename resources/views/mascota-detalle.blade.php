@extends('layout.app')

@section('title', 'Detalle de ' . $publicacion->nombre)

@section('content')
<div class="container mx-auto px-6 py-8">

    <div class="mb-6">
        @php
            $colores = [
                'ACTIVA' => 'bg-red-100 text-red-500',
                'ENCONTRADA' => 'bg-green-100 text-green-500',
                'REVISION' => 'bg-yellow-100 text-yellow-500',
            ];
            $claseEstado = $colores[$publicacion->estado] ?? 'bg-gray-100 text-gray-500';
        @endphp
        <span class="{{ $claseEstado }} text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
            {{ $publicacion->estado }}
        </span>
        
        <h1 class="text-4xl font-bold text-gray-900 mt-2">{{ $publicacion->nombre }}</h1>
        <p class="text-gray-500 text-lg">
            {{-- Lógica simple para mostrar la especie --}}
            {{ $publicacion->especie_id == 1 ? 'Perro' : ($publicacion->especie_id == 2 ? 'Gato' : 'Mascota') }}
            
            @if($publicacion->raza)
                - {{ $publicacion->raza }}
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="relative w-full h-96 bg-gray-200 rounded-2xl overflow-hidden group shadow-lg">
                @if($publicacion->fotoPrincipal)
                    <img src="{{ asset('storage/' . $publicacion->fotoPrincipal->url) }}" alt="{{ $publicacion->nombre }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                        <span class="text-lg">Sin fotografía</span>
                    </div>
                @endif
                
                </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Información de la Mascota</h3>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Fecha de Extravío</p>
                            <p class="text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->format('d \d\e F, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Zona del Extravío</p>
                            <p class="text-gray-900 font-medium">
                                {{ $publicacion->colonia_barrio }}
                                @if($publicacion->calle_referencias)
                                    <br><span class="text-sm text-gray-500 font-normal">{{ $publicacion->calle_referencias }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Tamaño</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($publicacion->tamano) }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Sexo</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($publicacion->sexo) }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Color</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($publicacion->color) }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 border-t border-gray-100 pt-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Descripción</p>
                            <p class="text-gray-700 text-sm leading-relaxed mt-1">
                                {{ $publicacion->descripcion }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Ubicación Aproximada</h3>
                
                <div class="relative w-full h-48 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                   <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                </div>

                <div class="mt-4 text-center">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($publicacion->colonia_barrio . ' ' . $publicacion->calle_referencias . ' Ocosingo Chiapas') }}" target="_blank" class="text-orange-500 text-sm font-medium hover:underline flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        Ver ubicación en Google Maps
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 opacity-75">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Comentarios</h3>
                <p class="text-sm text-gray-500 mb-4">La sección de comentarios estará disponible próximamente.</p>
                <textarea disabled class="w-full border border-gray-200 rounded-lg p-4 text-sm bg-gray-50 cursor-not-allowed resize-none h-24" placeholder="No hemos puesto los comentarios jiji"></textarea>
            </div>

        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Información de Contacto</h3>
                
                <div class="flex items-center gap-4 mb-6 p-3 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-lg">
                        {{ substr($publicacion->autor->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Publicado por</p>
                        <p class="font-bold text-gray-900">{{ $publicacion->autor->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-gray-400">
                            Miembro desde {{ $publicacion->autor->created_at ? $publicacion->autor->created_at->format('Y') : '2026' }}
                        </p>
                    </div>
                </div>

                @php
                    $telefono = $publicacion->telefono ?? '529190000000'; // Reemplaza esto con el dato real si existe
                    $mensaje = "Hola, vi tu publicación de {$publicacion->nombre} en Huellitas Perdidas.";
                    $whatsappUrl = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);
                @endphp

                <a href="{{ $whatsappUrl }}" target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow transition flex justify-center items-center gap-2 mb-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.897.003-6.171 5.02-11.192 11.196-11.192 3.029.003 5.86 1.187 7.968 3.326 2.148 2.151 3.308 5.015 3.298 8.019-.023 6.16-5.068 11.168-11.246 11.168-.96 0-1.977-.145-2.999-.467L.057 24zm2.233-4.232l.54.321c2.119 1.259 4.384 1.332 5.166 1.325.292-.003.52-.027.653-.041l.36-.039c3.344-.366 5.923-3.084 6.273-6.613l.014-.146c.009-.092.015-.179.015-.275.02-2.316-.922-4.494-2.656-6.131-1.696-1.602-3.926-2.486-6.279-2.488-5.023 0-9.109 4.087-9.112 9.111-.002 1.839.544 3.593 1.58 5.111l.325.48-1.085 3.961 4.206-1.125z"></path></svg>
                    Contactar por WhatsApp
                </a>

                <hr class="my-6 border-gray-100">

                <div class="grid grid-cols-1 gap-3">
                    <button onclick="window.print()" class="flex items-center justify-center gap-2 py-2 px-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-600 text-sm font-medium transition border border-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir Cartel
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection