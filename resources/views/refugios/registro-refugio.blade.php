@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <a href="{{ url()->previous() }}" class="inline-flex items-center text-orange-500 font-medium mb-6 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver al menú
        </a>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Registro de refugio</h1>
            <p class="text-gray-500">Únete a nuestra red para dar más visibilidad a los peluditos que buscan hogar.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                <strong class="font-bold">Revisa estos campos:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-100 border border-green-300 text-green-700 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('registro.refugio.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- DATOS DE CUENTA Y CONTACTO --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Datos de la cuenta y contacto</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Nombre del refugio <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nombre_refugio"
                            value="{{ old('nombre_refugio') }}"
                            placeholder="Ej. Refugio San Francisco"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Correo electrónico <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            name="correo"
                            value="{{ old('correo') }}"
                            placeholder="contacto@refugio.com"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Confirmar contraseña <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="Repite tu contraseña"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Teléfono (10 dígitos) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="telefono"
                            value="{{ old('telefono') }}"
                            maxlength="10"
                            placeholder="Ej. 9191234567"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">WhatsApp</label>
                        <input
                            type="text"
                            name="whatsapp"
                            value="{{ old('whatsapp') }}"
                            maxlength="10"
                            placeholder="Ej. 9191234567"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Descripción del refugio <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="descripcion"
                            rows="3"
                            placeholder="Cuéntanos sobre la misión, historia y labor del refugio..."
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- UBICACIÓN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Ubicación</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Calle y número <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="calle_numero"
                            type="text"
                            name="calle_numero"
                            value="{{ old('calle_numero') }}"
                            placeholder="Ej. Avenida Central #123"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Colonia/Barrio <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="colonia"
                                type="text"
                                name="colonia"
                                value="{{ old('colonia') }}"
                                placeholder="Ej. Centro"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Código postal <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="codigo_postal"
                                type="text"
                                name="codigo_postal"
                                value="{{ old('codigo_postal') }}"
                                placeholder="Ej. 29950"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Ciudad <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="ciudad"
                                type="text"
                                name="ciudad"
                                value="{{ old('ciudad', 'Ocosingo') }}"
                                placeholder="Ej. Ocosingo"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="estado_direccion"
                                type="text"
                                name="estado_direccion"
                                value="{{ old('estado_direccion', 'Chiapas') }}"
                                placeholder="Ej. Chiapas"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Buscar ubicación en el mapa</label>
                                <input
                                    id="map-search"
                                    type="text"
                                    placeholder="Busca una dirección o referencia"
                                    class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                                >
                            </div>

                            <button
                                type="button"
                                id="usar-mi-ubicacion"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition"
                            >
                                Usar mi ubicación actual
                            </button>

                            <div class="rounded-xl border border-orange-100 bg-orange-50 p-4 text-sm text-gray-700">
                                <p class="font-semibold text-gray-800 mb-1">Cómo marcar la ubicación</p>
                                <p>Puedes buscar una dirección, arrastrar el marcador o hacer clic directamente en el mapa.</p>
                            </div>

                            <p id="mapa-estado" class="text-xs text-gray-500"></p>
                        </div>

                        <div class="lg:col-span-2">
                            @if(config('services.google_maps.api_key'))
                                <div id="mapa-refugio" class="w-full h-[420px] rounded-2xl border border-gray-200 overflow-hidden shadow-sm"></div>
                            @else
                                <div class="w-full h-[420px] rounded-2xl border-2 border-dashed border-gray-300 bg-gray-100 flex flex-col items-center justify-center text-center px-6">
                                    <div class="bg-orange-100 p-3 rounded-full mb-3">
                                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-gray-800">Falta configurar Google Maps</p>
                                    <p class="text-sm text-gray-500 mt-1">Agrega tu API Key en el archivo .env para activar el mapa.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud', '16.90600000') }}">
                    <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud', '-92.09330000') }}">
                </div>
            </div>

            {{-- DETALLES DEL REFUGIO --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Detalles del refugio</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Capacidad máx. de perros <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="capacidad_perros"
                            value="{{ old('capacidad_perros') }}"
                            min="0"
                            placeholder="Ej. 50"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Capacidad máx. de gatos <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="capacidad_gatos"
                            value="{{ old('capacidad_gatos') }}"
                            min="0"
                            placeholder="Ej. 30"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Descripción de instalaciones</label>
                        <textarea
                            name="instalaciones_descripcion"
                            rows="3"
                            placeholder="Ej. Contamos con áreas separadas para perros y gatos, zona de cuarentena, patio amplio..."
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >{{ old('instalaciones_descripcion') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Requisitos generales de adopción <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="requisitos_adopcion"
                            rows="3"
                            placeholder="Ej. Identificación oficial, comprobante de domicilio reciente, visita previa a la casa, firma de contrato..."
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >{{ old('requisitos_adopcion') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            ¿Aceptan donaciones en especie? <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="acepta_donaciones"
                            name="acepta_donaciones"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                            <option value="1" {{ old('acepta_donaciones', '1') == '1' ? 'selected' : '' }}>Sí, aceptamos donaciones</option>
                            <option value="0" {{ old('acepta_donaciones') == '0' ? 'selected' : '' }}>No por el momento</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">¿Qué tipo de donaciones?</label>
                        <input
                            id="tipo_donaciones"
                            type="text"
                            name="tipo_donaciones"
                            value="{{ old('tipo_donaciones') }}"
                            placeholder="Ej. Croquetas, cobijas, arena..."
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                        >
                    </div>
                </div>
            </div>

            {{-- FOTOGRAFÍAS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Fotografías del refugio</h2>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 bg-gray-100">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="bg-orange-100 p-4 rounded-full mb-4">
                            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>

                        <h3 class="text-gray-900 font-bold mb-1">Sube fotografías del refugio</h3>
                        <p class="text-sm text-gray-500 mb-5">
                            Puedes agregar desde 1 hasta 10 imágenes. Formatos JPG o PNG. Máximo 5MB por archivo.
                        </p>

                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <label for="fotos_picker" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition cursor-pointer">
                                Agregar imágenes
                            </label>

                            <button
                                type="button"
                                id="limpiar-fotos"
                                class="bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-6 rounded-lg border border-gray-300 transition"
                            >
                                Limpiar selección
                            </button>
                        </div>

                        <input id="fotos_picker" type="file" multiple accept=".jpg,.jpeg,.png" class="hidden">
                        <input id="fotos" type="file" name="fotos[]" multiple class="hidden">

                        <p id="contador-fotos" class="mt-4 text-sm text-gray-500">0 de 10 imágenes seleccionadas.</p>
                    </div>
                </div>

                <div id="vista-previa-contenedor" class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4"></div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ url()->previous() }}" class="px-8 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition">
                    Cancelar
                </a>

                <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-lg font-bold shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    Enviar solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const refugioLatInicial = parseFloat(@json(old('latitud', '16.90600000')));
    const refugioLngInicial = parseFloat(@json(old('longitud', '-92.09330000')));

    const latInput = document.getElementById('latitud');
    const lngInput = document.getElementById('longitud');

    const calleInput = document.getElementById('calle_numero');
    const coloniaInput = document.getElementById('colonia');
    const cpInput = document.getElementById('codigo_postal');
    const ciudadInput = document.getElementById('ciudad');
    const estadoInput = document.getElementById('estado_direccion');

    const mapSearchInput = document.getElementById('map-search');
    const usarMiUbicacionBtn = document.getElementById('usar-mi-ubicacion');
    const mapaEstado = document.getElementById('mapa-estado');

    const aceptaDonacionesSelect = document.getElementById('acepta_donaciones');
    const tipoDonacionesInput = document.getElementById('tipo_donaciones');

    let refugioMap = null;
    let refugioMarker = null;
    let refugioAutocomplete = null;
    let refugioGeocoder = null;

    function toggleTipoDonaciones() {
        if (!aceptaDonacionesSelect || !tipoDonacionesInput) return;

        const acepta = aceptaDonacionesSelect.value === '1';

        tipoDonacionesInput.disabled = !acepta;
        tipoDonacionesInput.classList.toggle('bg-gray-200', !acepta);
        tipoDonacionesInput.classList.toggle('cursor-not-allowed', !acepta);

        if (!acepta) {
            tipoDonacionesInput.value = '';
        }
    }

    function setStatusMessage(message, isError = false) {
        if (!mapaEstado) return;
        mapaEstado.textContent = message || '';
        mapaEstado.className = 'text-xs ' + (isError ? 'text-red-500' : 'text-gray-500');
    }

    function updateCoords(lat, lng) {
        latInput.value = Number(lat).toFixed(6);
        lngInput.value = Number(lng).toFixed(6);
    }

    function getComponent(components, type) {
        const comp = components.find(c => c.types.includes(type));
        return comp ? comp.long_name : '';
    }

    function buildStreet(components) {
        const route = getComponent(components, 'route');
        const streetNumber = getComponent(components, 'street_number');

        if (route && streetNumber) return `${route} ${streetNumber}`;
        if (route) return route;
        if (streetNumber) return streetNumber;

        return '';
    }

    function getColonia(components) {
        return (
            getComponent(components, 'neighborhood') ||
            getComponent(components, 'sublocality_level_1') ||
            getComponent(components, 'sublocality') ||
            ''
        );
    }

    function getCiudad(components) {
        return (
            getComponent(components, 'locality') ||
            getComponent(components, 'administrative_area_level_2') ||
            ''
        );
    }

    function fillAddressFields(components) {
        if (!Array.isArray(components)) return;

        const calle = buildStreet(components);
        const colonia = getColonia(components);
        const postalCode = getComponent(components, 'postal_code');
        const ciudad = getCiudad(components);
        const estado = getComponent(components, 'administrative_area_level_1');

        if (calle) calleInput.value = calle;
        if (colonia) coloniaInput.value = colonia;
        if (postalCode) cpInput.value = postalCode;
        if (ciudad) ciudadInput.value = ciudad;
        if (estado) estadoInput.value = estado;
    }

    function reverseGeocode(latLng) {
        if (!refugioGeocoder) return;

        refugioGeocoder.geocode({ location: latLng }, function(results, status) {
            if (status === 'OK' && results && results.length) {
                fillAddressFields(results[0].address_components || []);
                setStatusMessage('Ubicación aplicada y datos de dirección autocompletados.');
            } else {
                setStatusMessage('Se actualizó la ubicación, pero no se pudieron autocompletar todos los campos.', true);
            }
        });
    }

    function moveMarkerAndMap(position, zoom = 16, geocodeAfter = true) {
        if (!refugioMap || !refugioMarker) return;

        refugioMarker.setPosition(position);
        refugioMap.setCenter(position);
        refugioMap.setZoom(zoom);

        updateCoords(position.lat(), position.lng());

        if (geocodeAfter) {
            reverseGeocode(position);
        }
    }

    window.initRefugioMap = function () {
        const mapaEl = document.getElementById('mapa-refugio');
        if (!mapaEl || typeof google === 'undefined' || !google.maps) return;

        const initialPosition = {
            lat: isNaN(refugioLatInicial) ? 16.9060 : refugioLatInicial,
            lng: isNaN(refugioLngInicial) ? -92.0933 : refugioLngInicial
        };

        refugioMap = new google.maps.Map(mapaEl, {
            center: initialPosition,
            zoom: 15,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        refugioMarker = new google.maps.Marker({
            map: refugioMap,
            position: initialPosition,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        refugioGeocoder = new google.maps.Geocoder();

        updateCoords(initialPosition.lat, initialPosition.lng);
        setStatusMessage('Puedes arrastrar el marcador, buscar una dirección o hacer clic en el mapa.');

        refugioMap.addListener('click', function(event) {
            moveMarkerAndMap(event.latLng);
        });

        refugioMarker.addListener('dragend', function(event) {
            updateCoords(event.latLng.lat(), event.latLng.lng());
            reverseGeocode(event.latLng);
        });

        if (mapSearchInput && google.maps.places) {
            refugioAutocomplete = new google.maps.places.Autocomplete(mapSearchInput, {
                fields: ['geometry', 'formatted_address', 'name', 'address_components'],
                componentRestrictions: { country: 'mx' }
            });

            refugioAutocomplete.addListener('place_changed', function() {
                const place = refugioAutocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    setStatusMessage('No se pudo obtener la ubicación seleccionada.', true);
                    return;
                }

                moveMarkerAndMap(place.geometry.location, 17, false);

                if (place.address_components) {
                    fillAddressFields(place.address_components);
                    setStatusMessage('Ubicación encontrada y dirección autocompletada.');
                } else {
                    reverseGeocode(place.geometry.location);
                }
            });
        }

        if (usarMiUbicacionBtn) {
            usarMiUbicacionBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    setStatusMessage('Tu navegador no permite obtener la ubicación actual.', true);
                    return;
                }

                setStatusMessage('Obteniendo tu ubicación...');

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const current = new google.maps.LatLng(
                            position.coords.latitude,
                            position.coords.longitude
                        );

                        moveMarkerAndMap(current, 17);
                    },
                    function() {
                        setStatusMessage('No se pudo obtener tu ubicación actual.', true);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000
                    }
                );
            });
        }

        if (Number.isFinite(initialPosition.lat) && Number.isFinite(initialPosition.lng)) {
            const initialLatLng = new google.maps.LatLng(initialPosition.lat, initialPosition.lng);
            reverseGeocode(initialLatLng);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        toggleTipoDonaciones();

        if (aceptaDonacionesSelect) {
            aceptaDonacionesSelect.addEventListener('change', toggleTipoDonaciones);
        }

        const pickerInput = document.getElementById('fotos_picker');
        const realInput = document.getElementById('fotos');
        const preview = document.getElementById('vista-previa-contenedor');
        const contador = document.getElementById('contador-fotos');
        const limpiarBtn = document.getElementById('limpiar-fotos');

        let selectedFiles = [];

        function isDuplicate(file) {
            return selectedFiles.some(existing =>
                existing.name === file.name &&
                existing.size === file.size &&
                existing.lastModified === file.lastModified
            );
        }

        function syncRealInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            realInput.files = dt.files;
        }

        function renderPreview() {
            preview.innerHTML = '';

            if (selectedFiles.length === 0) {
                contador.textContent = '0 de 10 imágenes seleccionadas.';
                return;
            }

            contador.textContent = `${selectedFiles.length} de 10 imágenes seleccionadas.`;

            selectedFiles.forEach((file, index) => {
                const url = URL.createObjectURL(file);

                const card = document.createElement('div');
                card.className = 'relative bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm';

                card.innerHTML = `
                    <img src="${url}" class="w-full h-32 object-cover" alt="Vista previa">
                    <button
                        type="button"
                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold shadow"
                        data-index="${index}"
                        title="Eliminar imagen"
                    >×</button>
                    <div class="p-2">
                        <p class="text-xs text-gray-600 truncate" title="${file.name}">${file.name}</p>
                    </div>
                `;

                preview.appendChild(card);
            });

            preview.querySelectorAll('button[data-index]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const index = Number(this.dataset.index);
                    selectedFiles.splice(index, 1);
                    syncRealInput();
                    renderPreview();
                });
            });
        }

        if (pickerInput) {
            pickerInput.addEventListener('change', function (event) {
                const files = Array.from(event.target.files || []);

                for (const file of files) {
                    if (!file.type.startsWith('image/')) continue;
                    if (isDuplicate(file)) continue;

                    if (selectedFiles.length >= 10) {
                        alert('Solo puedes subir un máximo de 10 imágenes.');
                        break;
                    }

                    selectedFiles.push(file);
                }

                syncRealInput();
                renderPreview();
                pickerInput.value = '';
            });
        }

        if (limpiarBtn) {
            limpiarBtn.addEventListener('click', function () {
                selectedFiles = [];
                syncRealInput();
                renderPreview();
                pickerInput.value = '';
            });
        }

        renderPreview();
    });
</script>

@if(config('services.google_maps.api_key'))
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initRefugioMap"
        async
        defer
    ></script>
@endif
@endsection