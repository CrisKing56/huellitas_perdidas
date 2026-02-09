@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-3xl p-6 shadow-sm mb-8 flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-1/2 h-64 md:h-80 relative rounded-2xl overflow-hidden group">
                <img src="{{ $veterinaria['imagen_principal'] }}" alt="Fachada" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                <span class="absolute top-4 left-4 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                    Veterinaria
                </span>
            </div>

            <div class="w-full md:w-1/2 flex flex-col justify-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $veterinaria['nombre'] }}</h1>
                <p class="text-gray-500 mb-4 text-lg">{{ $veterinaria['direccion_corta'] }}</p>

                <div class="flex items-center gap-2 mb-6">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-green-600 font-bold text-sm">Abierto ahora</span>
                    <span class="text-gray-400 text-sm">• Cierra a las 20:00</span>
                </div>

                <div class="flex flex-wrap gap-3 mb-6">
                    <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-bold shadow-sm transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Llamar
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-bold shadow-sm transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.968.585 1.96.89 3.03.89h.005c3.181 0 5.768-2.587 5.768-5.767 0-1.543-.604-2.99-1.597-3.983a5.714 5.714 0 00-4.041-1.693zm-3.328 3.01c.216-.48.483-.49.722-.497.165-.005.348-.002.502.002.19.006.444.072.678.537.243.483.829 2.03.901 2.176.071.147.118.318.016.513-.102.193-.153.313-.303.465-.128.13-.277.288-.385.397-.13.13-.266.27-.113.533.153.264.679 1.121 1.455 1.813 1.002.893 1.838 1.17 2.1 1.299.263.129.418.108.573-.07.155-.178.665-.773.843-1.037.178-.265.353-.22.592-.132.238.089 1.512.712 1.772.84.26.13.433.195.495.303.062.108.062.627-.154 1.24-.216.613-1.272 1.203-1.75 1.233-.448.028-.934.137-3.352-.82a10.05 10.05 0 01-3.664-3.134c-.167-.272-.647-1.125-.667-2.146-.022-1.066.568-1.59 0.77-1.815z"/></svg>
                        WhatsApp
                    </button>
                    <button class="bg-white border border-orange-500 text-orange-500 hover:bg-orange-50 px-6 py-3 rounded-xl font-bold shadow-sm transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Cómo llegar
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex text-yellow-400">
                        @for($i=0; $i<5; $i++) <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg> @endfor
                    </div>
                    <span class="font-bold text-gray-900 text-lg">4.8</span>
                    <span class="text-gray-500">(124 reseñas)</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Información General</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-orange-100 p-3 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Dirección</p>
                                <p class="text-gray-900 font-medium">{{ $veterinaria['direccion_completa'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-orange-100 p-3 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="w-full">
                                <p class="text-gray-500 text-sm mb-2">Horarios</p>
                                <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                    <span class="text-gray-700">Lunes - Viernes</span>
                                    <div class="text-right">
                                        <span class="font-bold text-gray-900">09:00 - 20:00</span>
                                        <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Abierto</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                    <span class="text-gray-700">Sábados</span>
                                    <span class="font-bold text-gray-900">10:00 - 14:00</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-700">Domingos</span>
                                    <span class="font-bold text-red-500">Cerrado</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-orange-100 p-3 rounded-full text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div class="w-full">
                                <p class="text-gray-500 text-sm mb-1">Teléfonos</p>
                                <p class="text-gray-900 font-bold text-lg mb-1">{{ $veterinaria['telefono'] }}</p>
                                <p class="text-gray-400 text-sm">Urgencias: {{ $veterinaria['telefono_urgencias'] }}</p>
                            </div>
                        </div>

                        <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-sm transition flex justify-center items-center gap-2">
                             <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.968.585 1.96.89 3.03.89h.005c3.181 0 5.768-2.587 5.768-5.767 0-1.543-.604-2.99-1.597-3.983a5.714 5.714 0 00-4.041-1.693zm-3.328 3.01c.216-.48.483-.49.722-.497.165-.005.348-.002.502.002.19.006.444.072.678.537.243.483.829 2.03.901 2.176.071.147.118.318.016.513-.102.193-.153.313-.303.465-.128.13-.277.288-.385.397-.13.13-.266.27-.113.533.153.264.679 1.121 1.455 1.813 1.002.893 1.838 1.17 2.1 1.299.263.129.418.108.573-.07.155-.178.665-.773.843-1.037.178-.265.353-.22.592-.132.238.089 1.512.712 1.772.84.26.13.433.195.495.303.062.108.062.627-.154 1.24-.216.613-1.272 1.203-1.75 1.233-.448.028-.934.137-3.352-.82a10.05 10.05 0 01-3.664-3.134c-.167-.272-.647-1.125-.667-2.146-.022-1.066.568-1.59 0.77-1.815z"/></svg>
                             Contactar por WhatsApp
                        </button>

                        <div class="h-48 bg-gray-200 rounded-xl overflow-hidden relative shadow-inner">
                            <img src="https://assets.website-files.com/6036a13217ba3f56d9539308/61153e7d5ad17145789f21f1_google-maps-placeholder.png" class="w-full h-full object-cover opacity-70">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="bg-white px-4 py-2 rounded-lg shadow-md font-bold text-gray-700 text-sm">Ver en mapa completo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Galería de Imágenes</h2>
                    <div class="relative rounded-2xl overflow-hidden h-72">
                         <img src="{{ $veterinaria['galeria'][0] }}" class="w-full h-full object-cover">
                         <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white p-3 rounded-full shadow-lg transition">
                             <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                         </button>
                         <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white p-3 rounded-full shadow-lg transition">
                             <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </button>
                         <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2">
                             <span class="w-6 h-1.5 bg-white rounded-full"></span>
                             <span class="w-2 h-1.5 bg-white/50 rounded-full"></span>
                             <span class="w-2 h-1.5 bg-white/50 rounded-full"></span>
                         </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Servicios Ofrecidos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @foreach($veterinaria['servicios'] as $servicio)
                        <div class="flex items-center gap-4 border border-gray-100 p-4 rounded-xl hover:shadow-md transition bg-gray-50/50">
                            <div class="bg-orange-100 p-3 rounded-full text-orange-500 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $servicio['nombre'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $servicio['descripcion'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button class="w-full border border-orange-500 text-orange-500 font-medium py-3 rounded-xl hover:bg-orange-50 transition">
                        Ver todos los servicios
                    </button>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Veterinarios Responsables</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($veterinaria['veterinarios'] as $vet)
                        <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
                            <img src="{{ $vet['foto'] }}" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $vet['nombre'] }}</h3>
                                <p class="text-orange-500 text-xs font-bold mb-2">{{ $vet['especialidad'] }}</p>
                                <p class="text-gray-400 text-[10px]">Cédula: {{ $vet['cedula'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Información Adicional</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="flex items-start gap-3">
                             <div class="bg-orange-100 p-2 rounded-full text-orange-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                             <div>
                                 <h4 class="font-bold text-sm">Horarios Extendidos</h4>
                                 <p class="text-xs text-gray-500 mt-1">Abierto de 9:00 a 20:00.</p>
                             </div>
                        </div>
                        <div class="flex items-start gap-3">
                             <div class="bg-orange-100 p-2 rounded-full text-orange-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                             <div>
                                 <h4 class="font-bold text-sm">Costos Estimados</h4>
                                 <p class="text-xs text-gray-500 mt-1">Consulta desde $350 MXN.</p>
                             </div>
                        </div>
                        <div class="flex items-start gap-3">
                             <div class="bg-orange-100 p-2 rounded-full text-orange-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg></div>
                             <div>
                                 <h4 class="font-bold text-sm">Convenios Refugios</h4>
                                 <p class="text-xs text-gray-500 mt-1">Descuentos en adopciones.</p>
                             </div>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-800 mb-4 text-sm">Servicios Destacados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8">
                        @foreach(['Microchip e identificación', 'Rayos X y ecografías', 'Odontología veterinaria', 'Análisis clínicos completos', 'Hospitalización', 'Medicina preventiva'] as $item)
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span class="text-gray-600 text-sm">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <button class="w-full mt-8 bg-orange-500 text-white font-medium py-3 rounded-xl hover:bg-orange-600 transition shadow-md">
                        Ver más servicios >
                    </button>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm sticky top-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Reseñas</h2>
                        <div class="flex items-center gap-1 text-yellow-400 font-bold">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span>4.8</span>
                        </div>
                    </div>

                    <button class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl mb-8 hover:bg-orange-600 transition shadow-sm flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        Escribir reseña
                    </button>

                    <div class="space-y-6">
                        @foreach($veterinaria['resenas'] as $resena)
                        <div class="border-b border-gray-100 pb-6 last:border-0">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold">
                                    {{ substr($resena['usuario'], 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">{{ $resena['usuario'] }}</h4>
                                    <div class="flex items-center gap-2">
                                        <div class="flex text-yellow-400 text-xs">
                                            @for($i=0; $i<5; $i++) <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg> @endfor
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $resena['fecha'] }}</span>
                                    </div>
                                </div>
                                <span class="ml-auto text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg></span>
                            </div>
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $resena['comentario'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <button class="w-full text-orange-500 font-medium text-sm mt-4 hover:underline">
                        Ver todas las reseñas (124)
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection