@extends('layout.app')

@section('content')

<div class="min-h-screen bg-gray-50 py-10 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-start mb-6">
            <div>
                <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full uppercase mb-2 tracking-wide">
                    {{ str_replace('_', ' ', $adopcion->estado) }}
                </span>
                
                <h1 class="text-4xl font-extrabold text-gray-900">{{ $adopcion->nombre }}</h1>
                <p class="text-gray-500 mt-1 text-lg">
                    {{ $adopcion->especie_id == 1 ? 'Perro' : 'Gato' }} 
                    @if($adopcion->otra_raza) - {{ $adopcion->otra_raza }} @endif
                </p>
            </div>
            
            <a href="{{ route('adopciones.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="relative w-full h-[500px] bg-gray-200 rounded-2xl overflow-hidden shadow-sm group">
                    @if($adopcion->fotoPrincipal)
                        <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}" alt="{{ $adopcion->nombre }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                            <svg class="w-20 h-20 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    
                    <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <div class="absolute bottom-4 left-4">
                        <button class="bg-white/90 hover:bg-white text-orange-500 text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Ver más fotos
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Información Básica</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div class="flex items-start gap-4">
                            <div class="bg-orange-50 p-2.5 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Edad</p>
                                <p class="text-gray-900 font-bold text-lg">{{ $adopcion->edad_anios }} años</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-orange-50 p-2.5 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Tamaño</p>
                                <p class="text-gray-900 font-bold text-lg">{{ ucfirst(strtolower($adopcion->tamano)) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-orange-50 p-2.5 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Sexo</p>
                                <p class="text-gray-900 font-bold text-lg">{{ ucfirst(strtolower($adopcion->sexo)) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-orange-50 p-2.5 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Color</p>
                                <p class="text-gray-900 font-bold text-lg">{{ $adopcion->color_predominante }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Descripción</h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $adopcion->descripcion }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Estado de Salud y Vacunas</h3>
                    
                    <div class="mb-6">
                        <p class="text-xs text-gray-400 font-bold uppercase mb-1">Estado de salud</p>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                            <p class="text-gray-900 font-bold text-lg">{{ $adopcion->condicion_salud ?? 'Saludable' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase mb-3">Vacunas aplicadas / Tratamientos</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2 text-gray-700">
                                <span class="text-orange-500 mt-1.5">•</span>
                                {{ $adopcion->vacunas_aplicadas ?? 'No especificadas' }}
                            </li>
                            <li class="flex items-start gap-2 text-gray-700">
                                <span class="text-orange-500 mt-1.5">•</span>
                                {{ $adopcion->esterilizado ? 'Esterilizado/Castrado' : 'No esterilizado' }}
                            </li>
                             @if($adopcion->descripcion_salud)
                                <li class="flex items-start gap-2 text-gray-700">
                                    <span class="text-orange-500 mt-1.5">•</span>
                                    {{ $adopcion->descripcion_salud }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Requisitos para el Adoptante</h3>
                    <ul class="space-y-3">
                         <li class="flex items-start gap-3 text-gray-700">
                            <div class="bg-orange-100 p-1 rounded-full mt-0.5 flex-shrink-0">
                                <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="leading-relaxed">{{ $adopcion->requisitos }}</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Información de Contacto</h3>
                    
                    <div class="flex items-start gap-4 mb-6">
                        <div class="bg-orange-50 p-2 rounded-full text-orange-500 mt-1">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Responsable</p>
                            <p class="font-bold text-gray-900 text-base leading-tight">{{ $adopcion->autor->nombre ?? 'Usuario' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <div class="bg-orange-50 p-2 rounded-full text-orange-500 mt-1">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Teléfono</p>
                            <p class="font-bold text-gray-900 text-base">{{ $adopcion->autor->telefono ?? 'No disponible' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <div class="bg-green-50 p-2 rounded-full text-green-500 mt-1">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">WhatsApp</p>
                            <p class="font-bold text-gray-900 text-base">{{ $adopcion->autor->telefono ?? 'No disponible' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-8">
                        <div class="bg-orange-50 p-2 rounded-full text-orange-500 mt-1">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase mb-0.5">Ubicación</p>
                            <p class="font-bold text-gray-900 text-base leading-tight">{{ $adopcion->colonia_barrio }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition">
                            Solicitar adopción
                        </button>
                        
                        @php
                            $telefono = $adopcion->autor->telefono ?? '0000000000';
                            $nombreDuenio = $adopcion->autor->name ?? 'Hola';
                            $mensaje = "Hola $nombreDuenio, me interesa adoptar a {$adopcion->nombre}.";
                            $whatsappUrl = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);
                        @endphp
                        
                        <a href="{{ $whatsappUrl }}" target="_blank" class="w-full flex justify-center items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition gap-2">
                            Contactar por WhatsApp
                        </a>
                        
                        <button class="w-full border border-orange-500 text-orange-500 hover:bg-orange-50 font-bold py-3 px-4 rounded-lg shadow-sm transition flex justify-center items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            Compartir
                        </button>
                    </div>

                    <p class="text-xs text-center text-gray-400 mt-4 leading-tight">
                        Al solicitar la adopción, recibirás instrucciones detalladas sobre el proceso y los documentos necesarios.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection