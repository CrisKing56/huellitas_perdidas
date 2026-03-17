@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto">
        
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-orange-500 font-medium mb-6 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver al menú
        </a>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Registro de veterinaria</h1>
            <p class="text-gray-500">Completa el formulario para registrar tu veterinaria en la plataforma.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-100 border border-green-300 text-green-700 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

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

        <form action="{{ route('registro.veterinaria.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Información general</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nombre de la veterinaria <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre_veterinaria" value="{{ old('nombre_veterinaria') }}" placeholder="Ej: Clínica Veterinaria San Francisco"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Descripción <span class="text-red-500">*</span></label>
                        <textarea name="descripcion" rows="4" placeholder="Describe los servicios y especialidades de tu veterinaria..."
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Datos de contacto</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Ej: contacto@veterinaria.com"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Teléfono <span class="text-red-500">*</span></label>
                        <input type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 9191234567"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">WhatsApp</label>
                        <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Ej: 9191234567"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Sitio web</label>
                        <input type="url" name="sitio_web" value="{{ old('sitio_web') }}" placeholder="Ej: www.miveterinaria.com (opcional)"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Confirmar contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Repite tu contraseña"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Dirección y ubicación</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Calle y número <span class="text-red-500">*</span></label>
                        <input type="text" name="calle_numero" value="{{ old('calle_numero') }}" placeholder="Ej: Av. Juárez 123"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Colonia <span class="text-red-500">*</span></label>
                            <input type="text" name="colonia" value="{{ old('colonia') }}" placeholder="Ej: Centro"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Código postal <span class="text-red-500">*</span></label>
                            <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}" placeholder="Ej: 29950"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Ciudad <span class="text-red-500">*</span></label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', 'Ocosingo') }}" placeholder="Ej: Ocosingo"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Estado <span class="text-red-500">*</span></label>
                            <input type="text" name="estado_direccion" value="{{ old('estado_direccion', 'Chiapas') }}" placeholder="Ej: Chiapas"
                                class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                        </div>
                    </div>

                    <div class="bg-gray-200 rounded-xl h-64 flex flex-col items-center justify-center text-gray-500 border-2 border-dashed border-gray-300">
                        <div class="bg-orange-100 p-3 rounded-full mb-2">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="font-medium">Mapa interactivo</span>
                        <span class="text-sm">Haz clic para marcar la ubicación de tu veterinaria</span>
                    </div>

                    <input type="hidden" name="latitud" value="{{ old('latitud', '16.9070') }}">
                    <input type="hidden" name="longitud" value="{{ old('longitud', '-92.0930') }}">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Horarios de atención</h2>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <span class="font-medium text-gray-700">Lunes a Viernes</span>
                        <input type="time" name="horario_lv_apertura" value="{{ old('horario_lv_apertura') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-4 bg-gray-100 text-gray-800">
                        <input type="time" name="horario_lv_cierre" value="{{ old('horario_lv_cierre') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-4 bg-gray-100 text-gray-800">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <span class="font-medium text-gray-700">Sábado</span>
                        <input type="time" name="horario_sab_apertura" value="{{ old('horario_sab_apertura') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-4 bg-gray-100 text-gray-800">
                        <input type="time" name="horario_sab_cierre" value="{{ old('horario_sab_cierre') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-4 bg-gray-100 text-gray-800">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <span class="font-medium text-gray-700">Domingo</span>
                        <input type="time" name="horario_dom_apertura" value="{{ old('horario_dom_apertura') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-4 bg-gray-100 text-gray-800">
                        <input type="time" name="horario_dom_cierre" value="{{ old('horario_dom_cierre') }}"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-4 bg-gray-100 text-gray-800">
                    </div>
                    <p class="text-xs text-gray-400 mt-2"><strong>Nota:</strong> Si cierras algún día, deja los campos de horario vacíos.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Servicios ofrecidos</h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-2 gap-y-4 gap-x-8 mb-6">
                    @foreach(['Consulta general', 'Vacunación', 'Cirugía', 'Esterilización', 'Urgencias 24/7', 'Hospitalización', 'Peluquería', 'Radiografías', 'Laboratorio', 'Dentista', 'Rehabilitación', 'Pensión'] as $servicio)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="servicios[]" value="{{ $servicio }}" class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-3 text-gray-700 text-sm font-medium">{{ $servicio }}</span>
                        </label>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Otros servicios</label>
                    <input type="text" name="otros_servicios" value="{{ old('otros_servicios') }}" placeholder="Especifica otros servicios que ofreces (separados por comas)"
                        class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Responsables y personal</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nombre del médico veterinario responsable <span class="text-red-500">*</span></label>
                        <input type="text" name="medico_responsable" value="{{ old('medico_responsable') }}" placeholder="Ej: Dr. Juan Pérez González"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cédula profesional <span class="text-red-500">*</span></label>
                        <input type="text" name="cedula_profesional" value="{{ old('cedula_profesional') }}" placeholder="Ej: 1234567"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Número de médicos veterinarios</label>
                        <input type="number" name="num_veterinarios" value="{{ old('num_veterinarios') }}" placeholder="Ej: 2"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Costos estimados (opcional)</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Consulta general</label>
                        <input type="text" name="costo_consulta" value="{{ old('costo_consulta') }}" placeholder="Ej: 200"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Vacunación</label>
                        <input type="text" name="costo_vacuna" value="{{ old('costo_vacuna') }}" placeholder="Ej: 150"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Esterilización</label>
                        <input type="text" name="costo_esterilizacion" value="{{ old('costo_esterilizacion') }}" placeholder="Ej: 800"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cirugía general</label>
                        <input type="text" name="costo_cirugia" value="{{ old('costo_cirugia') }}" placeholder="Ej: 1200"
                            class="w-full rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Fotografías</h2>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-gray-100">
                    <div class="bg-orange-100 p-4 rounded-full mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                    </div>

                    <h3 class="text-gray-900 font-bold mb-1">Sube fotografías de tu veterinaria</h3>
                    <p class="text-sm text-gray-500 mb-6">Puedes subir hasta 5 imágenes. Archivos JPG o PNG. Máximo 5MB cada una.</p>

                    <label for="fotos" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition cursor-pointer">
                        Seleccionar archivos
                    </label>

                    <input id="fotos" type="file" name="fotos[]" multiple accept=".jpg,.jpeg,.png" class="hidden">

                    <p id="contador-fotos" class="mt-4 text-sm text-gray-500"></p>
                </div>

                <div id="preview-fotos" class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4"></div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ url()->previous() }}" class="px-8 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-orange-500 text-white font-bold hover:bg-orange-600 shadow-md transition">
                    Enviar solicitud
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputFotos = document.getElementById('fotos');
    const preview = document.getElementById('preview-fotos');
    const contador = document.getElementById('contador-fotos');

    if (!inputFotos) return;

    inputFotos.addEventListener('change', function (event) {
        preview.innerHTML = '';
        contador.textContent = '';

        const archivos = Array.from(event.target.files || []);

        if (archivos.length === 0) {
            return;
        }

        if (archivos.length > 5) {
            contador.textContent = 'Solo puedes seleccionar hasta 5 imágenes.';
            inputFotos.value = '';
            return;
        }

        contador.textContent = archivos.length + ' imagen(es) seleccionada(s).';

        archivos.forEach((archivo) => {
            if (!archivo.type.startsWith('image/')) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                const card = document.createElement('div');
                card.className = 'bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm';

                card.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover" alt="Vista previa">
                    <div class="p-2">
                        <p class="text-xs text-gray-600 truncate" title="${archivo.name}">${archivo.name}</p>
                    </div>
                `;

                preview.appendChild(card);
            };

            reader.readAsDataURL(archivo);
        });
    });
});
</script>
@endsection