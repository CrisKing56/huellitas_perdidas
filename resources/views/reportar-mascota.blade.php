@extends('layout.app')

@section('title', 'Reportar Mascota Extraviada')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="container mx-auto px-4 lg:px-20">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Reportar mascota extraviada</h1>
            <p class="text-gray-500">Completa la información para ayudar a encontrarla.</p>
        </div>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">¡Faltan datos!</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <form action="{{ route('mascotas.store')}}" method="POST" enctype="multipart/form-data" class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @csrf <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Información de la mascota
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la mascota <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" placeholder="Ej: Max" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especie <span class="text-red-500">*</span></label>
                        <select name="especie_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="1">Perro</option>
                            <option value="2">Gato</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raza <span class="text-red-500">*</span></label>
                        <input type="text" name="raza" placeholder="Ej: Golden Retriever" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color <span class="text-red-500">*</span></label>
                        <input type="text" name="color" placeholder="Ej: Dorado, blanco y negro" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño <span class="text-red-500">*</span></label>
                        <select name="tamano" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="pequeno">Pequeño</option>
                            <option value="mediano">Mediano</option>
                            <option value="grande">Grande</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                        <select name="sexo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="macho">Macho</option>
                            <option value="hembra">Hembra</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de extravío <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha_extravio" class="w-full md:w-1/2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border text-gray-500">
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

                    <label class="bg-primary hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-lg shadow cursor-pointer inline-block text-center">
    
                        Seleccionar archivo
                        <input type="file" name="foto" class="hidden">
                        
                    </label>

                    <span id="nombre-archivo" class="ml-3 text-gray-500 text-sm">Ningún archivo seleccionado</span>
                    <script>
                        // Pequeño script para que aparezca el nombre del archivo al seleccionarlo
                        document.querySelector('input[name="foto"]').addEventListener('change', function(e) {
                            var fileName = e.target.files[0].name;
                            document.getElementById('nombre-archivo').innerText = fileName;
                        });
                    </script>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    Ubicación del extravío
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Colonia o barrio <span class="text-red-500">*</span></label>
                        <input type="text" name="colonia_barrio" placeholder="Ej: Centro, Retiro" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Calle y referencias (opcional)</label>
                        <input type="text" name="calle_referencias" placeholder="Ej: Cerca del parque principal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-3 bg-gray-50 border">
                    </div>
                </div>

                <div class="w-full h-64 bg-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-500 border border-gray-300 relative overflow-hidden mb-2">
                    <div class="absolute inset-0 opacity-10 bg-[url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg')] bg-cover bg-center"></div>
                    <svg class="w-12 h-12 text-primary mb-2 z-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    <p class="font-semibold z-10">Mapa interactivo</p>
                    <p class="text-xs z-10">Mueve el pin para indicar el lugar donde se extravió</p>
                </div>
                <p class="text-center text-xs text-gray-400">Mueve el pin para indicar el lugar donde se extravió.</p>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Descripción
                </h2>
                <textarea name="descripcion" rows="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-3 px-4 bg-white border placeholder-gray-400" placeholder="Describe qué pasó y cualquier detalle que ayude a identificar a la mascota. Ejemplo: comportamiento, señas particulares..."></textarea>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-primary p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    Datos de contacto del dueño
                </h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del dueño</label>
                    <input type="text" value="{{ Auth::user()->nombre }}" readonly class="w-full border-gray-200 rounded-lg bg-gray-100 text-gray-500 py-2.5 px-3 cursor-not-allowed">
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
                    ✓ Publicar reporte
                </button>
                <button type="button" class="w-full md:w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-lg transition">
                    Cancelar
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800">
                <ul class="list-disc pl-5 space-y-1">
                    <li>Tu reporte será revisado antes de publicarse.</li>
                    <li>Las fotos deben ser en formato JPG o PNG.</li>
                    <li>Asegúrate de proporcionar la mayor cantidad de detalles posibles.</li>
                </ul>
            </div>

        </form>
    </div>
</div>
@endsection