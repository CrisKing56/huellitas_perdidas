@extends('layout.app')

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

    $latInicial = old('latitud', optional($publicacion->ubicacion)->latitud ?? '16.9060');
    $lngInicial = old('longitud', optional($publicacion->ubicacion)->longitud ?? '-92.0933');
    $googleMapsApiKey = config('services.google_maps.api_key') ?: env('GOOGLE_MAPS_API_KEY');
@endphp

<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        <div class="mb-8">
            <a href="{{ route('extravios.index') }}"
               class="inline-flex items-center gap-2 text-orange-500 hover:text-orange-600 font-medium transition mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al menú
            </a>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center rounded-full bg-orange-50 text-orange-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-orange-100">
                        Editar reporte
                    </span>
                    <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        Editar publicación
                    </h1>
                    <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                        Actualiza la información del reporte sin perder fotografías, ubicación ni estructura.
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

        <form action="{{ route('extravios.update', $publicacion->id_publicacion) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                <div class="xl:col-span-8 space-y-8">

                    {{-- INFORMACIÓN DE LA MASCOTA --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Información de la mascota</h2>
                                <p class="text-sm text-gray-500 mt-1">Edita solo lo necesario para mantener el reporte actualizado.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la mascota <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="nombre"
                                       value="{{ old('nombre', $publicacion->nombre) }}"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Especie <span class="text-red-500">*</span></label>
                                <select name="especie_id"
                                        id="especie_id"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach($especies as $especie)
                                        <option value="{{ $especie->id_especie }}" {{ old('especie_id', $publicacion->especie_id) == $especie->id_especie ? 'selected' : '' }}>
                                            {{ $especie->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Raza <span class="text-red-500">*</span></label>
                                <input type="hidden" name="raza_id" id="raza_id_real" value="{{ old('raza_id', $publicacion->raza_id ?? '') }}">

                                <select id="raza_selector"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Seleccionar...</option>
                                </select>

                                <p class="text-xs text-gray-400 mt-2">Si no aparece en la lista, selecciona “Otra raza”.</p>
                            </div>

                            <div id="bloque_otra_raza" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Otra raza <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="otra_raza"
                                       id="otra_raza"
                                       value="{{ old('otra_raza', $publicacion->otra_raza ?? '') }}"
                                       placeholder="Ej: Criollo, mestizo, mezcla..."
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Color <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="color"
                                       value="{{ old('color', $publicacion->color) }}"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de extravío <span class="text-red-500">*</span></label>
                                <input type="date"
                                       name="fecha_extravio"
                                       value="{{ old('fecha_extravio', $publicacion->fecha_extravio) }}"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 text-gray-600 focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sexo <span class="text-red-500">*</span></label>
                                <select name="sexo"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                                    <option value="MACHO" {{ old('sexo', $publicacion->sexo) == 'MACHO' ? 'selected' : '' }}>Macho</option>
                                    <option value="HEMBRA" {{ old('sexo', $publicacion->sexo) == 'HEMBRA' ? 'selected' : '' }}>Hembra</option>
                                    <option value="DESCONOCIDO" {{ old('sexo', $publicacion->sexo) == 'DESCONOCIDO' ? 'selected' : '' }}>Desconocido</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tamaño <span class="text-red-500">*</span></label>
                                <select name="tamano"
                                        class="w-full rounded-2xl border border-gray-300 bg-white py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                                    <option value="PEQUEÑO" {{ old('tamano', $publicacion->tamano) == 'PEQUEÑO' ? 'selected' : '' }}>Pequeño</option>
                                    <option value="MEDIANO" {{ old('tamano', $publicacion->tamano) == 'MEDIANO' ? 'selected' : '' }}>Mediano</option>
                                    <option value="GRANDE" {{ old('tamano', $publicacion->tamano) == 'GRANDE' ? 'selected' : '' }}>Grande</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- UBICACIÓN --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Ubicación del extravío</h2>
                                <p class="text-sm text-gray-500 mt-1">Mueve el punto, busca una dirección o usa tu ubicación actual.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                            <div class="lg:col-span-1 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar ubicación</label>
                                    <input id="map-search"
                                           type="text"
                                           placeholder="Busca una calle, colonia o referencia"
                                           class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                                </div>

                                <button type="button"
                                        id="usar-mi-ubicacion"
                                        class="w-full inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-2xl shadow-sm transition">
                                    Usar mi ubicación actual
                                </button>

                                <div class="rounded-2xl border border-orange-100 bg-orange-50 p-4 text-sm text-gray-700">
                                    <p class="font-semibold text-gray-800 mb-1">Sugerencia</p>
                                    <p>Al mover el pin se intentarán actualizar colonia y referencias automáticamente.</p>
                                </div>

                                <p id="mapa-estado" class="text-xs text-gray-500"></p>
                            </div>

                            <div class="lg:col-span-2">
                                @if($googleMapsApiKey)
                                    <div id="mapa-formulario" class="w-full h-[380px] rounded-3xl border border-gray-200 overflow-hidden shadow-sm"></div>
                                @else
                                    <div class="w-full h-[380px] rounded-3xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center text-center px-6">
                                        <div class="bg-orange-100 p-3 rounded-full mb-3">
                                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Colonia o barrio <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="colonia_barrio"
                                       id="colonia_barrio"
                                       value="{{ old('colonia_barrio', $publicacion->colonia_barrio) }}"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Calle y referencias</label>
                                <input type="text"
                                       name="calle_referencias"
                                       id="calle_referencias"
                                       value="{{ old('calle_referencias', $publicacion->calle_referencias) }}"
                                       class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 px-4 focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 mt-3">Puedes corregir manualmente los datos detectados por el mapa.</p>

                        <input type="hidden" name="latitud" id="latitud" value="{{ $latInicial }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ $lngInicial }}">
                    </section>

                    {{-- DESCRIPCIÓN --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Descripción adicional</h2>
                                <p class="text-sm text-gray-500 mt-1">Actualiza señas particulares o información relevante.</p>
                            </div>
                        </div>

                        <textarea name="descripcion"
                                  rows="6"
                                  class="w-full rounded-2xl border border-gray-300 bg-white py-4 px-4 focus:border-orange-500 focus:ring-orange-500 placeholder-gray-400"
                                  placeholder="Describe características importantes o cualquier cambio relevante...">{{ old('descripcion', $publicacion->descripcion) }}</textarea>
                    </section>
                </div>

                <div class="xl:col-span-4 space-y-8">

                    {{-- FOTOGRAFÍAS --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 xl:sticky xl:top-24">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Fotografías</h2>
                                <p class="text-sm text-gray-500 mt-1">Puedes conservar, eliminar o agregar fotos. Máximo total: 8.</p>
                            </div>
                        </div>

                        @if($publicacion->fotos && $publicacion->fotos->count())
                            <div class="mb-8">
                                <p class="text-sm font-semibold text-gray-700 mb-3">Fotos actuales</p>
                                <div class="grid grid-cols-2 gap-4" id="galeria-existente">
                                    @foreach($publicacion->fotos as $foto)
                                        <div class="relative rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm foto-existente-item" data-foto-id="{{ $foto->id_foto }}">
                                            <img src="{{ asset('storage/' . $foto->url) }}" class="w-full h-36 object-cover" alt="Foto existente">
                                            <div class="p-2 text-xs text-gray-600">Foto {{ $loop->iteration }}</div>

                                            <input type="checkbox" name="delete_fotos[]" value="{{ $foto->id_foto }}" class="hidden delete-foto-checkbox">

                                            <button type="button"
                                                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow btn-eliminar-foto">
                                                ×
                                            </button>

                                            <div class="hidden absolute inset-0 bg-black/55 overlay-eliminada flex items-center justify-center text-white font-bold text-sm">
                                                Se eliminará
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 rounded-3xl bg-gray-50 p-6 text-center hover:bg-gray-100 transition">
                            <label class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-2xl shadow cursor-pointer transition">
                                Seleccionar nuevas fotos
                                <input type="file" name="fotos[]" id="fotos" class="hidden" accept="image/*" multiple>
                            </label>

                            <p id="nombre-archivo" class="mt-4 text-sm text-gray-500">Ningún archivo seleccionado</p>

                            <div id="preview-wrapper" class="mt-6 hidden">
                                <div id="preview-grid" class="grid grid-cols-2 gap-4"></div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Datos de contacto</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-400">Nombre del contacto</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->nombre ?? 'Usuario' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Teléfono registrado</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->telefono ?? 'No registrado' }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 pt-2">
                <button type="submit"
                        class="w-full md:w-auto inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-8 rounded-2xl shadow transition">
                    Guardar cambios
                </button>

                <a href="{{ route('extravios.index') }}"
                   class="w-full md:w-auto inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-8 rounded-2xl transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const razasPorEspecie = @json($razasPorEspecie);
    const razaIdInicial = @json(old('raza_id', $publicacion->raza_id ?? ''));
    const otraRazaInicial = @json(old('otra_raza', $publicacion->otra_raza ?? ''));
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

    document.querySelectorAll('.btn-eliminar-foto').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.foto-existente-item');
            const checkbox = item.querySelector('.delete-foto-checkbox');
            const overlay = item.querySelector('.overlay-eliminada');

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                overlay.classList.remove('hidden');
                this.classList.remove('bg-red-500', 'hover:bg-red-600');
                this.classList.add('bg-blue-500', 'hover:bg-blue-600');
                this.textContent = '↺';
            } else {
                overlay.classList.add('hidden');
                this.classList.add('bg-red-500', 'hover:bg-red-600');
                this.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                this.textContent = '×';
            }
        });
    });

    const inputFotos = document.getElementById('fotos');
    const previewWrapper = document.getElementById('preview-wrapper');
    const previewGrid = document.getElementById('preview-grid');
    const nombreArchivo = document.getElementById('nombre-archivo');
    let dtFotos = new DataTransfer();

    function contarFotosExistentesActivas() {
        let total = 0;
        document.querySelectorAll('.foto-existente-item').forEach(item => {
            const checkbox = item.querySelector('.delete-foto-checkbox');
            if (!checkbox.checked) {
                total++;
            }
        });
        return total;
    }

    function renderPreviewFotos() {
        previewGrid.innerHTML = '';

        const files = Array.from(dtFotos.files);
        nombreArchivo.textContent = files.length
            ? `${files.length} foto(s) nueva(s) seleccionada(s)`
            : 'Ningún archivo seleccionado';

        if (!files.length) {
            previewWrapper.classList.add('hidden');
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

    inputFotos.addEventListener('change', function(e) {
        const nuevos = Array.from(e.target.files);
        const totalActual = contarFotosExistentesActivas() + dtFotos.files.length;

        if ((totalActual + nuevos.length) > 8) {
            alert('Solo puedes tener hasta 8 fotografías en total.');
            e.target.value = '';
            return;
        }

        nuevos.forEach(file => dtFotos.items.add(file));
        inputFotos.files = dtFotos.files;
        renderPreviewFotos();
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
            draggable: true
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
    });
</script>

@if($googleMapsApiKey)
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initMapFormulario"
        async defer>
    </script>
@endif
@endsection