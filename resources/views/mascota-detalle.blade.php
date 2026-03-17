@extends('layout.app')

@section('content')

<style>
    #cartel-imprimible { display: none; }
    @media print {
        @page { margin: 0; size: auto; }
        body * { visibility: hidden; }
        #cartel-imprimible, #cartel-imprimible * { visibility: visible; }
        #cartel-imprimible {
            position: absolute; left: 0; top: 0; width: 100%; height: 100vh;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 20px; background: white; z-index: 9999;
        }
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                @php
                    $clasesEstado = [
                        'ACTIVA' => 'bg-red-100 text-red-600 border-red-200',
                        'ENCONTRADA' => 'bg-green-100 text-green-600 border-green-200',
                        'RESUELTA' => 'bg-blue-100 text-blue-600 border-blue-200',
                    ];
                    $clase = $clasesEstado[$publicacion->estado] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="{{ $clase }} border px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ $publicacion->estado }}
                </span>
                <span class="text-sm text-gray-500 font-medium">
                Publicado {{ $publicacion->created_at ? \Carbon\Carbon::parse($publicacion->created_at)->diffForHumans() : '' }}                </span>
            </div>
            
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $publicacion->nombre }}</h1>
            <p class="text-lg text-gray-500 mt-1 flex items-center gap-2">
                {{ $publicacion->especie_id == 1 ? 'Perro' : ($publicacion->especie_id == 2 ? 'Gato' : 'Mascota') }}
                @if($publicacion->raza)
                    <span class="w-1 h-1 bg-gray-400 rounded-full"></span> {{ $publicacion->raza }}
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="relative w-full h-96 bg-gray-200 rounded-2xl overflow-hidden shadow-sm border border-gray-100 group">
                    @if($publicacion->fotoPrincipal)
                        <img src="{{ asset('storage/' . $publicacion->fotoPrincipal->url) }}" alt="{{ $publicacion->nombre }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                            <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-lg font-medium">Sin fotografía disponible</span>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Información de la Mascota</h3>
                    
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Fecha de Extravío</p>
                                <p class="text-gray-900 font-semibold text-lg">
                                    {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->format('d \d\e F, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Último Avistamiento</p>
                                <p class="text-gray-900 font-semibold text-lg">{{ $publicacion->colonia_barrio }}</p>
                                @if($publicacion->calle_referencias)
                                    <p class="text-sm text-gray-500">{{ $publicacion->calle_referencias }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-4 border-t border-gray-100 pt-6">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Descripción y Detalles</p>
                                <p class="text-gray-700 text-base leading-relaxed mt-1">
                                    {{ $publicacion->descripcion }}
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium border border-gray-200">Sexo: {{ ucfirst($publicacion->sexo) }}</span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium border border-gray-200">Tamaño: {{ ucfirst($publicacion->tamano) }}</span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium border border-gray-200">Color: {{ ucfirst($publicacion->color) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Última ubicación conocida</h3>
                        
                        @if($publicacion->ubicacion)
                            <div id="mapa-detalle" style="width: 100%; height: 350px;" class="rounded shadow-md"></div>
                            <p class="text-sm text-gray-500 mt-2">Cerca de: {{ $publicacion->colonia_barrio }}</p>
                        @else
                            <p class="text-red-500">No hay coordenadas exactas para esta mascota.</p>
                        @endif
                    </div>

                    @if($publicacion->ubicacion)
                    <script>
                        function initMapDetalle() {
                            // Tomamos las coordenadas directamente de la base de datos usando Blade
                            const latitud = {{ $publicacion->ubicacion->latitud }};
                            const longitud = {{ $publicacion->ubicacion->longitud }};
                            const ubicacionMascota = { lat: latitud, lng: longitud };

                            // Creamos el mapa centrado en esa ubicación
                            const mapa = new google.maps.Map(document.getElementById('mapa-detalle'), {
                                zoom: 16,
                                center: ubicacionMascota,
                                mapTypeControl: false,
                                streetViewControl: false
                            });

                            // Colocamos el marcador FIJO (sin "draggable")
                            new google.maps.Marker({
                                position: ubicacionMascota,
                                map: mapa,
                                title: "{{ $publicacion->nombre }}"
                            });
                        }
                    </script>
                    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapDetalle" async defer></script>
                    @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Comentarios</h3>

                    <div class="mb-8">
                        <textarea class="w-full border border-gray-200 rounded-lg p-4 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none resize-none h-24 bg-gray-50" placeholder="Escribe un comentario si tienes información sobre esta mascota..."></textarea>
                        <div class="flex justify-end mt-2">
                            <button class="bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Comentar
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <div class="border border-gray-100 rounded-lg p-4 bg-gray-50/50">
                            <div class="flex gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-orange-400 text-white flex items-center justify-center text-xs font-bold shadow-sm">M</div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">María López <span class="text-xs text-gray-400 font-normal ml-2">hace 2 horas</span></p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 ml-11">Creo que vi a un perrito parecido cerca del parque central ayer por la tarde. ¿Tenía collar?</p>
                            <button class="ml-11 mt-2 text-xs text-orange-600 font-medium hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg> Responder
                            </button>
                        </div>

                        <div class="border border-gray-100 rounded-lg p-4 bg-white">
                            <div class="flex gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold shadow-sm">J</div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Juan Pérez <span class="text-xs text-gray-400 font-normal ml-2">hace 5 horas</span></p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 ml-11">Compartido en el grupo de vecinos de la colonia. ¡Espero que aparezca pronto!</p>
                             <button class="ml-11 mt-2 text-xs text-orange-600 font-medium hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg> Responder
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Información de Contacto</h3>
                    
                    <div class="flex items-center gap-4 mb-6 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            {{ substr($publicacion->autor->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Dueño</p>
                            <p class="font-bold text-gray-900">{{ $publicacion->autor->nombre ?? 'Usuario' }}</p>
                            <p class="text-xs text-gray-400">Miembro desde {{ $publicacion->autor->created_at ? $publicacion->autor->created_at->format('Y') : '2026' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mb-6 px-2">
                         <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                         </div>
                         <div>
                             <p class="text-xs text-gray-400">Teléfono</p>
                             <p class="text-sm font-semibold text-gray-800">{{ $publicacion->autor-> telefono ?? 'No visible' }}</p>
                         </div>
                    </div>

                    @php
                        $telefono = $publicacion->telefono ?? '529190000000';
                        $mensaje = "Hola, vi tu publicación de {$publicacion->nombre} en Huellitas Perdidas.";
                        $whatsappUrl = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);
                    @endphp

                    <a href="{{ $whatsappUrl }}" target="_blank" class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2 mb-4">
                        Contactar por WhatsApp
                    </a>
                    
                    <hr class="my-6 border-gray-100">

                    <p class="text-xs text-gray-500 mb-2 font-medium">Acciones</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="window.print()" class="flex items-center justify-center gap-2 py-2 px-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-600 text-sm font-medium transition border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Imprimir
                        </button>
                        <button class="flex items-center justify-center gap-2 py-2 px-4 bg-orange-50 hover:bg-orange-100 rounded-lg text-orange-600 text-sm font-medium transition border border-orange-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            Compartir
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="cartel-imprimible" class="font-sans text-center">
    <div class="border-b-4 border-red-600 pb-2 mb-2">
        <h1 class="text-5xl font-black text-red-600 uppercase tracking-tighter">¡SE BUSCA!</h1>
        <p class="text-xl text-gray-800 font-bold uppercase">Ayúdanos a encontrarlo</p>
    </div>
    <div class="flex-grow flex flex-col justify-center items-center overflow-hidden min-h-0">
        <div class="w-full h-full max-h-[45vh] flex justify-center mb-4">
            @if($publicacion->fotoPrincipal)
                <img src="{{ asset('storage/' . $publicacion->fotoPrincipal->url) }}" class="h-full w-auto object-contain border-4 border-gray-800 rounded-lg">
            @else
                <div class="h-64 w-64 bg-gray-200 flex items-center justify-center border-2 border-dashed border-gray-400 rounded-lg">
                    <span class="text-2xl text-gray-400 font-bold">Sin Foto</span>
                </div>
            @endif
        </div>
        <div class="w-full">
            <h2 class="text-4xl font-bold text-gray-900 leading-tight">{{ $publicacion->nombre }}</h2>
            <p class="text-xl text-gray-600 font-semibold mb-2">
                {{ $publicacion->especie_id == 1 ? 'Perro' : 'Gato' }} - {{ ucfirst($publicacion->raza ?? 'Raza desconocida') }}
            </p>
            <div class="flex justify-center gap-6 text-lg border-t border-b border-gray-300 py-2 mb-4 bg-gray-50">
                <div><span class="font-bold block text-xs text-gray-500">SEXO</span>{{ ucfirst($publicacion->sexo) }}</div>
                <div><span class="font-bold block text-xs text-gray-500">TAMAÑO</span>{{ ucfirst($publicacion->tamano) }}</div>
                <div><span class="font-bold block text-xs text-gray-500">COLOR</span>{{ ucfirst($publicacion->color) }}</div>
            </div>
            <div class="text-left px-4">
                <p class="text-lg mb-1"><strong class="text-red-600">📍 Visto en:</strong> {{ $publicacion->colonia_barrio }}</p>
                <p class="text-lg mb-2"><strong class="text-red-600">📅 Fecha:</strong> {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->format('d/m/Y') }}</p>
                <div class="bg-yellow-50 p-3 rounded border border-yellow-200 text-sm italic text-gray-700 line-clamp-3">"{{ $publicacion->descripcion }}"</div>
            </div>
        </div>
    </div>
    <div class="mt-4 pt-2 border-t-4 border-red-600">
        <p class="text-xl font-bold text-gray-800 uppercase">Si tienes información llama al:</p>
        <div class="bg-red-600 text-white rounded-xl py-2 px-4 mt-2 inline-block w-full">
            <h2 class="text-6xl font-black tracking-widest">{{ $publicacion->autor-> telefono }}</h2>
            <p class="text-lg font-medium">{{ $publicacion->autor->name ?? 'Contacto' }}</p>
        </div>
        <p class="mt-2 text-xs text-gray-400">Generado en HuellitasPerdidas.com</p>
    </div>
</div>
@endsection