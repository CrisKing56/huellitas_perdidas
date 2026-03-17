@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $fotoPrincipal = $fotos->first();
            $whatsNumero = preg_replace('/\D/', '', $veterinaria->whatsapp ?? $veterinaria->telefono ?? '');
            $mensajeWhatsapp = "Hola, vi la información de {$veterinaria->nombre} en Huellitas Perdidas y me gustaría pedir más información.";
            $whatsappUrl = $whatsNumero ? "https://wa.me/52{$whatsNumero}?text=" . urlencode($mensajeWhatsapp) : null;

            $dias = [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
                7 => 'Domingo',
            ];

            $direccionCompleta = collect([
                $veterinaria->calle_numero,
                $veterinaria->colonia,
                $veterinaria->codigo_postal ? 'CP ' . $veterinaria->codigo_postal : null,
                $veterinaria->ciudad,
                $veterinaria->estado_direccion,
            ])->filter()->implode(', ');
        @endphp

        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-green-100 text-green-700 border border-green-200 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    Veterinaria
                </span>
                <span class="text-sm text-gray-500 font-medium">
                    Información verificada en Huellitas Perdidas
                </span>
            </div>

            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $veterinaria->nombre }}</h1>
            <p class="text-lg text-gray-500 mt-1">
                {{ $direccionCompleta ?: 'Dirección no disponible' }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-8">

                <div class="relative w-full h-96 bg-gray-200 rounded-2xl overflow-hidden shadow-sm border border-gray-100 group">
                    @if($fotoPrincipal)
                        <img
                            src="{{ asset('storage/' . $fotoPrincipal->url) }}"
                            alt="{{ $veterinaria->nombre }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        >
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                            <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-lg font-medium">Sin fotografía disponible</span>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Información General</h3>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Dirección</p>
                                <p class="text-gray-900 font-semibold text-lg">{{ $direccionCompleta ?: 'No registrada' }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Teléfono</p>
                                <p class="text-gray-900 font-semibold text-lg">{{ $veterinaria->telefono }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4 border-t border-gray-100 pt-6">
                            <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Descripción</p>
                                <p class="text-gray-700 text-base leading-relaxed mt-1">
                                    {{ $veterinaria->descripcion }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium border border-gray-200">
                                        Médico responsable: {{ $veterinaria->medico_responsable }}
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium border border-gray-200">
                                        Cédula: {{ $veterinaria->cedula_profesional }}
                                    </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium border border-gray-200">
                                        Veterinarios: {{ $veterinaria->num_veterinarios ?? 'No registrado' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Horarios de atención</h3>

                    @if($horarios->isEmpty())
                        <p class="text-gray-500">No hay horarios registrados.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($horarios as $horario)
                                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                                    <p class="text-sm text-gray-500 font-medium">{{ $dias[$horario->dia_semana] ?? 'Día' }}</p>

                                    @if($horario->cerrado)
                                        <p class="text-red-500 font-semibold mt-1">Cerrado</p>
                                    @else
                                        <p class="text-gray-900 font-semibold mt-1">
                                            {{ \Carbon\Carbon::parse($horario->hora_apertura)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($horario->hora_cierre)->format('H:i') }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($veterinaria->latitud && $veterinaria->longitud)
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Ubicación</h3>
                        <div id="mapa-veterinaria" style="width: 100%; height: 350px;" class="rounded shadow-md"></div>
                        <p class="text-sm text-gray-500 mt-2">{{ $direccionCompleta }}</p>
                    </div>

                    <script>
                        function initMapVeterinaria() {
                            const ubicacion = {
                                lat: {{ $veterinaria->latitud }},
                                lng: {{ $veterinaria->longitud }}
                            };

                            const mapa = new google.maps.Map(document.getElementById('mapa-veterinaria'), {
                                zoom: 16,
                                center: ubicacion,
                                mapTypeControl: false,
                                streetViewControl: false
                            });

                            new google.maps.Marker({
                                position: ubicacion,
                                map: mapa,
                                title: @json($veterinaria->nombre)
                            });
                        }
                    </script>
                    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapVeterinaria" async defer></script>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Galería de imágenes</h3>

                    @if($fotos->isEmpty())
                        <p class="text-gray-500">No hay imágenes registradas.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($fotos as $foto)
                                <div class="overflow-hidden rounded-xl border border-gray-100 shadow-sm bg-gray-50">
                                    <img
                                        src="{{ asset('storage/' . $foto->url) }}"
                                        alt="Foto de {{ $veterinaria->nombre }}"
                                        class="w-full h-52 object-cover hover:scale-105 transition duration-500"
                                    >
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Servicios ofrecidos</h3>

                    @if($servicios->isEmpty())
                        <p class="text-gray-500">No hay servicios registrados.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($servicios as $servicio)
                                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                                    <p class="font-semibold text-gray-900">{{ $servicio }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($veterinaria->otros_servicios))
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <p class="text-sm text-gray-500 font-medium mb-1">Otros servicios</p>
                            <p class="text-gray-700">{{ $veterinaria->otros_servicios }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Costos estimados</h3>

                    @if($costos->isEmpty())
                        <p class="text-gray-500">No hay costos registrados.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($costos as $costo)
                                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                                    <p class="text-sm text-gray-500 font-medium">{{ $costo->nombre }}</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        ${{ number_format($costo->precio, 2) }} {{ $costo->moneda }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Información de Contacto</h3>

                    <div class="flex items-center gap-4 mb-6 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            {{ strtoupper(substr($veterinaria->nombre, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Veterinaria</p>
                            <p class="font-bold text-gray-900">{{ $veterinaria->nombre }}</p>
                            <p class="text-xs text-gray-400">Huellitas Perdidas</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6 px-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Teléfono</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $veterinaria->telefono }}</p>
                            </div>
                        </div>

                        @if($veterinaria->whatsapp)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-green-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.52 3.48A11.86 11.86 0 0012.04 0C5.54 0 .24 5.3.24 11.82c0 2.08.54 4.1 1.58 5.88L0 24l6.5-1.7a11.8 11.8 0 005.54 1.4h.01c6.5 0 11.8-5.3 11.8-11.82 0-3.16-1.23-6.13-3.33-8.4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">WhatsApp</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $veterinaria->whatsapp }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank"
                           class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2 mb-4">
                            Contactar por WhatsApp
                        </a>
                    @endif

                    <hr class="my-6 border-gray-100">

                    <p class="text-xs text-gray-500 mb-2 font-medium">Acciones</p>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="tel:{{ $veterinaria->telefono }}"
                           class="flex items-center justify-center gap-2 py-3 px-4 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition shadow-sm">
                            Llamar
                        </a>

                        @if($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank"
                               class="flex items-center justify-center gap-2 py-3 px-4 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition shadow-sm">
                                WhatsApp
                            </a>
                        @endif

                        @if($veterinaria->latitud && $veterinaria->longitud)
                            <a href="https://www.google.com/maps?q={{ $veterinaria->latitud }},{{ $veterinaria->longitud }}"
                               target="_blank"
                               class="flex items-center justify-center gap-2 py-3 px-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-700 text-sm font-medium transition border border-gray-200">
                                Cómo llegar
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection