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

        @php
            $fechaPublicacion = $publicacion->creado_en ?? $publicacion->created_at ?? null;
            $telefonoCrudo = $publicacion->autor->whatsapp ?? $publicacion->autor->telefono ?? null;
            $telefonoLimpio = $telefonoCrudo ? preg_replace('/\D+/', '', $telefonoCrudo) : null;

            if ($telefonoLimpio && strlen($telefonoLimpio) === 10) {
                $telefonoLimpio = '52' . $telefonoLimpio;
            }

            $mensaje = "Hola, vi tu publicación de {$publicacion->nombre} en Huellitas Perdidas.";
            $whatsappUrl = $telefonoLimpio ? "https://wa.me/{$telefonoLimpio}?text=" . urlencode($mensaje) : null;

            $latitudMapa = $publicacion->ubicacion->latitud ?? null;
            $longitudMapa = $publicacion->ubicacion->longitud ?? null;
            $mapaEmbedUrl = ($latitudMapa && $longitudMapa)
                ? "https://www.google.com/maps?q={$latitudMapa},{$longitudMapa}&z=16&output=embed"
                : null;

            $authUserId = auth()->check() ? auth()->user()->id_usuario : null;
            $mostrarBotonReporte = auth()->guest() || ((int) $authUserId !== (int) ($publicacion->autor_usuario_id ?? 0));
            $abrirReporte = old('motivo_id') || old('descripcion_adicional') || $errors->reportarPublicacion->any();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
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

                        @if($fechaPublicacion)
                            <span class="text-sm text-gray-500 font-medium">
                                Publicado {{ \Carbon\Carbon::parse($fechaPublicacion)->locale('es')->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $publicacion->nombre }}</h1>

                    <p class="text-lg text-gray-500 mt-2 flex items-center gap-2">
                        {{ $publicacion->especie_id == 1 ? 'Perro' : ($publicacion->especie_id == 2 ? 'Gato' : 'Mascota') }}
                        @if($publicacion->raza)
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span> {{ $publicacion->raza }}
                        @endif
                    </p>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm">
                            <p class="text-xs text-gray-500 font-medium">Último avistamiento</p>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ $publicacion->colonia_barrio }}</p>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm">
                            <p class="text-xs text-gray-500 font-medium">Fecha de extravío</p>
                            <p class="text-sm font-semibold text-gray-800 mt-1">
                                {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->locale('es')->translatedFormat('d/m/Y') }}
                            </p>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm">
                            <p class="text-xs text-gray-500 font-medium">Características</p>
                            <p class="text-sm font-semibold text-gray-800 mt-1">
                                {{ ucfirst($publicacion->sexo) }} · {{ ucfirst($publicacion->tamano) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative w-full h-[430px] bg-gray-200 rounded-2xl overflow-hidden shadow-sm border border-gray-100 group">
                    @if($publicacion->fotoPrincipal)
                        <img src="{{ asset('storage/' . $publicacion->fotoPrincipal->url) }}" alt="{{ $publicacion->nombre }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                            <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-lg font-medium">Sin fotografía disponible</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div id="acciones-publicacion" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Información de Contacto</h3>
                    
                    <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            {{ mb_strtoupper(mb_substr($publicacion->autor->nombre ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Dueño</p>
                            <p class="font-bold text-gray-900">{{ $publicacion->autor->nombre ?? 'Usuario' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mb-6 px-1">
                         <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                         </div>
                         <div>
                             <p class="text-xs text-gray-400">Teléfono</p>
                             <p class="text-sm font-semibold text-gray-800">{{ $publicacion->autor->whatsapp ?? $publicacion->autor->telefono ?? 'No visible' }}</p>
                         </div>
                    </div>

                    <div class="space-y-3">
                        @if($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                Contactar por WhatsApp
                            </a>
                        @else
                            <button type="button" disabled class="w-full bg-gray-300 text-white font-bold py-3 px-4 rounded-lg shadow-sm cursor-not-allowed flex justify-center items-center gap-2">
                                Sin número disponible
                            </button>
                        @endif
                    </div>

                    @if(session('success_reporte'))
                        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success_reporte') }}
                        </div>
                    @endif

                    @if($mostrarBotonReporte)
                        <div class="mt-4">
                            @guest
                                <a href="{{ route('login') }}" class="w-full border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3 px-4 rounded-lg transition flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414-1.414a2 2 0 00-2.828 0L4 14.343V20h5.657L19.778 9.879a2 2 0 000-2.829z"></path>
                                    </svg>
                                    Inicia sesión para reportar
                                </a>
                            @endguest

                            @auth
                                <details class="group" {{ $abrirReporte ? 'open' : '' }}>
                                    <summary class="list-none cursor-pointer w-full border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3 px-4 rounded-lg transition flex justify-center items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414-1.414a2 2 0 00-2.828 0L4 14.343V20h5.657L19.778 9.879a2 2 0 000-2.829z"></path>
                                        </svg>
                                        Reportar publicación
                                    </summary>

                                    <div class="mt-4 rounded-xl border border-red-100 bg-red-50/40 p-4">
                                        <form action="{{ route('extravios.reportar', $publicacion->id_publicacion) }}" method="POST" class="space-y-4">
                                            @csrf

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo</label>
                                                <select name="motivo_id" class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none">
                                                    <option value="">Selecciona un motivo</option>
                                                    @foreach($motivosReporte as $motivo)
                                                        <option value="{{ $motivo->id_motivo }}" {{ old('motivo_id') == $motivo->id_motivo ? 'selected' : '' }}>
                                                            {{ $motivo->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if($errors->reportarPublicacion->has('motivo_id'))
                                                    <p class="text-red-500 text-sm mt-2">{{ $errors->reportarPublicacion->first('motivo_id') }}</p>
                                                @endif

                                                @if($errors->reportarPublicacion->has('reporte'))
                                                    <p class="text-red-500 text-sm mt-2">{{ $errors->reportarPublicacion->first('reporte') }}</p>
                                                @endif
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción adicional</label>
                                                <textarea
                                                    name="descripcion_adicional"
                                                    rows="4"
                                                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none resize-none"
                                                    placeholder="Describe brevemente por qué estás reportando esta publicación..."
                                                >{{ old('descripcion_adicional') }}</textarea>

                                                @if($errors->reportarPublicacion->has('descripcion_adicional'))
                                                    <p class="text-red-500 text-sm mt-2">{{ $errors->reportarPublicacion->first('descripcion_adicional') }}</p>
                                                @endif
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-5 py-2.5 rounded-lg transition">
                                                    Enviar reporte
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            @endauth
                        </div>
                    @endif
                    
                    <hr class="my-6 border-gray-100">

                    <p class="text-xs text-gray-500 mb-3 font-medium">Acciones</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="window.print()" class="flex items-center justify-center gap-2 py-2.5 px-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-600 text-sm font-medium transition border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Imprimir
                        </button>
                        <button class="flex items-center justify-center gap-2 py-2.5 px-4 bg-orange-50 hover:bg-orange-100 rounded-lg text-orange-600 text-sm font-medium transition border border-orange-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            Compartir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mt-8">
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
                                {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->locale('es')->translatedFormat('d \d\e F, Y') }}
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Última ubicación conocida</h3>
                
                @if($mapaEmbedUrl)
                    <div class="overflow-hidden rounded-xl border border-gray-100 shadow-sm">
                        <iframe
                            src="{{ $mapaEmbedUrl }}"
                            width="100%"
                            height="360"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <p class="text-sm text-gray-500 mt-3">Cerca de: {{ $publicacion->colonia_barrio }}</p>
                @else
                    <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
                        <p class="text-red-500">No hay coordenadas exactas para esta mascota.</p>
                    </div>
                @endif
            </div>
        </div>

        <div id="comentarios" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mt-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Comentarios</h3>
                <span class="text-sm text-gray-400">{{ $comentarios->count() }} principales</span>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any() && !$errors->reportarPublicacion->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @auth
                <div class="mb-8 rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                    <form action="{{ route('extravios.comentarios.store', $publicacion->id_publicacion) }}" method="POST">
                        @csrf

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Agrega un comentario
                        </label>

                        <textarea 
                            name="comentario"
                            class="w-full border border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none resize-none h-28 bg-white"
                            placeholder="Escribe un comentario si tienes información sobre esta mascota..."
                        >{{ old('comentario') }}</textarea>

                        <div class="flex justify-end mt-3">
                            <button type="submit" class="bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-700 transition flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Publicar comentario
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mb-8 rounded-2xl border border-orange-200 bg-orange-50 p-5">
                    <p class="text-sm text-gray-700 mb-3">
                        Inicia sesión para comentar o responder comentarios en esta publicación.
                    </p>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition">
                        Iniciar sesión
                    </a>
                </div>
            @endauth

            <div class="space-y-6">
                @forelse($comentarios as $comentario)
                    @php
                        $nombre = trim($comentario->usuario_nombre ?? 'Usuario');
                        $partes = preg_split('/\s+/', $nombre);
                        $iniciales = '';

                        foreach (array_slice($partes, 0, 2) as $parte) {
                            $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1));
                        }

                        $esPropio = auth()->check() && auth()->user()->id_usuario == $comentario->usuario_id;
                        $respuestas = $respuestasPorPadre->get($comentario->id_comentario, collect());
                    @endphp

                    <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-5">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center text-sm font-bold shadow-sm flex-shrink-0">
                                {{ $iniciales ?: 'U' }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-bold text-gray-900">{{ $comentario->usuario_nombre }}</p>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($comentario->creado_en)->locale('es')->diffForHumans() }}</span>

                                    @if($comentario->estado === 'ELIMINADO')
                                        <span class="text-[10px] px-2 py-1 rounded-full bg-gray-200 text-gray-500 uppercase font-bold tracking-wide">
                                            Eliminado
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    @if($comentario->estado === 'ELIMINADO')
                                        <p class="text-sm italic text-gray-400">Este comentario fue eliminado.</p>
                                    @else
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $comentario->comentario }}</p>
                                    @endif
                                </div>

                                @if($comentario->estado === 'VISIBLE')
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @auth
                                            <details class="group">
                                                <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-orange-100 text-orange-600 text-xs font-semibold hover:bg-orange-50 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                    </svg>
                                                    Responder
                                                </summary>

                                                <div class="mt-3">
                                                    <form action="{{ route('extravios.comentarios.store', $publicacion->id_publicacion) }}" method="POST" class="space-y-3">
                                                        @csrf
                                                        <input type="hidden" name="comentario_padre_id" value="{{ $comentario->id_comentario }}">

                                                        <textarea name="comentario" class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none resize-none h-24 bg-white" placeholder="Escribe una respuesta..."></textarea>

                                                        <div class="flex justify-end">
                                                            <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                                                                Responder
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </details>

                                            @if($esPropio)
                                                <details class="group">
                                                    <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-blue-100 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m-7-7h14"></path>
                                                        </svg>
                                                        Editar
                                                    </summary>

                                                    <div class="mt-3">
                                                        <form action="{{ route('extravios.comentarios.update', [$publicacion->id_publicacion, $comentario->id_comentario]) }}" method="POST" class="space-y-3">
                                                            @csrf
                                                            @method('PUT')

                                                            <textarea name="comentario" class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none h-24 bg-white">{{ $comentario->comentario }}</textarea>

                                                            <div class="flex justify-end">
                                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                                                    Guardar cambios
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </details>

                                                <form action="{{ route('extravios.comentarios.destroy', [$publicacion->id_publicacion, $comentario->id_comentario]) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este comentario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-red-100 text-red-500 text-xs font-semibold hover:bg-red-50 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path>
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                @endif

                                @if($respuestas->count())
                                    <div class="mt-5 pl-4 border-l-2 border-orange-100 space-y-4">
                                        @foreach($respuestas as $respuesta)
                                            @php
                                                $nombreRespuesta = trim($respuesta->usuario_nombre ?? 'Usuario');
                                                $partesRespuesta = preg_split('/\s+/', $nombreRespuesta);
                                                $inicialesRespuesta = '';

                                                foreach (array_slice($partesRespuesta, 0, 2) as $parte) {
                                                    $inicialesRespuesta .= mb_strtoupper(mb_substr($parte, 0, 1));
                                                }

                                                $esPropiaRespuesta = auth()->check() && auth()->user()->id_usuario == $respuesta->usuario_id;
                                            @endphp

                                            <div class="rounded-xl border border-gray-100 bg-white p-4">
                                                <div class="flex gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">
                                                        {{ $inicialesRespuesta ?: 'U' }}
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <p class="text-sm font-bold text-gray-900">{{ $respuesta->usuario_nombre }}</p>
                                                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($respuesta->creado_en)->locale('es')->diffForHumans() }}</span>

                                                            @if($respuesta->estado === 'ELIMINADO')
                                                                <span class="text-[10px] px-2 py-1 rounded-full bg-gray-200 text-gray-500 uppercase font-bold tracking-wide">
                                                                    Eliminado
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="mt-2">
                                                            @if($respuesta->estado === 'ELIMINADO')
                                                                <p class="text-sm italic text-gray-400">Esta respuesta fue eliminada.</p>
                                                            @else
                                                                <p class="text-sm text-gray-700 leading-relaxed">{{ $respuesta->comentario }}</p>
                                                            @endif
                                                        </div>

                                                        @if($respuesta->estado === 'VISIBLE' && $esPropiaRespuesta)
                                                            <div class="mt-4 flex flex-wrap gap-2">
                                                                <details class="group">
                                                                    <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-blue-100 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m-7-7h14"></path>
                                                                        </svg>
                                                                        Editar
                                                                    </summary>

                                                                    <div class="mt-3">
                                                                        <form action="{{ route('extravios.comentarios.update', [$publicacion->id_publicacion, $respuesta->id_comentario]) }}" method="POST" class="space-y-3">
                                                                            @csrf
                                                                            @method('PUT')

                                                                            <textarea name="comentario" class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none h-24 bg-white">{{ $respuesta->comentario }}</textarea>

                                                                            <div class="flex justify-end">
                                                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                                                                    Guardar cambios
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </details>

                                                                <form action="{{ route('extravios.comentarios.destroy', [$publicacion->id_publicacion, $respuesta->id_comentario]) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta respuesta?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-red-100 text-red-500 text-xs font-semibold hover:bg-red-50 transition">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path>
                                                                        </svg>
                                                                        Eliminar
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                        <p class="text-gray-500 text-sm">Aún no hay comentarios en esta publicación.</p>
                    </div>
                @endforelse
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
                <p class="text-lg mb-2"><strong class="text-red-600">📅 Fecha:</strong> {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->locale('es')->translatedFormat('d/m/Y') }}</p>
                <div class="bg-yellow-50 p-3 rounded border border-yellow-200 text-sm italic text-gray-700 line-clamp-3">"{{ $publicacion->descripcion }}"</div>
            </div>
        </div>
    </div>
    <div class="mt-4 pt-2 border-t-4 border-red-600">
        <p class="text-xl font-bold text-gray-800 uppercase">Si tienes información llama al:</p>
        <div class="bg-red-600 text-white rounded-xl py-2 px-4 mt-2 inline-block w-full">
            <h2 class="text-6xl font-black tracking-widest">{{ $publicacion->autor->telefono ?? 'SIN NÚMERO' }}</h2>
            <p class="text-lg font-medium">{{ $publicacion->autor->nombre ?? 'Contacto' }}</p>
        </div>
        <p class="mt-2 text-xs text-gray-400">Generado en HuellitasPerdidas.com</p>
    </div>
</div>
@endsection