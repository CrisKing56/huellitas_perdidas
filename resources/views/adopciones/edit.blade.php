@extends('layout.app')

@section('title', 'Editar publicación de adopción')

@section('content')
@php
    $razasPorEspecie = $razas->groupBy('especie_id')->map(function ($items) {
        return $items->map(function ($raza) {
            return [
                'id_raza' => $raza->id_raza,
                'nombre' => $raza->nombre,
            ];
        })->values();
    });

    $googleMapsApiKey = config('services.google_maps.api_key') ?: env('GOOGLE_MAPS_API_KEY');

    $razaInicial = old('raza_id', $adopcion->raza_id);
    $otraRazaInicial = old('otra_raza', $adopcion->otra_raza ?? '');
    $esterilizadoInicial = old('esterilizado', isset($adopcion->esterilizado) ? (string) (int) $adopcion->esterilizado : '');
@endphp

<div class="bg-gradient-to-b from-white via-green-50/30 to-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        {{-- TOAST DE ÉXITO --}}
        @if(session('success'))
            <div id="toast-success"
                 class="fixed top-5 right-5 z-50 max-w-sm w-[92%] sm:w-full bg-white border border-green-200 shadow-xl rounded-2xl overflow-hidden">
                <div class="flex items-start gap-3 p-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-900">Cambios guardados</p>
                        <p class="text-sm text-gray-600 mt-1">{{ session('success') }}</p>
                    </div>
                    <button type="button" id="cerrar-toast-success" class="text-gray-400 hover:text-gray-600 text-xl leading-none">
                        ×
                    </button>
                </div>
                <div class="h-1 bg-green-500" id="toast-success-bar"></div>
            </div>
        @endif

        <div class="mb-8">
            <a href="{{ route('adopciones.mis-adopciones') }}"
               class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a mis adopciones
            </a>

            <div class="bg-white rounded-3xl border border-green-100 shadow-sm p-6 sm:p-8 relative overflow-hidden">
                <div class="absolute inset-y-0 right-0 w-40 bg-gradient-to-l from-green-100/60 to-transparent pointer-events-none"></div>

                <div class="max-w-3xl relative z-10">
                    <span class="inline-flex items-center rounded-full bg-green-50 text-green-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-green-100">
                        Editar publicación
                    </span>

                    <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        Actualiza la información de la mascota
                    </h1>

                    <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                        Modifica los datos necesarios para mantener la publicación actualizada y más clara para futuros adoptantes.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">
                            Información general
                        </span>
                        <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">
                            Salud y requisitos
                        </span>
                        <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">
                            Ubicación y fotos
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl mb-6 shadow-sm">
                <strong class="font-bold">Revisa estos campos:</strong>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-editar-adopcion"
              action="{{ route('adopciones.update', $adopcion->id_publicacion) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                <div class="xl:col-span-8 space-y-8">

                    {{-- INFORMACIÓN DE LA MASCOTA --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Información de la mascota</h2>
                                <p class="text-sm text-gray-500 mt-1">Edita los datos principales de la publicación.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la mascota <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="nombre"
                                       value="{{ old('nombre', $adopcion->nombre) }}"
                                       placeholder="Ej: Luna"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Especie <span class="text-red-500">*</span>
                                </label>
                                <select name="especie_id"
                                        id="especie_id"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-green-500 focus:ring-green-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach($especies as $especie)
                                        <option value="{{ $especie->id_especie }}" {{ (string) old('especie_id', $adopcion->especie_id) === (string) $especie->id_especie ? 'selected' : '' }}>
                                            {{ $especie->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Raza
                                </label>
                                <input type="hidden" name="raza_id" id="raza_id_real" value="{{ $razaInicial }}">

                                <select id="raza_selector"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-green-500 focus:ring-green-500">
                                    <option value="">Seleccionar...</option>
                                </select>

                                <p class="text-xs text-gray-400 mt-2">Si no aparece en la lista, selecciona “Otra raza”.</p>
                            </div>

                            <div id="bloque_otra_raza" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Otra raza
                                </label>
                                <input type="text"
                                       name="otra_raza"
                                       id="otra_raza"
                                       value="{{ $otraRazaInicial }}"
                                       placeholder="Ej: Criollo, mestizo, mezcla..."
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Edad (años)
                                </label>
                                <input type="number"
                                       name="edad_anios"
                                       value="{{ old('edad_anios', $adopcion->edad_anios) }}"
                                       min="0"
                                       max="30"
                                       placeholder="Ej: 2"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sexo <span class="text-red-500">*</span>
                                </label>
                                <select name="sexo"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-green-500 focus:ring-green-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="MACHO" {{ old('sexo', $adopcion->sexo) == 'MACHO' ? 'selected' : '' }}>Macho</option>
                                    <option value="HEMBRA" {{ old('sexo', $adopcion->sexo) == 'HEMBRA' ? 'selected' : '' }}>Hembra</option>
                                    <option value="DESCONOCIDO" {{ old('sexo', $adopcion->sexo) == 'DESCONOCIDO' ? 'selected' : '' }}>Desconocido</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tamaño <span class="text-red-500">*</span>
                                </label>
                                <select name="tamano"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-green-500 focus:ring-green-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="CHICO" {{ old('tamano', $adopcion->tamano) == 'CHICO' ? 'selected' : '' }}>Chico</option>
                                    <option value="MEDIANO" {{ old('tamano', $adopcion->tamano) == 'MEDIANO' ? 'selected' : '' }}>Mediano</option>
                                    <option value="GRANDE" {{ old('tamano', $adopcion->tamano) == 'GRANDE' ? 'selected' : '' }}>Grande</option>
                                    <option value="DESCONOCIDO" {{ old('tamano', $adopcion->tamano) == 'DESCONOCIDO' ? 'selected' : '' }}>Desconocido</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Color predominante
                                </label>
                                <input type="text"
                                       name="color_predominante"
                                       value="{{ old('color_predominante', $adopcion->color_predominante) }}"
                                       placeholder="Ej: Blanco con café"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea name="descripcion"
                                      rows="6"
                                      class="w-full rounded-2xl border border-gray-300 bg-white py-4 px-4 focus:border-green-500 focus:ring-green-500 placeholder-gray-400"
                                      placeholder="Describe la personalidad, comportamiento o cualquier detalle importante...">{{ old('descripcion', $adopcion->descripcion) }}</textarea>
                        </div>
                    </section>

                    {{-- SALUD Y REQUISITOS --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Salud y adopción responsable</h2>
                                <p class="text-sm text-gray-500 mt-1">Edita la información complementaria de la mascota.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vacunas aplicadas</label>
                                <input type="text"
                                       name="vacunas_aplicadas"
                                       value="{{ old('vacunas_aplicadas', $adopcion->vacunas_aplicadas) }}"
                                       placeholder="Ej: Rabia, parvovirus"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">¿Está esterilizado?</label>
                                <select name="esterilizado"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-green-500 focus:ring-green-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="0" {{ $esterilizadoInicial === '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $esterilizadoInicial === '1' ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Condición de salud</label>
                            <input type="text"
                                   name="condicion_salud"
                                   value="{{ old('condicion_salud', $adopcion->condicion_salud) }}"
                                   placeholder="Ej: Buena, en tratamiento, requiere seguimiento"
                                   class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción detallada de salud</label>
                            <textarea name="descripcion_salud"
                                      rows="4"
                                      placeholder="Ej: Fue rescatado, ya fue revisado por veterinario, toma vitaminas..."
                                      class="w-full rounded-2xl border border-gray-300 bg-white py-4 px-4 focus:border-green-500 focus:ring-green-500">{{ old('descripcion_salud', $adopcion->descripcion_salud) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requisitos para el adoptante</label>
                            <textarea name="requisitos"
                                      rows="4"
                                      placeholder="Ej: Ser mayor de edad, compromiso de cuidados, seguimiento por WhatsApp..."
                                      class="w-full rounded-2xl border border-gray-300 bg-white py-4 px-4 focus:border-green-500 focus:ring-green-500">{{ old('requisitos', $adopcion->requisitos) }}</textarea>
                        </div>
                    </section>

                    {{-- UBICACIÓN --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Ubicación de referencia</h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Puedes buscar una ubicación o mover el pin para autocompletar la referencia.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                            <div class="lg:col-span-1 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar ubicación</label>
                                    <input id="map-search"
                                           type="text"
                                           placeholder="Busca una calle, colonia o referencia"
                                           class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                                </div>

                                <button type="button"
                                        id="usar-mi-ubicacion"
                                        class="w-full inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-2xl shadow-sm transition">
                                    Usar mi ubicación actual
                                </button>

                                <div class="rounded-2xl border border-green-100 bg-green-50 p-4 text-sm text-gray-700">
                                    <p class="font-semibold text-gray-800 mb-1">Sugerencia</p>
                                    <p>Puedes mover el pin o buscar una referencia para visualizar mejor la zona.</p>
                                </div>

                                <p id="mapa-estado" class="text-xs text-gray-500"></p>
                            </div>

                            <div class="lg:col-span-2">
                                @if($googleMapsApiKey)
                                    <div id="mapa-formulario" class="w-full h-[380px] rounded-3xl border border-gray-200 overflow-hidden shadow-sm"></div>
                                @else
                                    <div class="w-full h-[380px] rounded-3xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center text-center px-6">
                                        <div class="bg-green-100 p-3 rounded-full mb-3">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9"></path>
                                            </svg>
                                        </div>
                                        <p class="font-semibold text-gray-800">Falta configurar Google Maps</p>
                                        <p class="text-sm text-gray-500 mt-1">Agrega tu API Key para activar el mapa.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Colonia o barrio
                                </label>
                                <input type="text"
                                       name="colonia_barrio"
                                       id="colonia_barrio"
                                       value="{{ old('colonia_barrio', $adopcion->colonia_barrio) }}"
                                       placeholder="Ej: Centro, Barrio Norte"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Calle y referencias
                                </label>
                                <input type="text"
                                       name="calle_referencias"
                                       id="calle_referencias"
                                       value="{{ old('calle_referencias', $adopcion->calle_referencias) }}"
                                       placeholder="Ej: Frente al parque"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>

                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $adopcion->latitud ?? '16.9060') }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $adopcion->longitud ?? '-92.0933') }}">
                    </section>
                </div>

                <div class="xl:col-span-4 space-y-8">

                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 xl:sticky xl:top-24 space-y-8">

                        {{-- FOTOS ACTUALES --}}
                        <div>
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Fotografías actuales</h2>
                                    <p class="text-sm text-gray-500 mt-1">Estas son las fotos guardadas en tu publicación.</p>
                                </div>
                            </div>

                            @if($adopcion->fotos && $adopcion->fotos->count())
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($adopcion->fotos->sortBy('orden') as $foto)
                                        <div class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm group">
                                            <img src="{{ asset('storage/' . $foto->url) }}"
                                                 alt="Foto actual"
                                                 class="w-full h-36 object-cover transition duration-300 group-hover:scale-[1.03]">
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($adopcion->fotoPrincipal)
                                <div class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">
                                    <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}"
                                         alt="Foto actual"
                                         class="w-full h-56 object-cover">
                                </div>
                            @else
                                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-500">
                                    Esta publicación todavía no tiene fotos visibles.
                                </div>
                            @endif
                        </div>

                        {{-- REEMPLAZAR FOTO PRINCIPAL --}}
                        <div class="border-t border-gray-100 pt-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Reemplazar foto principal</h3>
                            <p class="text-sm text-gray-500 mb-5">
                                Si eliges una imagen aquí, se usará como nueva foto principal. Si es pesada, se optimizará automáticamente antes de enviarse.
                            </p>

                            <div class="border-2 border-dashed border-gray-300 rounded-3xl bg-gray-50 p-6 text-center hover:bg-gray-100 transition">
                                <div class="bg-green-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </div>

                                <label class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-2xl shadow cursor-pointer transition">
                                    Seleccionar nueva foto principal
                                    <input type="file" name="foto" id="foto_principal" class="hidden" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                </label>

                                <p id="nombre-foto-principal" class="mt-4 text-sm text-gray-500">No has seleccionado una nueva foto principal</p>
                                <p id="estado-foto-principal" class="mt-2 text-xs text-gray-400"></p>

                                <div id="preview-foto-principal-wrapper" class="mt-6 hidden">
                                    <div class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">
                                        <img id="preview-foto-principal" src="" alt="Vista previa" class="w-full h-52 object-cover">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- AGREGAR MÁS FOTOS --}}
                        <div class="border-t border-gray-100 pt-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Agregar fotos adicionales</h3>
                            <p class="text-sm text-gray-500 mb-2">
                                Puedes subir hasta <strong>8 fotos nuevas</strong>. Si alguna pesa más de <strong>7 MB</strong>, se comprimirá automáticamente.
                            </p>
                            <p class="text-xs text-gray-400 mb-5">
                                Formatos permitidos: JPG, JPEG, PNG y WEBP.
                            </p>

                            <div class="border-2 border-dashed border-gray-300 rounded-3xl bg-gray-50 p-6 text-center hover:bg-gray-100 transition">
                                <div class="bg-green-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </div>

                                <label class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-2xl shadow cursor-pointer transition">
                                    Seleccionar fotos adicionales
                                    <input type="file" name="fotos[]" id="fotos" class="hidden" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>
                                </label>

                                <p id="nombre-archivo" class="mt-4 text-sm text-gray-500">Ningún archivo seleccionado</p>
                                <p id="estado-fotos" class="mt-2 text-xs text-gray-400"></p>

                                <div id="preview-wrapper" class="mt-6 hidden">
                                    <div id="preview-grid" class="grid grid-cols-2 gap-4"></div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <h3 class="text-sm font-bold text-gray-900 mb-2">Datos de contacto</h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-400">Nombre del responsable</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->nombre ?? 'Usuario' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Teléfono registrado</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->telefono ?? 'No registrado' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 pt-2">
                <button type="submit"
                        id="btn-guardar-cambios"
                        class="w-full md:w-auto inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-8 rounded-2xl shadow transition disabled:opacity-70 disabled:cursor-not-allowed">
                    <span id="btn-texto">Guardar cambios</span>
                    <svg id="btn-spinner" class="hidden animate-spin ml-2 w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>

                <a href="{{ route('adopciones.mis-adopciones') }}"
                   class="w-full md:w-auto inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-8 rounded-2xl transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const razasPorEspecie = @json($razasPorEspecie);
    const razaIdInicial = @json($razaInicial);
    const otraRazaInicial = @json($otraRazaInicial);
    const googleMapsApiKey = @json($googleMapsApiKey);

    const especieSelect = document.getElementById('especie_id');
    const razaSelector = document.getElementById('raza_selector');
    const razaIdReal = document.getElementById('raza_id_real');
    const bloqueOtraRaza = document.getElementById('bloque_otra_raza');
    const inputOtraRaza = document.getElementById('otra_raza');

    const formEditar = document.getElementById('form-editar-adopcion');
    const btnGuardar = document.getElementById('btn-guardar-cambios');
    const btnTexto = document.getElementById('btn-texto');
    const btnSpinner = document.getElementById('btn-spinner');

    const inputFotoPrincipal = document.getElementById('foto_principal');
    const nombreFotoPrincipal = document.getElementById('nombre-foto-principal');
    const estadoFotoPrincipal = document.getElementById('estado-foto-principal');
    const previewFotoPrincipalWrapper = document.getElementById('preview-foto-principal-wrapper');
    const previewFotoPrincipal = document.getElementById('preview-foto-principal');

    const inputFotos = document.getElementById('fotos');
    const previewWrapper = document.getElementById('preview-wrapper');
    const previewGrid = document.getElementById('preview-grid');
    const nombreArchivo = document.getElementById('nombre-archivo');
    const estadoFotos = document.getElementById('estado-fotos');

    let dtFotos = new DataTransfer();
    let procesandoImagenes = false;

    const MAX_FOTOS_NUEVAS = 8;
    const MB = 1024 * 1024;
    const LIMITE_COMPRESION = 7 * MB;
    const LIMITE_DURO = 25 * MB;
    const MAX_DIMENSION = 1600;

    function mostrarOtraRaza() {
        bloqueOtraRaza.classList.remove('hidden');
        inputOtraRaza.disabled = false;
    }

    function ocultarOtraRaza() {
        bloqueOtraRaza.classList.add('hidden');
        inputOtraRaza.disabled = true;
    }

    function cargarRazas(mantenerSeleccion = true) {
        const especieId = especieSelect.value;
        const valorActual = mantenerSeleccion ? (razaIdReal.value || (inputOtraRaza.value ? '__otra__' : '')) : '';

        razaSelector.innerHTML = '<option value="">Seleccionar...</option>';

        if (especieId && razasPorEspecie[especieId]) {
            razasPorEspecie[especieId].forEach(function(raza) {
                const option = document.createElement('option');
                option.value = raza.id_raza;
                option.textContent = raza.nombre;

                if (String(valorActual) === String(raza.id_raza)) {
                    option.selected = true;
                }

                razaSelector.appendChild(option);
            });
        }

        const otraOption = document.createElement('option');
        otraOption.value = '__otra__';
        otraOption.textContent = 'Otra raza';

        if (valorActual === '__otra__') {
            otraOption.selected = true;
        }

        razaSelector.appendChild(otraOption);
        manejarCambioRaza();
    }

    function manejarCambioRaza() {
        if (razaSelector.value === '__otra__') {
            razaIdReal.value = '';
            mostrarOtraRaza();
            return;
        }

        if (razaSelector.value) {
            razaIdReal.value = razaSelector.value;
            ocultarOtraRaza();
            return;
        }

        if (inputOtraRaza.value.trim() !== '') {
            mostrarOtraRaza();
            razaIdReal.value = '';
            return;
        }

        razaIdReal.value = '';
        ocultarOtraRaza();
    }

    especieSelect.addEventListener('change', function () {
        razaIdReal.value = '';
        inputOtraRaza.value = '';
        cargarRazas(false);
    });

    razaSelector.addEventListener('change', manejarCambioRaza);

    function setBotonProcesando(estado, texto = 'Guardando cambios...') {
        procesandoImagenes = estado;
        btnGuardar.disabled = estado;

        if (estado) {
            btnTexto.textContent = texto;
            btnSpinner.classList.remove('hidden');
        } else {
            btnTexto.textContent = 'Guardar cambios';
            btnSpinner.classList.add('hidden');
        }
    }

    function formatoValido(file) {
        const tipos = ['image/jpeg', 'image/png', 'image/webp'];
        return tipos.includes(file.type);
    }

    function leerComoDataURL(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = e => resolve(e.target.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function cargarImagen(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = src;
        });
    }

    function canvasABlob(canvas, quality = 0.82, type = 'image/webp') {
        return new Promise(resolve => {
            canvas.toBlob(blob => resolve(blob), type, quality);
        });
    }

    async function comprimirImagenSiEsNecesario(file) {
        if (!formatoValido(file)) {
            throw new Error(`El archivo "${file.name}" no tiene un formato permitido.`);
        }

        if (file.size > LIMITE_DURO) {
            throw new Error(`La imagen "${file.name}" es demasiado pesada. Intenta con una menor a 25 MB.`);
        }

        if (file.size <= LIMITE_COMPRESION) {
            return {
                file,
                comprimida: false,
                mensaje: `${file.name} no necesitó compresión.`
            };
        }

        const dataUrl = await leerComoDataURL(file);
        const img = await cargarImagen(dataUrl);

        let width = img.width;
        let height = img.height;

        if (width > height && width > MAX_DIMENSION) {
            height = Math.round((height * MAX_DIMENSION) / width);
            width = MAX_DIMENSION;
        } else if (height >= width && height > MAX_DIMENSION) {
            width = Math.round((width * MAX_DIMENSION) / height);
            height = MAX_DIMENSION;
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);

        let qualityLevels = [0.86, 0.80, 0.74, 0.68, 0.60];
        let finalBlob = null;

        for (const quality of qualityLevels) {
            const blob = await canvasABlob(canvas, quality, 'image/webp');
            if (!blob) continue;

            finalBlob = blob;

            if (blob.size <= 7 * MB) {
                break;
            }
        }

        if (!finalBlob) {
            throw new Error(`No se pudo procesar la imagen "${file.name}".`);
        }

        const nuevoNombre = file.name.replace(/\.[^.]+$/, '') + '.webp';
        const nuevoArchivo = new File([finalBlob], nuevoNombre, { type: 'image/webp' });

        return {
            file: nuevoArchivo,
            comprimida: true,
            mensaje: `${file.name} fue optimizada automáticamente.`
        };
    }

    function mostrarPreviewPrincipal(file) {
        if (!file) {
            nombreFotoPrincipal.textContent = 'No has seleccionado una nueva foto principal';
            previewFotoPrincipalWrapper.classList.add('hidden');
            estadoFotoPrincipal.textContent = '';
            return;
        }

        nombreFotoPrincipal.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(ev) {
            previewFotoPrincipal.src = ev.target.result;
            previewFotoPrincipalWrapper.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    inputFotoPrincipal.addEventListener('change', async function(e) {
        const archivo = e.target.files[0];

        if (!archivo) {
            mostrarPreviewPrincipal(null);
            return;
        }

        try {
            setBotonProcesando(true, 'Procesando imagen...');
            estadoFotoPrincipal.textContent = 'Procesando foto principal...';

            const resultado = await comprimirImagenSiEsNecesario(archivo);

            const nuevoDT = new DataTransfer();
            nuevoDT.items.add(resultado.file);
            inputFotoPrincipal.files = nuevoDT.files;

            mostrarPreviewPrincipal(resultado.file);
            estadoFotoPrincipal.textContent = resultado.mensaje;
        } catch (error) {
            inputFotoPrincipal.value = '';
            mostrarPreviewPrincipal(null);
            estadoFotoPrincipal.textContent = '';
            alert(error.message || 'No se pudo procesar la foto principal.');
        } finally {
            setBotonProcesando(false);
        }
    });

    function renderPreviewFotos() {
        previewGrid.innerHTML = '';

        const files = Array.from(dtFotos.files);
        nombreArchivo.textContent = files.length
            ? `${files.length} foto(s) seleccionada(s)`
            : 'Ningún archivo seleccionado';

        if (!files.length) {
            previewWrapper.classList.add('hidden');
            estadoFotos.textContent = '';
            return;
        }

        previewWrapper.classList.remove('hidden');

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'relative rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm';

                item.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-36 object-cover" alt="Vista previa">
                    <div class="p-2 text-xs text-gray-600 truncate">${file.name}</div>
                    <button type="button"
                            data-index="${index}"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow">
                        ×
                    </button>
                `;

                previewGrid.appendChild(item);

                item.querySelector('button').addEventListener('click', function() {
                    const removeIndex = parseInt(this.dataset.index);
                    const nuevoDT = new DataTransfer();

                    Array.from(dtFotos.files).forEach((f, i) => {
                        if (i !== removeIndex) {
                            nuevoDT.items.add(f);
                        }
                    });

                    dtFotos = nuevoDT;
                    inputFotos.files = dtFotos.files;
                    renderPreviewFotos();
                });
            };
            reader.readAsDataURL(file);
        });
    }

    inputFotos.addEventListener('change', async function(e) {
        const nuevos = Array.from(e.target.files);

        if (!nuevos.length) {
            return;
        }

        if ((dtFotos.files.length + nuevos.length) > MAX_FOTOS_NUEVAS) {
            alert(`Solo puedes subir hasta ${MAX_FOTOS_NUEVAS} fotografías nuevas.`);
            e.target.value = '';
            return;
        }

        try {
            setBotonProcesando(true, 'Procesando imágenes...');
            estadoFotos.textContent = 'Optimizando fotos, por favor espera...';

            for (const archivo of nuevos) {
                const resultado = await comprimirImagenSiEsNecesario(archivo);
                dtFotos.items.add(resultado.file);
            }

            inputFotos.files = dtFotos.files;
            renderPreviewFotos();
            estadoFotos.textContent = 'Las fotos nuevas están listas para enviarse.';
        } catch (error) {
            estadoFotos.textContent = '';
            alert(error.message || 'Ocurrió un error al procesar las fotos.');
        } finally {
            e.target.value = '';
            setBotonProcesando(false);
        }
    });

    function obtenerComponente(components, tipos) {
        for (const component of components) {
            for (const tipo of tipos) {
                if (component.types.includes(tipo)) {
                    return component.long_name;
                }
            }
        }
        return '';
    }

    function autocompletarDireccionDesdeMapa(latLng, geocoder) {
        geocoder.geocode({ location: latLng }, function(results, status) {
            if (status !== 'OK' || !results || !results.length) {
                return;
            }

            const components = results[0].address_components || [];

            const colonia =
                obtenerComponente(components, ['neighborhood']) ||
                obtenerComponente(components, ['sublocality_level_1']) ||
                obtenerComponente(components, ['sublocality']) ||
                obtenerComponente(components, ['locality']);

            const calle = obtenerComponente(components, ['route']);
            const numero = obtenerComponente(components, ['street_number']);
            const referencia = [calle, numero].filter(Boolean).join(' ');

            const coloniaInput = document.getElementById('colonia_barrio');
            const calleInput = document.getElementById('calle_referencias');

            if (colonia) {
                coloniaInput.value = colonia;
            }

            if (referencia) {
                calleInput.value = referencia;
            } else if (results[0].formatted_address) {
                calleInput.value = results[0].formatted_address;
            }
        });
    }

    function initMapFormulario() {
        if (!googleMapsApiKey) return;

        const latInput = document.getElementById('latitud');
        const lngInput = document.getElementById('longitud');
        const geocoder = new google.maps.Geocoder();
        const estadoMapa = document.getElementById('mapa-estado');
        const searchInput = document.getElementById('map-search');
        const usarMiUbicacionBtn = document.getElementById('usar-mi-ubicacion');
        const autocomplete = new google.maps.places.Autocomplete(searchInput);

        const ubicacionInicial = {
            lat: parseFloat(latInput.value),
            lng: parseFloat(lngInput.value)
        };

        const mapa = new google.maps.Map(document.getElementById('mapa-formulario'), {
            zoom: 15,
            center: ubicacionInicial,
            streetViewControl: false,
            mapTypeControl: false,
            fullscreenControl: false
        });

        const marcador = new google.maps.Marker({
            position: ubicacionInicial,
            map: mapa,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        function actualizarEstado(lat, lng) {
            estadoMapa.textContent = `Ubicación seleccionada: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        marcador.addListener('dragend', function() {
            const pos = marcador.getPosition();
            latInput.value = pos.lat();
            lngInput.value = pos.lng();
            actualizarEstado(pos.lat(), pos.lng());
            autocompletarDireccionDesdeMapa(pos, geocoder);
        });

        mapa.addListener('click', function(event) {
            marcador.setPosition(event.latLng);
            latInput.value = event.latLng.lat();
            lngInput.value = event.latLng.lng();
            actualizarEstado(event.latLng.lat(), event.latLng.lng());
            autocompletarDireccionDesdeMapa(event.latLng, geocoder);
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();

            if (!place.geometry || !place.geometry.location) return;

            mapa.setCenter(place.geometry.location);
            mapa.setZoom(16);
            marcador.setPosition(place.geometry.location);

            latInput.value = place.geometry.location.lat();
            lngInput.value = place.geometry.location.lng();
            actualizarEstado(place.geometry.location.lat(), place.geometry.location.lng());
            autocompletarDireccionDesdeMapa(place.geometry.location, geocoder);
        });

        usarMiUbicacionBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                estadoMapa.textContent = 'Tu navegador no permite geolocalización.';
                return;
            }

            estadoMapa.textContent = 'Obteniendo tu ubicación actual...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const nuevaUbicacion = { lat, lng };

                    mapa.setCenter(nuevaUbicacion);
                    mapa.setZoom(16);
                    marcador.setPosition(nuevaUbicacion);

                    latInput.value = lat;
                    lngInput.value = lng;
                    actualizarEstado(lat, lng);
                    autocompletarDireccionDesdeMapa(nuevaUbicacion, geocoder);
                },
                function () {
                    estadoMapa.textContent = 'No se pudo obtener tu ubicación actual.';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                }
            );
        });

        actualizarEstado(ubicacionInicial.lat, ubicacionInicial.lng);

        if (!document.getElementById('colonia_barrio').value && !document.getElementById('calle_referencias').value) {
            autocompletarDireccionDesdeMapa(ubicacionInicial, geocoder);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (razaIdInicial) {
            razaIdReal.value = razaIdInicial;
        }

        if (otraRazaInicial && !razaIdInicial) {
            inputOtraRaza.value = otraRazaInicial;
        }

        cargarRazas(true);

        if (otraRazaInicial && !razaIdInicial) {
            razaSelector.value = '__otra__';
            mostrarOtraRaza();
        }

        const toast = document.getElementById('toast-success');
        const cerrarToast = document.getElementById('cerrar-toast-success');
        const toastBar = document.getElementById('toast-success-bar');

        if (toast) {
            let duracion = 4500;
            let inicio = Date.now();

            const intervalo = setInterval(() => {
                const transcurrido = Date.now() - inicio;
                const restante = Math.max(0, 100 - ((transcurrido / duracion) * 100));
                if (toastBar) toastBar.style.width = restante + '%';

                if (transcurrido >= duracion) {
                    clearInterval(intervalo);
                    toast.classList.add('opacity-0', 'translate-y-2', 'transition', 'duration-300');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 50);

            if (cerrarToast) {
                cerrarToast.addEventListener('click', () => {
                    clearInterval(intervalo);
                    toast.remove();
                });
            }
        }
    });

    formEditar.addEventListener('submit', function(e) {
        if (procesandoImagenes) {
            e.preventDefault();
            alert('Espera a que terminen de procesarse las imágenes.');
            return;
        }

        setBotonProcesando(true, 'Guardando cambios...');
    });
</script>

@if($googleMapsApiKey)
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initMapFormulario"
        async defer>
    </script>
@endif
@endsection