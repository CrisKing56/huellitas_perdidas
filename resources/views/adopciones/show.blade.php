@extends('layout.app')

@section('title', 'Detalle de mascota en adopción')

@section('meta_tags')
    <meta property="og:title" content="¡{{ $adopcion->nombre }} busca un hogar!" />
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($adopcion->descripcion ?? 'Conoce esta mascota en adopción en Huellitas Perdidas.', 160) }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="article" />

    @if(isset($adopcion->fotos) && $adopcion->fotos->count() > 0)
        <meta property="og:image" content="{{ asset('storage/' . $adopcion->fotos->sortBy('orden')->first()->url) }}" />
    @elseif($adopcion->fotoPrincipal)
        <meta property="og:image" content="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}" />
    @endif
@endsection

@section('content')

@php
    $fechaPublicacion = $adopcion->created_at ?? null;

    $telefonoCrudo = $adopcion->autor->whatsapp ?? $adopcion->autor->telefono ?? null;
    $telefonoLimpio = $telefonoCrudo ? preg_replace('/\D+/', '', $telefonoCrudo) : null;

    if ($telefonoLimpio && strlen($telefonoLimpio) === 10) {
        $telefonoLimpio = '52' . $telefonoLimpio;
    }

    $mensajeWhatsapp = "Hola, me interesa adoptar a {$adopcion->nombre} que vi en Huellitas Perdidas.";
    $whatsappUrl = $telefonoLimpio ? "https://wa.me/{$telefonoLimpio}?text=" . urlencode($mensajeWhatsapp) : null;

    $urlActual = urlencode(url()->current());
    $facebookShareUrl = "https://www.facebook.com/sharer/sharer.php?u=" . $urlActual;

    $authUserId = auth()->check() ? (auth()->user()->id_usuario ?? null) : null;
    $esAutor = auth()->check() && ((int) $authUserId === (int) ($adopcion->autor_usuario_id ?? 0));

    $puedeVerContacto = $puedeVerContacto ?? false;
    $solicitudAceptada = $solicitudAceptada ?? null;

    $clasesEstado = [
        'DISPONIBLE' => 'bg-green-100 text-green-700 border-green-200',
        'EN_PROCESO' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'ADOPTADA' => 'bg-purple-100 text-purple-700 border-purple-200',
        'PAUSADA' => 'bg-gray-100 text-gray-600 border-gray-200',
    ];

    $clase = $clasesEstado[$adopcion->estado] ?? 'bg-gray-100 text-gray-600 border-gray-200';

    $especieTexto = $adopcion->especie->nombre ?? match ((int) $adopcion->especie_id) {
        1 => 'Perro',
        2 => 'Gato',
        default => 'Mascota',
    };

    $razaTexto = $adopcion->raza->nombre ?? (!empty($adopcion->otra_raza) ? $adopcion->otra_raza : null);

    $fotosGaleria = collect();
    if (isset($adopcion->fotos) && $adopcion->fotos->count()) {
        $fotosGaleria = $adopcion->fotos->sortBy('orden')->values();
    } elseif ($adopcion->fotoPrincipal) {
        $fotosGaleria = collect([$adopcion->fotoPrincipal]);
    }

    $nombreAutor = $adopcion->autor->nombre ?? $adopcion->autor->name ?? 'Usuario';
    $telefonoAutor = $adopcion->autor->telefono ?? $adopcion->autor->whatsapp ?? 'No disponible';
    $correoAutor = $adopcion->autor->correo ?? $adopcion->autor->email ?? 'No disponible';

    $inicialesAutor = '';
    $partesNombre = preg_split('/\s+/', trim($nombreAutor));
    foreach (array_slice($partesNombre, 0, 2) as $parte) {
        $inicialesAutor .= mb_strtoupper(mb_substr($parte, 0, 1));
    }
    if ($inicialesAutor === '') {
        $inicialesAutor = 'U';
    }

    $colonia = $adopcion->colonia_barrio ?? null;
    $referencias = $adopcion->calle_referencias ?? null;
    $colorPredominante = $adopcion->color_predominante ?? null;
    $condicionSalud = $adopcion->condicion_salud ?? null;
    $vacunas = $adopcion->vacunas_aplicadas ?? null;
    $descripcionSalud = $adopcion->descripcion_salud ?? null;
    $requisitos = $adopcion->requisitos ?? null;
    $esterilizado = isset($adopcion->esterilizado) ? $adopcion->esterilizado : null;

    $latitud = $adopcion->latitud ?? null;
    $longitud = $adopcion->longitud ?? null;
    $hayCoordenadas = is_numeric($latitud) && is_numeric($longitud);
    $mapaEmbedUrl = $hayCoordenadas
        ? "https://www.google.com/maps?q={$latitud},{$longitud}&z=16&output=embed"
        : null;

    $mostrarWhatsapp = !$esAutor && $puedeVerContacto && !empty($whatsappUrl);

    $mascotasRelacionadas = ($mascotasRelacionadas ?? collect())
        ->filter(fn($m) => (int) $m->id_publicacion !== (int) $adopcion->id_publicacion)
        ->values();
@endphp

<style>
    .gallery-slide {
        min-width: 100%;
    }

    .thumb-active {
        outline: 3px solid #16a34a;
        outline-offset: 2px;
    }
</style>

<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-8 lg:py-10">

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-5">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="{{ $clase }} border px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide">
                                {{ str_replace('_', ' ', $adopcion->estado) }}
                            </span>

                            <span class="inline-flex items-center rounded-full bg-green-50 text-green-600 px-3 py-1 text-[11px] font-semibold border border-green-100 uppercase tracking-wide">
                                {{ $especieTexto }}
                            </span>

                            @if($fechaPublicacion)
                                <span class="text-sm text-gray-500">
                                    Publicado {{ \Carbon\Carbon::parse($fechaPublicacion)->locale('es')->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                            {{ $adopcion->nombre }}
                        </h1>

                        <div class="mt-3 flex flex-wrap items-center gap-2 text-base text-gray-500">
                            <span>{{ $especieTexto }}</span>

                            @if($razaTexto)
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>{{ $razaTexto }}</span>
                            @endif

                            @if($adopcion->sexo)
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>{{ ucfirst(mb_strtolower($adopcion->sexo)) }}</span>
                            @endif
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
                                                    alt="{{ $adopcion->nombre }}"
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
                                Sin fotografías disponibles
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900">Resumen de adopción</h2>
                        <p class="text-sm text-gray-500 mt-1">Datos clave y acciones disponibles.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Edad</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $adopcion->edad_anios !== null ? $adopcion->edad_anios . ' año(s)' : 'No especificada' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Ubicación</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $colonia ?: 'No especificada' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Tamaño</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $adopcion->tamano ? ucfirst(mb_strtolower($adopcion->tamano)) : 'No especificado' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Color</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $colorPredominante ?: 'No especificado' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    {{ $inicialesAutor }}
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">Responsable</p>
                                    <p class="text-gray-900 font-bold truncate">{{ $nombreAutor }}</p>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                @if($esAutor || $puedeVerContacto)
                                    <p class="text-xs text-gray-400 mb-1">Teléfono</p>
                                    <p class="text-sm font-semibold text-gray-800 break-words">
                                        {{ $telefonoAutor }}
                                    </p>

                                    <p class="text-xs text-gray-400 mb-1 mt-3">Correo</p>
                                    <p class="text-sm font-semibold text-gray-800 break-words">
                                        {{ $correoAutor }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Los datos de contacto del responsable se desbloquean únicamente si tu solicitud es aceptada.
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if(auth()->check() && !$esAutor && $puedeVerContacto)
                            <div class="rounded-2xl border border-green-200 bg-green-50 p-4">
                                <p class="text-sm font-semibold text-green-800">
                                    Tu solicitud fue aceptada. Ya puedes ponerte en contacto con el responsable.
                                </p>
                            </div>
                        @endif

                        @if($esAutor)
                            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                                <p class="text-sm font-semibold text-blue-800">
                                    Esta publicación es tuya. No puedes escribirte a ti mismo por WhatsApp.
                                </p>
                            </div>
                        @endif

                        <div class="space-y-3">
                            @if($esAutor)
                                <a
                                    href="{{ route('adopciones.solicitudes.recibidas') }}"
                                    class="w-full inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition"
                                >
                                    Ver solicitudes recibidas
                                </a>

                                @if($adopcion->estado === 'EN_PROCESO')
                                    <form action="{{ route('adopciones.marcarAdoptada', $adopcion->id_publicacion) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="w-full inline-flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition"
                                        >
                                            Marcar como adoptada
                                        </button>
                                    </form>
                                @endif

                                @if($adopcion->estado === 'ADOPTADA')
                                    <form action="{{ route('adopciones.volverEnProceso', $adopcion->id_publicacion) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="w-full inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-5 rounded-xl transition"
                                        >
                                            Volver a en proceso
                                        </button>
                                    </form>
                                @endif
                            @else
                                @auth
                                    @if($adopcion->estado === 'DISPONIBLE' && !$puedeVerContacto)
                                        <a
                                            href="{{ route('adopciones.solicitudes.create', $adopcion->id_publicacion) }}"
                                            class="w-full inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition"
                                        >
                                            Solicitar adopción
                                        </a>
                                    @elseif($adopcion->estado === 'EN_PROCESO' && !$puedeVerContacto)
                                        <button
                                            type="button"
                                            disabled
                                            class="w-full inline-flex items-center justify-center bg-yellow-100 text-yellow-800 font-bold py-3.5 px-5 rounded-xl cursor-not-allowed"
                                        >
                                            Proceso de adopción en curso
                                        </button>
                                    @elseif($adopcion->estado === 'ADOPTADA' && !$puedeVerContacto)
                                        <button
                                            type="button"
                                            disabled
                                            class="w-full inline-flex items-center justify-center bg-purple-100 text-purple-800 font-bold py-3.5 px-5 rounded-xl cursor-not-allowed"
                                        >
                                            Esta mascota ya fue adoptada
                                        </button>
                                    @endif

                                    <a
                                        href="{{ route('adopciones.solicitudes.enviadas') }}"
                                        class="w-full inline-flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-3.5 px-5 rounded-xl border border-blue-100 transition"
                                    >
                                        Mis solicitudes
                                    </a>
                                @endauth

                                @guest
                                    <a
                                        href="{{ route('login') }}"
                                        class="w-full inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition"
                                    >
                                        Inicia sesión para solicitar
                                    </a>
                                @endguest
                            @endif

                            @if($mostrarWhatsapp)
                                <a
                                    href="{{ $whatsappUrl }}"
                                    target="_blank"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3.5 px-5 rounded-xl shadow-sm transition"
                                >
                                    <svg class="w-5 h-5" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                                        <path d="M19.11 17.39c-.3-.15-1.77-.87-2.05-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.08-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.8-1.67-2.1-.18-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.08-.15-.67-1.62-.92-2.23-.24-.57-.48-.5-.67-.51h-.57c-.2 0-.52.08-.8.38-.27.3-1.05 1.02-1.05 2.48s1.08 2.88 1.23 3.08c.15.2 2.13 3.25 5.17 4.56.72.31 1.28.5 1.72.64.72.23 1.37.2 1.89.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                                        <path d="M16.03 3.2C9.03 3.2 3.34 8.88 3.34 15.9c0 2.23.58 4.4 1.68 6.32L3 29l6.98-1.83a12.62 12.62 0 0 0 6.05 1.54h.01c7 0 12.69-5.69 12.69-12.7 0-3.39-1.32-6.58-3.72-8.98a12.58 12.58 0 0 0-8.98-3.72zm0 23.25h-.01a10.52 10.52 0 0 1-5.36-1.47l-.38-.23-4.14 1.09 1.1-4.03-.25-.41a10.51 10.51 0 0 1-1.62-5.5c0-5.8 4.72-10.52 10.53-10.52 2.81 0 5.45 1.09 7.43 3.08a10.45 10.45 0 0 1 3.08 7.44c0 5.8-4.72 10.52-10.52 10.52z"/>
                                    </svg>
                                    Escribir por WhatsApp
                                </a>
                            @elseif(!$esAutor && !$puedeVerContacto)
                                <button
                                    type="button"
                                    disabled
                                    class="w-full inline-flex items-center justify-center bg-gray-200 text-gray-500 font-bold py-3.5 px-5 rounded-xl cursor-not-allowed"
                                >
                                    Contacto disponible solo si eres aceptado
                                </button>
                            @endif

                            <a
                                href="{{ $facebookShareUrl }}"
                                target="_blank"
                                class="w-full inline-flex items-center justify-center gap-2 border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-3.5 px-5 rounded-xl transition"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.019 4.388 10.998 10.125 11.854v-8.385H7.078v-3.47h3.047V9.428c0-3.007 1.792-4.67 4.533-4.67 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.49 0-1.956.926-1.956 1.874v2.252h3.328l-.532 3.469h-2.796v8.385C19.612 23.07 24 18.09 24 12.073z"/>
                                </svg>
                                Compartir en Facebook
                            </a>

                            @if($esAutor)
                                <a
                                    href="{{ route('adopciones.edit', $adopcion->id_publicacion) }}"
                                    class="w-full inline-flex items-center justify-center border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3.5 px-5 rounded-xl transition"
                                >
                                    Editar publicación
                                </a>
                            @endif
                        </div>

                        <p class="text-xs text-center text-gray-400 leading-tight">
                            El contacto con el responsable solo se habilita cuando la solicitud fue aceptada.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mt-10">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900">Información de la mascota</h3>
                    <p class="text-sm text-gray-500 mt-1">Detalles para conocerla mejor.</p>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="space-y-6">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Descripción</p>
                            <p class="text-gray-700 leading-relaxed">
                                {{ $adopcion->descripcion ?: 'Sin descripción disponible.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Especie</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $especieTexto }}</p>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Raza</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $razaTexto ?: 'No especificada' }}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-3">Características</p>
                            <div class="flex flex-wrap gap-2">
                                @if($adopcion->sexo)
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                        Sexo: {{ ucfirst(mb_strtolower($adopcion->sexo)) }}
                                    </span>
                                @endif

                                @if($adopcion->tamano)
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                        Tamaño: {{ ucfirst(mb_strtolower($adopcion->tamano)) }}
                                    </span>
                                @endif

                                @if($colorPredominante)
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                        Color: {{ $colorPredominante }}
                                    </span>
                                @endif

                                @if($adopcion->edad_anios !== null)
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium border border-gray-200">
                                        Edad: {{ $adopcion->edad_anios }} año(s)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-3">Mapa de ubicación</p>

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
                                    Cerca de: <span class="font-medium text-gray-700">{{ $colonia ?: 'Ubicación registrada' }}</span>
                                </p>
                            @else
                                <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center">
                                    <p class="text-gray-500 font-medium">No hay coordenadas exactas para esta mascota.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900">Salud y ubicación</h3>
                    <p class="text-sm text-gray-500 mt-1">Información complementaria de la publicación.</p>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Condición de salud</p>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $condicionSalud ?: 'No especificada por el momento.' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Vacunas y tratamientos</p>
                        <ul class="space-y-2 text-gray-700">
                            <li>
                                <span class="font-medium">Vacunas:</span>
                                {{ $vacunas ?: 'No especificadas.' }}
                            </li>

                            <li>
                                <span class="font-medium">Esterilizado:</span>
                                @if($esterilizado === null || $esterilizado === '')
                                    No especificado.
                                @else
                                    {{ (int) $esterilizado === 1 ? 'Sí' : 'No' }}
                                @endif
                            </li>

                            @if($descripcionSalud)
                                <li>
                                    <span class="font-medium">Notas:</span>
                                    {{ $descripcionSalud }}
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Requisitos del adoptante</p>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $requisitos ?: 'No especificados por el momento.' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Ubicación de referencia</p>

                        @if($colonia || $referencias)
                            <div class="space-y-2">
                                @if($colonia)
                                    <p class="text-gray-800 font-medium">{{ $colonia }}</p>
                                @endif

                                @if($referencias)
                                    <p class="text-gray-600">{{ $referencias }}</p>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-600">
                                La ubicación todavía no fue guardada en esta publicación.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <section class="mt-14">
            <div class="flex items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Otras mascotas que buscan un hogar</h2>
                    <p class="text-sm text-gray-500 mt-1">Más publicaciones de adopción que podrían interesarte.</p>
                </div>

                <a href="{{ route('adopciones.index') }}"
                   class="hidden md:inline-flex items-center text-sm font-semibold text-green-600 hover:text-green-700 transition">
                    Ver todas
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @forelse($mascotasRelacionadas as $mascota)
                    @php
                        $especieRelacionada = $mascota->especie->nombre ?? match ((int) $mascota->especie_id) {
                            1 => 'Perro',
                            2 => 'Gato',
                            default => 'Mascota',
                        };
                    @endphp

                    <a href="{{ route('adopciones.show', $mascota->id_publicacion) }}">
                        <div class="bg-white rounded-3xl shadow-sm hover:shadow-lg transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                            <div class="h-64 relative overflow-hidden bg-gray-100">
                                @if($mascota->fotoPrincipal)
                                    <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}"
                                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                                @elseif(isset($mascota->fotos) && $mascota->fotos->count())
                                    <img src="{{ asset('storage/' . $mascota->fotos->sortBy('orden')->first()->url) }}"
                                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                        <span class="text-sm">Sin imagen</span>
                                    </div>
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>

                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold shadow-sm uppercase tracking-wide bg-green-100 text-green-600">
                                    En adopción
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
                                    <svg class="w-4 h-4 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $mascota->colonia_barrio ?: 'Ubicación no especificada' }}
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <span class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-green-50 text-green-600 font-semibold py-2.5 border border-green-100 group-hover:bg-green-600 group-hover:text-white group-hover:border-green-600 transition">
                                        Ver publicación
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
                        <p class="text-gray-500 text-lg">No hay más mascotas en adopción para mostrar por ahora.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

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
    });
</script>

@endsection