@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto">
        
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-orange-500 font-medium mb-6 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Volver al menú
        </a>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Registro de veterinaria</h1>
            <p class="text-gray-500">Completa el formulario para registrar tu veterinaria en la plataforma.</p>
        </div>

        <form action="{{ route('veterinarias.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Información general</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre de la veterinaria <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" placeholder="Ej: Clínica Veterinaria San Francisco" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Descripción <span class="text-red-500">*</span></label>
                        <textarea name="descripcion" rows="4" placeholder="Describe los servicios y especialidades de tu veterinaria..." class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Datos de contacto</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="email" placeholder="Ej: contacto@veterinaria.com" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono <span class="text-red-500">*</span></label>
                        <input type="tel" name="telefono" placeholder="Ej: +52 123 456 789" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">WhatsApp</label>
                        <input type="tel" name="whatsapp" placeholder="Ej: +52 123 456 789 (opcional)" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Sitio web</label>
                        <input type="url" name="sitio_web" placeholder="Ej: www.miveterinaria.com (opcional)" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Dirección y ubicación</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Calle y número <span class="text-red-500">*</span></label>
                        <input type="text" name="calle" placeholder="Ej: Av. Juárez 123" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Colonia <span class="text-red-500">*</span></label>
                            <input type="text" name="colonia" placeholder="Ej: Centro" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Código postal <span class="text-red-500">*</span></label>
                            <input type="text" name="cp" placeholder="Ej: 29950" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Ciudad <span class="text-red-500">*</span></label>
                            <input type="text" name="ciudad" placeholder="Ej: Ocosingo" value="Ocosingo" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado <span class="text-red-500">*</span></label>
                            <input type="text" name="estado" placeholder="Ej: Chiapas" value="Chiapas" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                        </div>
                    </div>

                    <div class="bg-gray-200 rounded-xl h-64 flex flex-col items-center justify-center text-gray-500 border-2 border-dashed border-gray-300">
                        <div class="bg-orange-100 p-3 rounded-full mb-2">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="font-medium">Mapa interactivo</span>
                        <span class="text-sm">Haz clic para marcar la ubicación de tu veterinaria</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Horarios de atención</h2>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <span class="font-medium text-gray-700">Lunes a Viernes</span>
                        <input type="time" name="horario_lv_apertura" class="w-full rounded-lg border-gray-200 py-2.5 px-4 bg-gray-50/30">
                        <input type="time" name="horario_lv_cierre" class="w-full rounded-lg border-gray-200 py-2.5 px-4 bg-gray-50/30">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <span class="font-medium text-gray-700">Sábado</span>
                        <input type="time" name="horario_sab_apertura" class="w-full rounded-lg border-gray-200 py-2.5 px-4 bg-gray-50/30">
                        <input type="time" name="horario_sab_cierre" class="w-full rounded-lg border-gray-200 py-2.5 px-4 bg-gray-50/30">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <span class="font-medium text-gray-700">Domingo</span>
                        <input type="time" name="horario_dom_apertura" class="w-full rounded-lg border-gray-200 py-2.5 px-4 bg-gray-50/30">
                        <input type="time" name="horario_dom_cierre" class="w-full rounded-lg border-gray-200 py-2.5 px-4 bg-gray-50/30">
                    </div>
                    <p class="text-xs text-gray-400 mt-2"><strong>Nota:</strong> Si cierras algún día, deja los campos de horario vacíos.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
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
                    <label class="block text-sm font-medium text-gray-500 mb-1">Otros servicios</label>
                    <input type="text" name="otros_servicios" placeholder="Especifica otros servicios que ofreces (separados por comas)" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Responsables y personal</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre del médico veterinario responsable <span class="text-red-500">*</span></label>
                        <input type="text" name="medico_responsable" placeholder="Ej: Dr. Juan Pérez González" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cédula profesional <span class="text-red-500">*</span></label>
                        <input type="text" name="cedula" placeholder="Ej: 1234567" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Número de médicos veterinarios</label>
                        <input type="number" name="num_veterinarios" placeholder="Ej: 2" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Costos estimados (opcional)</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Consulta general</label>
                        <input type="text" name="costo_consulta" placeholder="Ej: $200 - $300" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Vacunación</label>
                        <input type="text" name="costo_vacuna" placeholder="Ej: $150 - $250" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Esterilización</label>
                        <input type="text" name="costo_esterilizacion" placeholder="Ej: $800 - $1,500" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cirugía general</label>
                        <input type="text" name="costo_cirugia" placeholder="Ej: $1,000 - $3,000" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/30">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Fotografías</h2>
                </div>

                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-gray-50/30 hover:bg-gray-50 transition cursor-pointer">
                    <div class="bg-orange-100 p-4 rounded-full mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <h3 class="text-gray-900 font-bold mb-1">Sube fotografías de tu veterinaria</h3>
                    <p class="text-sm text-gray-500 mb-6">Puedes subir hasta 5 imágenes. Archivos JPG o PNG. Máximo 5MB cada una.</p>
                    <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition">
                        Seleccionar archivos
                    </button>
                    <input type="file" name="fotos[]" multiple class="hidden">
                </div>
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
@endsection