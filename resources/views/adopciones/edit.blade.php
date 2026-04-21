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
@endphp

<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        <div class="mb-8">
            <a href="{{ route('adopciones.mis-adopciones') }}"
               class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a mis adopciones
            </a>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center rounded-full bg-green-50 text-green-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-green-100">
                        Editar publicación
                    </span>
                    <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        Editar mascota en adopción
                    </h1>
                    <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                        Actualiza la información con el mayor detalle posible para mantener la publicación clara y útil.
                    </p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl mb-6">
                <strong class="font-bold">Revisa estos campos:</strong>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('adopciones.update', $adopcion->id_publicacion) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-8"
              id="form-editar-adopcion">
            @csrf
            @method('PUT')

            <div id="fotos-eliminar-container"></div>

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
                                <p class="text-sm text-gray-500 mt-1">Datos básicos para identificarla mejor.</p>
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
                                        <option value="{{ $especie->id_especie }}" {{ old('especie_id', $adopcion->especie_id) == $especie->id_especie ? 'selected' : '' }}>
                                            {{ $especie->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Raza
                                </label>
                                <input type="hidden" name="raza_id" id="raza_id_real" value="{{ old('raza_id', $adopcion->raza_id) }}">

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
                                       value="{{ old('otra_raza', $adopcion->otra_raza) }}"
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
                                      placeholder="Ej: Es muy noble, convive con niños, es juguetón, ya está listo para un hogar...">{{ old('descripcion', $adopcion->descripcion) }}</textarea>
                        </div>
                    </section>

                    {{-- SALUD --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Salud y adopción responsable</h2>
                                <p class="text-sm text-gray-500 mt-1">Estos campos quedarán listos visualmente para el módulo.</p>
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
                                    <option value="0" {{ old('esterilizado', isset($adopcion->esterilizado) ? (string)(int)$adopcion->esterilizado : '') === '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('esterilizado', isset($adopcion->esterilizado) ? (string)(int)$adopcion->esterilizado : '') === '1' ? 'selected' : '' }}>Sí</option>
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
                                    Puedes marcar el punto y autocompletar los campos como apoyo visual del formulario.
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
                                    <p>Puedes buscar una dirección, mover el marcador o hacer clic directo en el mapa.</p>
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
                                       placeholder="Ej: Centro, La Cañada, Barrio Norte"
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
                                       placeholder="Ej: Frente al parque, cerca de la farmacia"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>

                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $adopcion->latitud ?? '16.9060') }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $adopcion->longitud ?? '-92.0933') }}">
                    </section>
                </div>

                <div class="xl:col-span-4 space-y-8">

                    {{-- FOTOS --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 xl:sticky xl:top-24">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Fotografías</h2>
                                <p class="text-sm text-gray-500 mt-1">Conserva, elimina o agrega hasta llegar a un máximo de 8.</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 mb-6">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-400">Fotos actuales</p>
                                    <p class="font-bold text-gray-900" id="contador-actuales">{{ $adopcion->fotos->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Nuevas por agregar</p>
                                    <p class="font-bold text-gray-900" id="contador-nuevas">0</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Marcadas para borrar</p>
                                    <p class="font-bold text-red-600" id="contador-eliminar">0</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Total final</p>
                                    <p class="font-bold text-green-700"><span id="contador-total-final">{{ $adopcion->fotos->count() }}</span> / 8</p>
                                </div>
                            </div>
                        </div>

                        @if($adopcion->fotos && $adopcion->fotos->count())
                            <div class="mb-6">
                                <p class="text-sm font-semibold text-gray-800 mb-3">Fotos actuales</p>
                                <div class="grid grid-cols-2 gap-4" id="grid-fotos-actuales">
                                    @foreach($adopcion->fotos->sortBy('orden') as $foto)
                                        <div class="relative rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm foto-actual-card" data-foto-id="{{ $foto->id_foto }}">
                                            <img src="{{ asset('storage/' . $foto->url) }}"
                                                 alt="Foto actual"
                                                 class="w-full h-36 object-cover">

                                            <div class="absolute inset-0 bg-red-600/70 text-white text-sm font-bold items-center justify-center hidden foto-overlay-eliminar">
                                                Se eliminará
                                            </div>

                                            <div class="p-3">
                                                <button type="button"
                                                        class="w-full text-sm font-semibold bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl py-2 px-3 btn-toggle-eliminar-foto"
                                                        data-foto-id="{{ $foto->id_foto }}">
                                                    Marcar para eliminar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 rounded-3xl bg-gray-50 p-6 text-center hover:bg-gray-100 transition">
                            <div class="bg-green-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                            </div>

                            <p class="text-gray-900 font-semibold mb-1">Agregar nuevas fotografías</p>
                            <p class="text-xs text-gray-500 mb-5">
                                Formato JPG, JPEG, PNG o WEBP. Máximo final 7 MB por imagen. Si una foto pesa más, se optimizará automáticamente antes de enviarse.
                            </p>

                            <label class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-2xl shadow cursor-pointer transition">
                                Seleccionar fotos
                                <input type="file" name="fotos[]" id="fotos" class="hidden" accept="image/jpeg,image/png,image/webp" multiple>
                            </label>

                            <p id="nombre-archivo" class="mt-4 text-sm text-gray-500">Ningún archivo seleccionado</p>
                            <p id="estado-fotos" class="mt-2 text-xs text-gray-500"></p>

                            <div id="preview-wrapper" class="mt-6 hidden">
                                <div id="preview-grid" class="grid grid-cols-2 gap-4"></div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 pt-2">
                <button type="submit"
                        id="btn-guardar-cambios"
                        class="w-full md:w-auto inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-8 rounded-2xl shadow transition">
                    Guardar cambios
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
    const razaIdInicial = @json(old('raza_id', $adopcion->raza_id));
    const otraRazaInicial = @json(old('otra_raza', $adopcion->otra_raza ?? ''));
    const googleMapsApiKey = @json($googleMapsApiKey);

    const especieSelect = document.getElementById('especie_id');
    const razaSelector = document.getElementById('raza_selector');
    const razaIdReal = document.getElementById('raza_id_real');
    const bloqueOtraRaza = document.getElementById('bloque_otra_raza');
    const inputOtraRaza = document.getElementById('otra_raza');

    function toggleOtraRaza(show) {
        if (show) {
            bloqueOtraRaza.classList.remove('hidden');
            inputOtraRaza.disabled = false;
        } else {
            bloqueOtraRaza.classList.add('hidden');
            inputOtraRaza.disabled = true;
            inputOtraRaza.value = '';
        }
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
            bloqueOtraRaza.classList.remove('hidden');
            inputOtraRaza.disabled = false;
        } else {
            razaIdReal.value = razaSelector.value || '';
            if (razaSelector.value) {
                toggleOtraRaza(false);
            } else if (inputOtraRaza.value.trim() !== '') {
                toggleOtraRaza(true);
            } else {
                toggleOtraRaza(false);
            }
        }
    }

    especieSelect.addEventListener('change', function () {
        razaIdReal.value = '';
        toggleOtraRaza(false);
        cargarRazas(false);
    });

    razaSelector.addEventListener('change', manejarCambioRaza);

    const inputFotos = document.getElementById('fotos');
    const previewWrapper = document.getElementById('preview-wrapper');
    const previewGrid = document.getElementById('preview-grid');
    const nombreArchivo = document.getElementById('nombre-archivo');
    const estadoFotos = document.getElementById('estado-fotos');
    const formEditar = document.getElementById('form-editar-adopcion');
    const btnGuardar = document.getElementById('btn-guardar-cambios');
    const fotosEliminarContainer = document.getElementById('fotos-eliminar-container');

    const contadorActuales = document.getElementById('contador-actuales');
    const contadorNuevas = document.getElementById('contador-nuevas');
    const contadorEliminar = document.getElementById('contador-eliminar');
    const contadorTotalFinal = document.getElementById('contador-total-final');

    const LIMITE_FINAL_BYTES = 7 * 1024 * 1024;
    const LIMITE_SELECCION_BYTES = 25 * 1024 * 1024;
    const MAX_FOTOS = 8;
    const DIMENSION_MAXIMA = 1600;
    const TIPOS_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];

    let dtFotos = new DataTransfer();
    let procesandoFotos = false;
    let fotosEliminarSet = new Set();
    const textoOriginalBoton = btnGuardar.textContent.trim();

    function formatearBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function cambiarExtension(nombre, nuevaExtension) {
        const base = nombre.replace(/\.[^/.]+$/, '');
        return `${base}.${nuevaExtension}`;
    }

    function setEstadoFotos(texto = '', tipo = 'normal') {
        estadoFotos.textContent = texto;
        estadoFotos.className = 'mt-2 text-xs';

        if (tipo === 'error') {
            estadoFotos.classList.add('text-red-600');
        } else if (tipo === 'ok') {
            estadoFotos.classList.add('text-green-600');
        } else {
            estadoFotos.classList.add('text-gray-500');
        }
    }

    function bloquearEnvio(bloquear, texto = 'Procesando...') {
        procesandoFotos = bloquear;
        btnGuardar.disabled = bloquear;
        btnGuardar.classList.toggle('opacity-70', bloquear);
        btnGuardar.classList.toggle('cursor-not-allowed', bloquear);
        btnGuardar.textContent = bloquear ? texto : textoOriginalBoton;
    }

    function canvasToBlob(canvas, tipo, calidad) {
        return new Promise((resolve) => {
            canvas.toBlob((blob) => resolve(blob), tipo, calidad);
        });
    }

    function cargarImagenDesdeArchivo(file) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            const objectUrl = URL.createObjectURL(file);

            image.onload = () => {
                URL.revokeObjectURL(objectUrl);
                resolve(image);
            };

            image.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(new Error(`No se pudo leer la imagen ${file.name}.`));
            };

            image.src = objectUrl;
        });
    }

    function calcularDimensiones(width, height, maxDimension) {
        if (width <= maxDimension && height <= maxDimension) {
            return { width, height };
        }

        const ratio = Math.min(maxDimension / width, maxDimension / height);

        return {
            width: Math.round(width * ratio),
            height: Math.round(height * ratio),
        };
    }

    async function optimizarArchivo(file) {
        if (file.size <= LIMITE_FINAL_BYTES) {
            return {
                file,
                optimizado: false,
                pesoOriginal: file.size,
                pesoFinal: file.size,
            };
        }

        const image = await cargarImagenDesdeArchivo(file);
        let { width, height } = calcularDimensiones(image.width, image.height, DIMENSION_MAXIMA);

        const qualitySteps = [0.86, 0.80, 0.74, 0.68, 0.62, 0.56];
        let mejorBlob = null;

        for (let intento = 0; intento < 5; intento++) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = width;
            canvas.height = height;

            ctx.drawImage(image, 0, 0, width, height);

            for (const quality of qualitySteps) {
                const blob = await canvasToBlob(canvas, 'image/webp', quality);

                if (!blob) continue;

                mejorBlob = blob;

                if (blob.size <= LIMITE_FINAL_BYTES) {
                    const nuevoArchivo = new File(
                        [blob],
                        cambiarExtension(file.name, 'webp'),
                        {
                            type: 'image/webp',
                            lastModified: Date.now(),
                        }
                    );

                    return {
                        file: nuevoArchivo,
                        optimizado: true,
                        pesoOriginal: file.size,
                        pesoFinal: blob.size,
                    };
                }
            }

            width = Math.max(Math.round(width * 0.85), 800);
            height = Math.max(Math.round(height * 0.85), 800);
        }

        if (mejorBlob && mejorBlob.size < file.size) {
            const nuevoArchivo = new File(
                [mejorBlob],
                cambiarExtension(file.name, 'webp'),
                {
                    type: 'image/webp',
                    lastModified: Date.now(),
                }
            );

            return {
                file: nuevoArchivo,
                optimizado: true,
                pesoOriginal: file.size,
                pesoFinal: mejorBlob.size,
            };
        }

        throw new Error(`No se pudo optimizar ${file.name} a un tamaño aceptable.`);
    }

    function contarFotosActualesActivas() {
        return document.querySelectorAll('.foto-actual-card').length - fotosEliminarSet.size;
    }

    function obtenerTotalFinalFotos() {
        return contarFotosActualesActivas() + dtFotos.files.length;
    }

    function syncFotosEliminarInputs() {
        fotosEliminarContainer.innerHTML = '';
        fotosEliminarSet.forEach((idFoto) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'fotos_eliminar[]';
            input.value = idFoto;
            fotosEliminarContainer.appendChild(input);
        });
    }

    function actualizarResumenFotos() {
        contadorActuales.textContent = contarFotosActualesActivas();
        contadorNuevas.textContent = dtFotos.files.length;
        contadorEliminar.textContent = fotosEliminarSet.size;
        contadorTotalFinal.textContent = obtenerTotalFinalFotos();

        nombreArchivo.textContent = dtFotos.files.length
            ? `${dtFotos.files.length} foto(s) nueva(s) lista(s)`
            : 'Ningún archivo seleccionado';
    }

    function renderPreviewFotos() {
        previewGrid.innerHTML = '';

        const files = Array.from(dtFotos.files);

        if (!files.length) {
            previewWrapper.classList.add('hidden');
            actualizarResumenFotos();
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
                    <div class="p-2 text-xs text-gray-600">
                        <div class="truncate font-medium">${file.name}</div>
                        <div class="text-gray-400 mt-1">${formatearBytes(file.size)}</div>
                    </div>
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

                    if (dtFotos.files.length === 0) {
                        setEstadoFotos('');
                    }
                });
            };

            reader.readAsDataURL(file);
        });

        actualizarResumenFotos();
    }

    async function procesarNuevosArchivos(nuevosArchivos) {
        const errores = [];
        let optimizadas = 0;

        bloquearEnvio(true, 'Optimizando fotos...');
        setEstadoFotos('Preparando imágenes para subir...', 'normal');

        for (let i = 0; i < nuevosArchivos.length; i++) {
            const file = nuevosArchivos[i];

            setEstadoFotos(`Procesando imagen ${i + 1} de ${nuevosArchivos.length}: ${file.name}`, 'normal');

            if (!TIPOS_PERMITIDOS.includes(file.type)) {
                errores.push(`${file.name}: formato no permitido. Usa JPG, JPEG, PNG o WEBP.`);
                continue;
            }

            if (file.size > LIMITE_SELECCION_BYTES) {
                errores.push(`${file.name}: pesa ${formatearBytes(file.size)} y supera el máximo permitido para procesarla.`);
                continue;
            }

            try {
                const resultado = await optimizarArchivo(file);
                dtFotos.items.add(resultado.file);

                if (resultado.optimizado) {
                    optimizadas++;
                }
            } catch (error) {
                errores.push(error.message || `No se pudo procesar ${file.name}.`);
            }
        }

        inputFotos.files = dtFotos.files;
        renderPreviewFotos();

        if (errores.length) {
            setEstadoFotos('Algunas imágenes no pudieron procesarse. Revisa el mensaje mostrado.', 'error');
            alert(errores.join('\n'));
        } else if (optimizadas > 0) {
            setEstadoFotos(`${optimizadas} imagen(es) fueron optimizadas automáticamente antes de enviarse.`, 'ok');
        } else {
            setEstadoFotos('Las imágenes están listas para enviarse.', 'ok');
        }

        bloquearEnvio(false);
    }

    function toggleEliminarFoto(idFoto) {
        const idString = String(idFoto);
        const card = document.querySelector(`.foto-actual-card[data-foto-id="${idString}"]`);
        if (!card) return;

        const overlay = card.querySelector('.foto-overlay-eliminar');
        const boton = card.querySelector('.btn-toggle-eliminar-foto');

        if (fotosEliminarSet.has(idString)) {
            fotosEliminarSet.delete(idString);
            card.classList.remove('ring-2', 'ring-red-300', 'opacity-70');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            boton.textContent = 'Marcar para eliminar';
            boton.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-300');
            boton.classList.add('bg-red-50', 'text-red-600', 'border-red-200');
        } else {
            fotosEliminarSet.add(idString);
            card.classList.add('ring-2', 'ring-red-300', 'opacity-70');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            boton.textContent = 'No eliminar';
            boton.classList.remove('bg-red-50', 'text-red-600', 'border-red-200');
            boton.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300');
        }

        syncFotosEliminarInputs();
        actualizarResumenFotos();
    }

    document.querySelectorAll('.btn-toggle-eliminar-foto').forEach((btn) => {
        btn.addEventListener('click', function () {
            toggleEliminarFoto(this.dataset.fotoId);
        });
    });

    inputFotos.addEventListener('click', function () {
        this.value = null;
    });

    inputFotos.addEventListener('change', async function(e) {
        const nuevos = Array.from(e.target.files);

        if (!nuevos.length) {
            return;
        }

        if ((obtenerTotalFinalFotos() + nuevos.length) > MAX_FOTOS) {
            alert(`No puedes tener más de ${MAX_FOTOS} fotografías en total.`);
            return;
        }

        await procesarNuevosArchivos(nuevos);
    });

    formEditar.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (procesandoFotos) {
            alert('Espera a que termine la optimización de imágenes antes de guardar.');
            return;
        }

        syncFotosEliminarInputs();

        const totalFinal = obtenerTotalFinalFotos();

        if (totalFinal < 1) {
            alert('La publicación debe conservar al menos una fotografía.');
            return;
        }

        if (totalFinal > MAX_FOTOS) {
            alert(`No puedes tener más de ${MAX_FOTOS} fotografías en total.`);
            return;
        }

        bloquearEnvio(true, 'Guardando cambios...');

        try {
            const formData = new FormData(formEditar);

            formData.delete('fotos[]');
            formData.delete('fotos');
            formData.delete('foto');
            formData.delete('fotos_eliminar[]');

            fotosEliminarSet.forEach((idFoto) => {
                formData.append('fotos_eliminar[]', idFoto);
            });

            Array.from(dtFotos.files).forEach((file, index) => {
                formData.append(`fotos[${index}]`, file, file.name);
            });

            const response = await fetch(formEditar.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    const mensajes = [];
                    Object.values(data.errors).forEach((grupo) => {
                        grupo.forEach((mensaje) => mensajes.push(mensaje));
                    });
                    alert(mensajes.join('\n'));
                } else {
                    alert(data.message || 'No se pudo actualizar la publicación.');
                }

                bloquearEnvio(false);
                return;
            }

            window.location.href = data.redirect || '{{ route('adopciones.mis-adopciones') }}';
        } catch (error) {
            console.error(error);
            alert('Ocurrió un error inesperado al guardar los cambios.');
            bloquearEnvio(false);
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
            manejarCambioRaza();
        }

        actualizarResumenFotos();
    });
</script>

@if($googleMapsApiKey)
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initMapFormulario"
        async defer>
    </script>
@endif
@endsection