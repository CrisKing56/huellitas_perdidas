@extends('layout.app')

@section('title', 'Publicar Mascota en Adopción')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="container mx-auto px-4 lg:px-20">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Publicar mascota en adopción</h1>
            <p class="text-gray-500">Comparte los datos de la mascota para encontrarle un hogar responsable.</p>
        </div>

        <form action="#" method="POST" enctype="multipart/form-data" class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @csrf 

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Información de la mascota
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la mascota <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" placeholder="Ej: Luna" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especie <span class="text-red-500">*</span></label>
                        <select name="especie" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="perro">Perro</option>
                            <option value="gato">Gato</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raza <span class="text-red-500">*</span></label>
                        <input type="text" name="raza" placeholder="Ej: Mestizo, Labrador" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Edad <span class="text-red-500">*</span></label>
                        <input type="text" name="edad" placeholder="Ej: 2 años, 6 meses" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                        <select name="sexo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="macho">Macho</option>
                            <option value="hembra">Hembra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño <span class="text-red-500">*</span></label>
                        <select name="tamano" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="pequeno">Pequeño</option>
                            <option value="mediano">Mediano</option>
                            <option value="grande">Grande</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color <span class="text-red-500">*</span></label>
                    <input type="text" name="color" placeholder="Ej: Marrón, blanco y negro" class="w-full md:w-1/2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Estado de salud y vacunas
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vacunas aplicadas</label>
                        <input type="text" name="vacunas" placeholder="Ej: Parvovirus, Rabia" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Esterilizado/Castrado <span class="text-red-500">*</span></label>
                        <select name="esterilizado" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                            <option value="no_se">No sé</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                     <label class="block text-sm font-medium text-gray-700 mb-1">Condición de salud</label>
                     <input type="text" name="condicion_salud" class="w-full md:w-1/2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción general de salud (opcional)</label>
                    <textarea name="salud_descripcion" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-3 px-4 bg-white border placeholder-gray-400" placeholder="Describe el estado de salud general de la mascota, tratamientos en curso, alergias, etc."></textarea>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </span>
                    Fotografía
                </h2>

                <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-10 text-center hover:bg-gray-100 transition cursor-pointer">
                    <div class="bg-orange-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                         <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">Sube al menos una fotografía de la mascota</p>
                    <p class="text-xs text-gray-500 mb-4">Arrastra y suelta o haz clic para seleccionar</p>
                    <button type="button" class="bg-primary hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-lg shadow transition text-sm">
                        Seleccionar archivo
                    </button>
                    <input type="file" name="imagen" class="hidden">
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    Requisitos para adoptantes
                </h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Requisitos del adoptante <span class="text-red-500">*</span></label>
                    <textarea name="requisitos" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-3 px-4 bg-white border placeholder-gray-400" placeholder="Ejemplo: Ser mayor de edad, tener espacio adecuado, seguimiento por 3 meses, compromiso de cuidado..."></textarea>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    Ubicación del refugio/dueño
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Colonia o barrio <span class="text-red-500">*</span></label>
                        <input type="text" name="colonia" placeholder="Ej: Centro, Retiro" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Calle y referencias (opcional)</label>
                        <input type="text" name="calle" placeholder="Ej: Cerca del parque principal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                </div>

                <div class="w-full h-64 bg-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-500 border border-gray-300 relative overflow-hidden mb-2">
                    <div class="absolute inset-0 opacity-10 bg-[url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg')] bg-cover bg-center"></div>
                    <svg class="w-12 h-12 text-primary mb-2 z-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    <p class="font-semibold z-10">Mapa interactivo</p>
                    <p class="text-xs z-10">Mueve el pin para indicar la ubicación</p>
                </div>
                <p class="text-center text-xs text-gray-400">Mueve el pin para indicar la ubicación aproximada.</p>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    Datos de contacto
                </h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del responsable</label>
                    <input type="text" value="Carlos Martínez" readonly class="w-full border-gray-200 rounded-lg bg-gray-100 text-gray-500 py-2.5 px-3 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Este nombre se muestra porque estás logueado</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                        <input type="text" name="telefono" placeholder="+52 123 456 789" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp" placeholder="+52 123 456 789" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                </div>

                <div class="flex items-start mb-6">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="publico" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label class="font-medium text-gray-700">Mostrar mi número públicamente en la publicación</label>
                        <p class="text-gray-500 text-xs">Si no seleccionas esta opción, solo recibirás mensajes a través de la plataforma</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-8">
                <button type="submit" class="w-full md:w-1/2 bg-primary hover:bg-orange-600 text-white font-bold py-3 rounded-lg shadow-lg transition">
                    ✓ Publicar en adopción
                </button>
                <button type="button" class="w-full md:w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-lg transition">
                    Cancelar
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800">
                <ul class="list-disc pl-5 space-y-1">
                    <li>Las adopciones deben ser responsables. Comparte toda la información disponible.</li>
                    <li>Tu publicación será revisada antes de mostrarse en el sitio.</li>
                    <li>Las fotos deben ser en formato JPG o PNG.</li>
                </ul>
            </div>

        </form>
    </div>
</div>
@endsection