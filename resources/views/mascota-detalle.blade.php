@extends('layout.app')

@section('meta_tags')
    <meta property="og:title" content="¡Ayúdame a encontrar a {{ $publicacion->nombre }}!" />
    <meta property="og:description" content="Se extravió el {{ $publicacion->fecha_extravio }} por la zona de {{ $publicacion->ultimo_avistamiento }}. Características: {{ $publicacion->sexo }}, tamaño {{ $publicacion->tamano }}." />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="article" />
    
    @if($publicacion->fotos->count() > 0)
        <meta property="og:image" content="{{ asset('storage/' . $publicacion->fotos->first()->url) }}" />
    @endif
@endsection


@section('content')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
/>

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
    $esAutor = auth()->check() && ((int) $authUserId === (int) ($publicacion->autor_usuario_id ?? 0));
    $mostrarBotonReporte = auth()->guest() || !$esAutor;
    $abrirReporte = old('motivo_id') || old('descripcion_adicional') || $errors->reportarPublicacion->any();

    $abrirAvistamiento =
        old('descripcion_avistamiento') ||
        old('nombre_contacto') ||
        old('telefono_contacto') ||
        old('fecha_avistamiento') ||
        old('colonia_barrio') ||
        old('calle_referencias') ||
        old('latitud_avistamiento') ||
        old('longitud_avistamiento');

    $clasesEstado = [
        'ACTIVA' => 'bg-red-100 text-red-700 border-red-200',
        'ENCONTRADA' => 'bg-green-100 text-green-700 border-green-200',
        'RESUELTA' => 'bg-blue-100 text-blue-700 border-blue-200',
    ];

    $clase = $clasesEstado[$publicacion->estado] ?? 'bg-gray-100 text-gray-600 border-gray-200';

    $fotosGaleria = collect();

    if (isset($publicacion->fotos) && $publicacion->fotos->count()) {
        $fotosGaleria = $publicacion->fotos->sortBy('orden')->values();
    } elseif ($publicacion->fotoPrincipal) {
        $fotosGaleria = collect([$publicacion->fotoPrincipal]);
    }
@endphp

<style>
    details summary::-webkit-details-marker {
        display: none;
    }

    .gallery-slide {
        min-width: 100%;
    }

    .thumb-active {
        outline: 3px solid #f97316;
        outline-offset: 2px;
    }

    .avistamiento-map {
        height: 320px;
        width: 100%;
        border-radius: 1rem;
        z-index: 1;
    }

    @if($esAutor)
    #cartel-imprimible { display: none; }

    @media print {
        @page { margin: 0; size: auto; }

        body * {
            visibility: hidden;
        }

        #cartel-imprimible,
        #cartel-imprimible * {
            visibility: visible;
        }

        #cartel-imprimible {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            background: white;
            z-index: 9999;
        }
    }
    @endif
</style>

<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-8 lg:py-10">

        <div class="mb-8">
            <nav class="text-sm text-gray-400 flex flex-wrap items-center gap-2">
                <a href="{{ route('inicio') }}" class="hover:text-orange-500 transition">Inicio</a>
                <span>/</span>
                <a href="{{ route('mascotas.index2') }}" class="hover:text-orange-500 transition">Mascotas perdidas</a>
                <span>/</span>
                <span class="text-gray-600 font-medium">{{ $publicacion->nombre }}</span>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-5">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="{{ $clase }} border px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide">
                                {{ $publicacion->estado }}
                            </span>

                            <span class="inline-flex items-center rounded-full bg-orange-50 text-orange-600 px-3 py-1 text-[11px] font-semibold border border-orange-100">
                                {{ $especieTexto }}
                            </span>

                            @if($fechaPublicacion)
                                <span class="text-sm text-gray-500">
                                    Publicado {{ \Carbon\Carbon::parse($fechaPublicacion)->locale('es')->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                            {{ $publicacion->nombre }}
                        </h1>

                        <div class="mt-3 flex flex-wrap items-center gap-2 text-base text-gray-500">
                            <span>{{ $especieTexto }}</span>

                            @if($razaTexto)
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>{{ $razaTexto }}</span>
                            @elseif(!empty($publicacion->otra_raza))
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>{{ $publicacion->otra_raza }}</span>
                            @endif

                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span>{{ ucfirst(strtolower($publicacion->sexo)) }}</span>
                        </div>
                    </div>

                    <div class="px-4 sm:px-5 pb-5">
                        @if($fotosGaleria->count())
                            <div class="space-y-4">
                                <div class="relative w-full aspect-[4/3] bg-gray-100 rounded-[1.75rem] overflow-hidden border border-gray-100">
                                    <div id="galleryTrack" class="flex h-full transition-transform duration-500 ease-in-out">
                                        @foreach($fotosGaleria as $foto)
                                            <div class="gallery-slide h-full">
                                                <img
                                                    src="{{ asset('storage/' . $foto->url) }}"
                                                    alt="{{ $publicacion->nombre }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($fotosGaleria->count() > 1)
                                        <button
                                            type="button"
                                            id="galleryPrev"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 shadow-md rounded-full w-11 h-11 flex items-center justify-center transition"
                                        >
                                            ‹
                                        </button>

                                        <button
                                            type="button"
                                            id="galleryNext"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 shadow-md rounded-full w-11 h-11 flex items-center justify-center transition"
                                        >
                                            ›
                                        </button>

                                        <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2">
                                            @foreach($fotosGaleria as $index => $foto)
                                                <button
                                                    type="button"
                                                    class="gallery-dot w-2.5 h-2.5 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"
                                                    data-index="{{ $index }}"
                                                ></button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if($fotosGaleria->count() > 1)
                                    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
                                        @foreach($fotosGaleria as $index => $foto)
                                            <button
                                                type="button"
                                                class="gallery-thumb rounded-2xl overflow-hidden border border-gray-200 h-20 sm:h-24 {{ $index === 0 ? 'thumb-active' : '' }}"
                                                data-index="{{ $index }}"
                                            >
                                                <img
                                                    src="{{ asset('storage/' . $foto->url) }}"
                                                    alt="Miniatura {{ $index + 1 }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="w-full aspect-[4/3] bg-gray-100 rounded-[1.75rem] border border-gray-100 flex items-center justify-center text-gray-400">
                                Sin fotografía disponible
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900">Resumen del reporte</h2>
                        <p class="text-sm text-gray-500 mt-1">Datos clave y acciones disponibles.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Fecha</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->locale('es')->translatedFormat('d \d\e F, Y') }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Ubicación</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $publicacion->colonia_barrio }}</p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Tamaño</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ ucfirst(strtolower($publicacion->tamano)) }}</p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Color</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $publicacion->color }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($publicacion->autor->nombre ?? 'U', 0, 1)) }}
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">Dueño</p>
                                    <p class="text-gray-900 font-bold truncate">{{ $publicacion->autor->nombre ?? 'Usuario' }}</p>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-400 mb-1">Teléfono</p>
                                <p class="text-sm font-semibold text-gray-800 break-words">
                                    {{ $publicacion->autor->whatsapp ?? $publicacion->autor->telefono ?? 'No visible' }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if($whatsappUrl)
                                <a
                                    href="{{ $whatsappUrl }}"
                                    target="_blank"
                                    class="w-full inline-flex items-center justify-center bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition"
                                >
                                    Contactar por WhatsApp
                                </a>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="w-full inline-flex items-center justify-center bg-gray-300 text-white font-bold py-3.5 px-5 rounded-xl cursor-not-allowed"
                                >
                                    Sin número disponible
                                </button>
                            @endif

                            <button
                                type="button"
                                onclick="navigator.share ? navigator.share({ title: '{{ $publicacion->nombre }}', text: 'Ayuda a encontrar a {{ $publicacion->nombre }}', url: window.location.href }) : window.prompt('Copia este enlace:', window.location.href)"
                                class="w-full inline-flex items-center justify-center border border-orange-100 bg-orange-50 hover:bg-orange-100 text-orange-600 font-semibold py-3.5 px-5 rounded-xl transition"
                            >
                                Compartir reporte
                            </button>
                        </div>

                        @if(session('success_reporte'))
                            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                {{ session('success_reporte') }}
                            </div>
                        @endif

                        @if($mostrarBotonReporte)
                            @guest
                                <a
                                    href="{{ route('login') }}"
                                    class="w-full inline-flex items-center justify-center border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3.5 px-5 rounded-xl transition"
                                >
                                    Inicia sesión para reportar
                                </a>
                            @endguest

                            @auth
                                <details class="group" {{ $abrirReporte ? 'open' : '' }}>
                                    <summary class="list-none cursor-pointer w-full inline-flex items-center justify-center border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3.5 px-5 rounded-xl transition">
                                        Reportar publicación
                                    </summary>

                                    <div class="mt-4 rounded-2xl border border-red-100 bg-red-50/40 p-4">
                                        <form action="{{ route('extravios.reportar', $publicacion->id_publicacion) }}" method="POST" class="space-y-4">
                                            @csrf

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo</label>
                                                <select
                                                    name="motivo_id"
                                                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none"
                                                >
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
                                                <button
                                                    type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white font-semibold px-5 py-2.5 rounded-xl transition"
                                                >
                                                    Enviar reporte
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            @endauth
                        @endif

                        <div id="bloque-avistamiento">
                            @if(session('success_avistamiento') && !$esAutor)
                                <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                    {{ session('success_avistamiento') }}
                                </div>
                            @endif

                            @guest
                                <a
                                    href="{{ route('login') }}"
                                    class="w-full inline-flex items-center justify-center border border-orange-200 bg-orange-50 hover:bg-orange-100 text-orange-600 font-semibold py-3.5 px-5 rounded-xl transition"
                                >
                                    Reportar avistamiento
                                </a>
                            @endguest

                            @auth
                                @if(!$esAutor)
                                    <details class="group" {{ $abrirAvistamiento ? 'open' : '' }}>
                                        <summary class="list-none cursor-pointer w-full inline-flex items-center justify-center border border-orange-200 bg-orange-50 hover:bg-orange-100 text-orange-600 font-semibold py-3.5 px-5 rounded-xl transition">
                                            Reportar avistamiento
                                        </summary>

                                        <div class="mt-4 rounded-2xl border border-orange-100 bg-orange-50/40 p-4">
                                            <form action="{{ route('extravios.avistamientos.store', $publicacion->id_publicacion) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                @csrf

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de contacto</label>
                                                        <input
                                                            type="text"
                                                            name="nombre_contacto"
                                                            value="{{ old('nombre_contacto', auth()->user()->nombre ?? '') }}"
                                                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none"
                                                        >
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                                                        <input
                                                            type="text"
                                                            name="telefono_contacto"
                                                            value="{{ old('telefono_contacto', auth()->user()->telefono ?? '') }}"
                                                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none"
                                                        >
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha del avistamiento</label>
                                                        <input
                                                            type="date"
                                                            name="fecha_avistamiento"
                                                            value="{{ old('fecha_avistamiento') }}"
                                                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none"
                                                        >
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Colonia o barrio</label>
                                                        <input
                                                            type="text"
                                                            id="colonia_barrio_avistamiento"
                                                            name="colonia_barrio"
                                                            value="{{ old('colonia_barrio') }}"
                                                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none"
                                                        >
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Calle y referencias</label>
                                                    <input
                                                        type="text"
                                                        id="calle_referencias_avistamiento"
                                                        name="calle_referencias"
                                                        value="{{ old('calle_referencias') }}"
                                                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none"
                                                    >
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                                                    <textarea
                                                        name="descripcion_avistamiento"
                                                        rows="4"
                                                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none resize-none"
                                                        placeholder="Describe dónde la viste, cómo estaba y cualquier dato útil..."
                                                    >{{ old('descripcion_avistamiento') }}</textarea>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto opcional</label>
                                                    <input
                                                        type="file"
                                                        name="foto_avistamiento"
                                                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none"
                                                    >
                                                </div>

                                                <div class="rounded-2xl border border-orange-200 bg-white p-4">
                                                    <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-800">Ubicación del avistamiento</p>
                                                            <p class="text-xs text-gray-500">Da clic en el mapa para colocar un pin y rellenar los campos automáticamente.</p>
                                                        </div>

                                                        <button
                                                            type="button"
                                                            id="usarUbicacionActualBtn"
                                                            class="px-4 py-2 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-700 text-sm font-semibold transition"
                                                        >
                                                            Usar mi ubicación
                                                        </button>
                                                    </div>

                                                    <div id="mapa-avistamiento" class="avistamiento-map border border-gray-200"></div>

                                                    <div class="mt-3 text-xs text-gray-500">
                                                        <span id="mapa-avistamiento-estado">Aún no has marcado una ubicación.</span>
                                                    </div>

                                                    <input type="hidden" id="latitud_avistamiento" name="latitud_avistamiento" value="{{ old('latitud_avistamiento') }}">
                                                    <input type="hidden" id="longitud_avistamiento" name="longitud_avistamiento" value="{{ old('longitud_avistamiento') }}">
                                                </div>

                                                @if($errors->has('descripcion_avistamiento'))
                                                    <p class="text-red-500 text-sm">{{ $errors->first('descripcion_avistamiento') }}</p>
                                                @endif

                                                <div class="flex justify-end">
                                                    <button
                                                        type="submit"
                                                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl transition"
                                                    >
                                                        Enviar avistamiento
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mt-10">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900">Información de la mascota</h3>
                    <p class="text-sm text-gray-500 mt-1">Detalles para reconocerla con mayor facilidad.</p>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="space-y-6">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Descripción</p>
                            <p class="text-gray-700 leading-relaxed">{{ $publicacion->descripcion }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Fecha de extravío</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->locale('es')->translatedFormat('d \d\e F, Y') }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Último lugar visto</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $publicacion->colonia_barrio }}</p>

                                @if($publicacion->calle_referencias)
                                    <p class="text-sm text-gray-500 mt-1">{{ $publicacion->calle_referencias }}</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-3">Características</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                    Sexo: {{ ucfirst(strtolower($publicacion->sexo)) }}
                                </span>
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                    Tamaño: {{ ucfirst(strtolower($publicacion->tamano)) }}
                                </span>
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                    Color: {{ $publicacion->color }}
                                </span>
                                @if($razaTexto)
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                        Raza: {{ $razaTexto }}
                                    </span>
                                @elseif(!empty($publicacion->otra_raza))
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                        Raza: {{ $publicacion->otra_raza }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900">Última ubicación conocida</h3>
                    <p class="text-sm text-gray-500 mt-1">Referencia geográfica del reporte.</p>
                </div>

                <div class="p-6 sm:p-8">
                    @if($mapaEmbedUrl)
                        <div class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm">
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

                        <p class="text-sm text-gray-500 mt-4">
                            Cerca de: <span class="font-medium text-gray-700">{{ $publicacion->colonia_barrio }}</span>
                        </p>
                    @else
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
                            <p class="text-gray-500 font-medium">No hay coordenadas exactas para esta mascota.</p>
                        </div>
                    @endif
                    
                    <hr class="my-6 border-gray-100">

                    <p class="text-xs text-gray-500 mb-3 font-medium">Acciones</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="window.print()" class="flex items-center justify-center gap-2 py-2.5 px-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-600 text-sm font-medium transition border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Imprimir
                        </button>
                        @php
                            // Obtenemos la URL exacta de esta página y la codificamos para que sea segura
                            $urlActual = urlencode(url()->current());
                            $urlFacebook = "https://www.facebook.com/sharer/sharer.php?u=" . $urlActual;
                        @endphp

                        <a href="{{ $urlFacebook }}" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 bg-orange-50 border border-orange-200 rounded-lg font-semibold text-orange-600 hover:bg-orange-100 transition-colors">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Compartir en Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($esAutor)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-10" id="avistamientos">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Avistamientos recibidos</h3>
                            <p class="text-sm text-gray-500 mt-1">Solo tú puedes ver esta información.</p>
                        </div>
                        <span class="text-sm text-gray-400">{{ $avistamientos->count() }} registrados</span>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    @if(session('success_avistamiento'))
                        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success_avistamiento') }}
                        </div>
                    @endif

                    <div class="space-y-5">
                        @forelse($avistamientos as $avistamiento)
                            @php
                                $estadoAv = $avistamiento->estado ?? 'ENVIADO';
                                $claseEstadoAv = match($estadoAv) {
                                    'VISTO' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'DESCARTADO' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-amber-100 text-amber-700 border-amber-200',
                                };
                            @endphp

                            <div class="rounded-3xl border border-gray-100 bg-gray-50 p-5">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $avistamiento->nombre_contacto ?: ($avistamiento->usuario_nombre ?: 'Usuario') }}
                                            </p>

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $claseEstadoAv }}">
                                                {{ $estadoAv }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-600">
                                            <strong>Fecha del avistamiento:</strong>
                                            {{ $avistamiento->fecha_avistamiento ? \Carbon\Carbon::parse($avistamiento->fecha_avistamiento)->format('d/m/Y') : 'No indicada' }}
                                        </p>

                                        <p class="text-sm text-gray-600">
                                            <strong>Zona:</strong> {{ $avistamiento->colonia_barrio ?: 'No indicada' }}
                                        </p>

                                        <p class="text-sm text-gray-600">
                                            <strong>Referencia:</strong> {{ $avistamiento->calle_referencias ?: 'No indicada' }}
                                        </p>

                                        @if($avistamiento->telefono_contacto)
                                            <p class="text-sm text-gray-600">
                                                <strong>Teléfono:</strong> {{ $avistamiento->telefono_contacto }}
                                            </p>
                                        @endif

                                        @if(!empty($avistamiento->latitud) && !empty($avistamiento->longitud))
                                            <p class="text-sm">
                                                <a
                                                    href="https://www.google.com/maps?q={{ $avistamiento->latitud }},{{ $avistamiento->longitud }}"
                                                    target="_blank"
                                                    class="text-orange-600 font-medium hover:text-orange-700"
                                                >
                                                    Ver ubicación en mapa
                                                </a>
                                            </p>
                                        @endif

                                        <div class="mt-3">
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $avistamiento->descripcion }}</p>
                                        </div>

                                        @if($avistamiento->foto_url)
                                            <div class="mt-4">
                                                <img
                                                    src="{{ asset('storage/' . $avistamiento->foto_url) }}"
                                                    class="w-40 h-40 object-cover rounded-2xl border border-gray-200"
                                                >
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        @if($avistamiento->estado !== 'VISTO')
                                            <form action="{{ route('extravios.avistamientos.visto', $avistamiento->id_avistamiento) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-4 py-2 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-semibold transition"
                                                >
                                                    Marcar como visto
                                                </button>
                                            </form>
                                        @endif

                                        @if($avistamiento->estado !== 'DESCARTADO')
                                            <form action="{{ route('extravios.avistamientos.descartar', $avistamiento->id_avistamiento) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-4 py-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 text-sm font-semibold transition"
                                                >
                                                    Descartar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 border border-dashed border-gray-200 rounded-3xl bg-gray-50">
                                <p class="text-gray-500 text-sm">Aún no hay avistamientos registrados para esta publicación.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <div id="comentarios" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-10">
            <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Comentarios</h3>
                        <p class="text-sm text-gray-500 mt-1">La comunidad puede dejar información útil para ayudarte.</p>
                    </div>
                    <span class="text-sm text-gray-400">{{ $comentarios->count() }} principales</span>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                @if(session('success'))
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any() && !$errors->reportarPublicacion->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                @auth
                    <div class="mb-8 rounded-3xl border border-orange-100 bg-orange-50/50 p-5">
                        <form action="{{ route('extravios.comentarios.store', $publicacion->id_publicacion) }}" method="POST">
                            @csrf

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Agrega un comentario
                            </label>

                            <textarea
                                name="comentario"
                                class="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none resize-none h-28 bg-white"
                                placeholder="Escribe un comentario si tienes información sobre esta mascota..."
                            >{{ old('comentario') }}</textarea>

                            <div class="flex justify-end mt-3">
                                <button
                                    type="submit"
                                    class="bg-orange-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-600 transition"
                                >
                                    Publicar comentario
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mb-8 rounded-3xl border border-orange-200 bg-orange-50 p-5">
                        <p class="text-sm text-gray-700 mb-3">
                            Inicia sesión para comentar o responder comentarios en esta publicación.
                        </p>

                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl transition"
                        >
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

                        <div class="rounded-3xl border border-gray-100 bg-gray-50 p-5 sm:p-6">
                            <div class="flex gap-3 sm:gap-4">
                                <div class="w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
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
                                                    <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-orange-100 text-orange-600 text-xs font-semibold hover:bg-orange-50 transition">
                                                        Responder
                                                    </summary>

                                                    <div class="mt-3">
                                                        <form action="{{ route('extravios.comentarios.store', $publicacion->id_publicacion) }}" method="POST" class="space-y-3">
                                                            @csrf
                                                            <input type="hidden" name="comentario_padre_id" value="{{ $comentario->id_comentario }}">

                                                            <textarea
                                                                name="comentario"
                                                                class="w-full border border-gray-200 rounded-2xl p-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none resize-none h-24 bg-white"
                                                                placeholder="Escribe una respuesta..."
                                                            ></textarea>

                                                            <div class="flex justify-end">
                                                                <button
                                                                    type="submit"
                                                                    class="bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-orange-600 transition"
                                                                >
                                                                    Responder
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </details>

                                                @if($esPropio)
                                                    <details class="group">
                                                        <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-blue-100 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition">
                                                            Editar
                                                        </summary>

                                                        <div class="mt-3">
                                                            <form action="{{ route('extravios.comentarios.update', [$publicacion->id_publicacion, $comentario->id_comentario]) }}" method="POST" class="space-y-3">
                                                                @csrf
                                                                @method('PUT')

                                                                <textarea
                                                                    name="comentario"
                                                                    class="w-full border border-gray-200 rounded-2xl p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none h-24 bg-white"
                                                                >{{ $comentario->comentario }}</textarea>

                                                                <div class="flex justify-end">
                                                                    <button
                                                                        type="submit"
                                                                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition"
                                                                    >
                                                                        Guardar cambios
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </details>

                                                    <form action="{{ route('extravios.comentarios.destroy', [$publicacion->id_publicacion, $comentario->id_comentario]) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este comentario?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-red-100 text-red-500 text-xs font-semibold hover:bg-red-50 transition"
                                                        >
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

                                                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                                    <div class="flex gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
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
                                                                        <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-blue-100 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition">
                                                                            Editar
                                                                        </summary>

                                                                        <div class="mt-3">
                                                                            <form action="{{ route('extravios.comentarios.update', [$publicacion->id_publicacion, $respuesta->id_comentario]) }}" method="POST" class="space-y-3">
                                                                                @csrf
                                                                                @method('PUT')

                                                                                <textarea
                                                                                    name="comentario"
                                                                                    class="w-full border border-gray-200 rounded-2xl p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none h-24 bg-white"
                                                                                >{{ $respuesta->comentario }}</textarea>

                                                                                <div class="flex justify-end">
                                                                                    <button
                                                                                        type="submit"
                                                                                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition"
                                                                                    >
                                                                                        Guardar cambios
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </details>

                                                                    <form action="{{ route('extravios.comentarios.destroy', [$publicacion->id_publicacion, $respuesta->id_comentario]) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta respuesta?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button
                                                                            type="submit"
                                                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-red-100 text-red-500 text-xs font-semibold hover:bg-red-50 transition"
                                                                        >
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
                        <div class="text-center py-12 border border-dashed border-gray-200 rounded-3xl bg-gray-50">
                            <p class="text-gray-500 text-sm">Aún no hay comentarios en esta publicación.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <section class="mt-14">
            <div class="flex items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Otras mascotas extraviadas</h2>
                    <p class="text-sm text-gray-500 mt-1">Más reportes que podrían ayudarte a identificar casos cercanos o similares.</p>
                </div>

                <a href="{{ route('mascotas.index2') }}"
                   class="hidden md:inline-flex items-center text-sm font-semibold text-orange-500 hover:text-orange-600 transition">
                    Ver todas
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @forelse($mascotasRelacionadas as $mascota)
                    @php
                        $especieRelacionada = match ((int) $mascota->especie_id) {
                            1 => 'Perro',
                            2 => 'Gato',
                            default => 'Mascota',
                        };
                    @endphp

                    <a href="{{ route('extravios.show', $mascota->id_publicacion) }}">
                        <div class="bg-white rounded-3xl shadow-sm hover:shadow-lg transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                            <div class="h-64 relative overflow-hidden bg-gray-100">
                                @if($mascota->fotoPrincipal)
                                    <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}"
                                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                        <span class="text-sm">Sin imagen</span>
                                    </div>
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>

                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold shadow-sm uppercase tracking-wide bg-red-100 text-red-600">
                                    Perdida
                                </span>

                                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm bg-white/90 text-gray-700 backdrop-blur-sm">
                                    {{ $especieRelacionada }}
                                </span>
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $mascota->nombre }}</h3>

                                <p class="text-sm text-gray-500 mb-3 min-h-[44px] leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit($mascota->descripcion, 85) }}
                                </p>

                                <div class="mt-auto flex items-center text-gray-500 text-xs font-medium mb-4">
                                    <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $mascota->colonia_barrio }}
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <span class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-50 text-orange-600 font-semibold py-2.5 border border-orange-100 group-hover:bg-orange-500 group-hover:text-white group-hover:border-orange-500 transition">
                                        Ver reporte
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-1 md:col-span-2 xl:col-span-4 text-center py-12 rounded-3xl border border-dashed border-gray-200 bg-white">
                        <p class="text-gray-500 text-lg">No hay otras mascotas extraviadas para mostrar por ahora.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

@if($esAutor)
<div id="cartel-imprimible" class="font-sans text-center">
    <div class="border-b-4 border-red-600 pb-2 mb-2">
        <h1 class="text-5xl font-black text-red-600 uppercase tracking-tighter">¡SE BUSCA!</h1>
        <p class="text-xl text-gray-800 font-bold uppercase">Ayúdanos a encontrarlo</p>
    </div>

    <div class="flex-grow flex flex-col justify-center items-center overflow-hidden min-h-0">
        <div class="w-full h-full max-h-[45vh] flex justify-center mb-4">
            @if($fotosGaleria->count())
                <img
                    src="{{ asset('storage/' . $fotosGaleria->first()->url) }}"
                    class="h-full w-auto object-contain border-4 border-gray-800 rounded-lg"
                >
            @else
                <div class="h-64 w-64 bg-gray-200 flex items-center justify-center border-2 border-dashed border-gray-400 rounded-lg">
                    <span class="text-2xl text-gray-400 font-bold">Sin Foto</span>
                </div>
            @endif
        </div>

        <div class="w-full">
            <h2 class="text-4xl font-bold text-gray-900 leading-tight">{{ $publicacion->nombre }}</h2>

            <p class="text-xl text-gray-600 font-semibold mb-2">
                {{ $especieTexto }} - {{ ucfirst($razaTexto ?? $publicacion->otra_raza ?? 'Raza desconocida') }}
            </p>

            <div class="flex justify-center gap-6 text-lg border-t border-b border-gray-300 py-2 mb-4 bg-gray-50">
                <div>
                    <span class="font-bold block text-xs text-gray-500">SEXO</span>
                    {{ ucfirst(strtolower($publicacion->sexo)) }}
                </div>
                <div>
                    <span class="font-bold block text-xs text-gray-500">TAMAÑO</span>
                    {{ ucfirst(strtolower($publicacion->tamano)) }}
                </div>
                <div>
                    <span class="font-bold block text-xs text-gray-500">COLOR</span>
                    {{ $publicacion->color }}
                </div>
            </div>

            <div class="text-left px-4">
                <p class="text-lg mb-1">
                    <strong class="text-red-600">📍 Visto en:</strong> {{ $publicacion->colonia_barrio }}
                </p>

                <p class="text-lg mb-2">
                    <strong class="text-red-600">📅 Fecha:</strong>
                    {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->locale('es')->translatedFormat('d/m/Y') }}
                </p>

                <div class="bg-yellow-50 p-3 rounded border border-yellow-200 text-sm italic text-gray-700">
                    "{{ $publicacion->descripcion }}"
                </div>
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
@endif

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.getElementById('galleryTrack');
        const prevBtn = document.getElementById('galleryPrev');
        const nextBtn = document.getElementById('galleryNext');
        const thumbs = document.querySelectorAll('.gallery-thumb');
        const dots = document.querySelectorAll('.gallery-dot');

        if (track) {
            let currentIndex = 0;
            const total = track.children.length;

            function updateGallery(index) {
                currentIndex = index;
                track.style.transform = `translateX(-${index * 100}%)`;

                thumbs.forEach((thumb, i) => {
                    thumb.classList.toggle('thumb-active', i === index);
                });

                dots.forEach((dot, i) => {
                    dot.classList.toggle('bg-white', i === index);
                    dot.classList.toggle('bg-white/50', i !== index);
                });
            }

            prevBtn?.addEventListener('click', () => {
                updateGallery((currentIndex - 1 + total) % total);
            });

            nextBtn?.addEventListener('click', () => {
                updateGallery((currentIndex + 1) % total);
            });

            thumbs.forEach((thumb) => {
                thumb.addEventListener('click', () => {
                    updateGallery(Number(thumb.dataset.index));
                });
            });

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    updateGallery(Number(dot.dataset.index));
                });
            });
        }

        const mapElement = document.getElementById('mapa-avistamiento');

        if (mapElement && typeof L !== 'undefined') {
            const latInput = document.getElementById('latitud_avistamiento');
            const lngInput = document.getElementById('longitud_avistamiento');
            const coloniaInput = document.getElementById('colonia_barrio_avistamiento');
            const referenciaInput = document.getElementById('calle_referencias_avistamiento');
            const estadoTexto = document.getElementById('mapa-avistamiento-estado');
            const usarUbicacionBtn = document.getElementById('usarUbicacionActualBtn');

            const initialLat = latInput.value || "{{ $latitudMapa ?? '16.7569' }}";
            const initialLng = lngInput.value || "{{ $longitudMapa ?? '-93.1292' }}";

            const map = L.map('mapa-avistamiento').setView([parseFloat(initialLat), parseFloat(initialLng)], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            let marker = null;

            function updateMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

                    marker.on('dragend', function (e) {
                        const pos = e.target.getLatLng();
                        setLocationFields(pos.lat, pos.lng, true);
                    });
                }

                map.setView([lat, lng], 16);
            }

            function setLocationFields(lat, lng, doReverse = false) {
                latInput.value = lat;
                lngInput.value = lng;
                estadoTexto.textContent = `Ubicación seleccionada: ${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;

                updateMarker(lat, lng);

                if (doReverse) {
                    reverseGeocode(lat, lng);
                }
            }

            async function reverseGeocode(lat, lng) {
                try {
                    estadoTexto.textContent = 'Buscando dirección...';

                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
                    const data = await response.json();

                    const address = data.address || {};
                    const colonia =
                        address.suburb ||
                        address.neighbourhood ||
                        address.city_district ||
                        address.village ||
                        address.town ||
                        address.city ||
                        '';

                    const calleBase =
                        [address.road, address.house_number].filter(Boolean).join(' ').trim();

                    const referencia =
                        calleBase ||
                        data.display_name?.split(',').slice(0, 2).join(', ') ||
                        '';

                    if (colonia) {
                        coloniaInput.value = colonia;
                    }

                    if (referencia) {
                        referenciaInput.value = referencia;
                    }

                    estadoTexto.textContent = `Ubicación seleccionada: ${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;
                } catch (error) {
                    estadoTexto.textContent = `Ubicación seleccionada: ${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;
                }
            }

            map.on('click', function (e) {
                setLocationFields(e.latlng.lat, e.latlng.lng, true);
            });

            usarUbicacionBtn?.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    estadoTexto.textContent = 'Tu navegador no permite geolocalización.';
                    return;
                }

                estadoTexto.textContent = 'Obteniendo tu ubicación actual...';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        setLocationFields(lat, lng, true);
                    },
                    function () {
                        estadoTexto.textContent = 'No se pudo obtener tu ubicación actual.';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                    }
                );
            });

            if (latInput.value && lngInput.value) {
                setLocationFields(parseFloat(latInput.value), parseFloat(lngInput.value), false);
            }
        }
    });
</script>

@endsection