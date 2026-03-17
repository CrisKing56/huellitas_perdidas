@extends('layout.app')

@section('content')

    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Registro de Refugio</h1>
            <p class="text-gray-500 mt-2">Únete a nuestra red para dar más visibilidad a los peluditos que buscan hogar.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('registro.refugio.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-8">
            @csrf

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Datos de la Cuenta y Contacto</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Refugio <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre_refugio" value="{{ old('nombre_refugio') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. Refugio San Francisco" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="correo" value="{{ old('correo') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="contacto@refugio.com" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:tracking-widest" placeholder="••••••••" required minlength="8">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:tracking-widest" placeholder="••••••••" required minlength="8">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono (10 dígitos) <span class="text-red-500">*</span></label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. 9191234567" required maxlength="10">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp (Opcional)</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. 9191234567" maxlength="10">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción del refugio <span class="text-red-500">*</span></label>
                        <textarea name="descripcion" rows="3" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" required placeholder="Cuéntanos sobre la misión, historia y labor del refugio...">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Ubicación</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Calle y Número <span class="text-red-500">*</span></label>
                        <input type="text" name="calle_numero" value="{{ old('calle_numero') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. Avenida Central #123" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Colonia/Barrio <span class="text-red-500">*</span></label>
                        <input type="text" name="colonia" value="{{ old('colonia') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. Centro" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal <span class="text-red-500">*</span></label>
                        <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. 29950" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad <span class="text-red-500">*</span></label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. Ocosingo" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                        <input type="text" name="estado_direccion" value="{{ old('estado_direccion') }}" class="block w-full p-3 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-500 placeholder:italic" placeholder="Ej. Chiapas" required>
                    </div>

                    <input type="hidden" name="latitud" value="16.90600000">
                    <input type="hidden" name="longitud" value="-92.09330000">
                </div>
            </div>

            <div class="mb-8 bg-orange-50 p-6 rounded-lg border border-orange-200">
                <h3 class="text-lg font-semibold text-orange-900 mb-4 border-b border-orange-200 pb-2">Detalles del Refugio</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad máx. de perros <span class="text-red-500">*</span></label>
                        <input type="number" name="capacidad_perros" value="{{ old('capacidad_perros') }}" min="0" class="block w-full p-3 bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400 placeholder:italic" placeholder="Ej. 50" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad máx. de gatos <span class="text-red-500">*</span></label>
                        <input type="number" name="capacidad_gatos" value="{{ old('capacidad_gatos') }}" min="0" class="block w-full p-3 bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400 placeholder:italic" placeholder="Ej. 30" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Requisitos generales de adopción <span class="text-red-500">*</span></label>
                        <textarea name="requisitos_adopcion" rows="3" class="block w-full p-3 bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400 placeholder:italic" required placeholder="Ej. Identificación oficial, comprobante de domicilio reciente, visita previa a la casa, firma de contrato...">{{ old('requisitos_adopcion') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">¿Aceptan donaciones en especie? <span class="text-red-500">*</span></label>
                        <select name="acepta_donaciones" class="block w-full p-3 bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
                            <option value="1">Sí, aceptamos donaciones</option>
                            <option value="0">No por el momento</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">¿Qué tipo de donaciones? (Opcional)</label>
                        <input type="text" name="tipo_donaciones" value="{{ old('tipo_donaciones') }}" class="block w-full p-3 bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400 placeholder:italic" placeholder="Ej. Croquetas, cobijas, arena...">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Fotografías del Refugio</h3>
                <div class="mt-1 flex flex-col justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 hover:border-orange-400 transition-colors">
                    
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        
                        <div class="flex text-sm text-gray-600 justify-center mt-4">
                            <label for="fotos" class="relative cursor-pointer bg-orange-500 rounded-md py-2 px-4 font-medium text-white hover:bg-orange-600 shadow-sm focus-within:outline-none">
                                <span>Seleccionar archivos</span>
                                <input id="fotos" name="fotos[]" type="file" class="sr-only" multiple accept="image/png, image/jpeg, image/jpg" required>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Sube entre 1 y 5 fotos de las instalaciones (JPG o PNG, máx 5MB).</p>
                    </div>

                    <div id="vista-previa-contenedor" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-6">
                    </div>

                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-lg font-bold shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Buscamos el input de fotos y el contenedor donde las vamos a mostrar
            const inputFotos = document.getElementById('fotos');
            const contenedorPreview = document.getElementById('vista-previa-contenedor');

            // Si existen en la página, le agregamos el "escuchador" de eventos
            if(inputFotos && contenedorPreview) {
                inputFotos.addEventListener('change', function(event) {
                    // 1. Limpiamos las fotos viejas si el usuario vuelve a elegir otras
                    contenedorPreview.innerHTML = ''; 
                    
                    const archivos = event.target.files;

                    // 2. Si hay archivos seleccionados, los procesamos
                    if (archivos) {
                        Array.from(archivos).forEach(archivo => {
                            // Validamos que sea solo imagen
                            if (archivo.type.match('image.*')) {
                                const lector = new FileReader();
                                
                                // 3. Cuando se termine de leer, creamos la miniatura HTML
                                lector.onload = function(e) {
                                    // Creamos un cajoncito para la foto
                                    const divFoto = document.createElement('div');
                                    divFoto.className = "relative rounded-lg overflow-hidden h-24 shadow-sm border border-gray-200 bg-white";
                                    
                                    // Creamos la imagen en sí
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.className = "w-full h-full object-cover";
                                    
                                    // Las metemos a la página
                                    divFoto.appendChild(img);
                                    contenedorPreview.appendChild(divFoto);
                                }
                                
                                lector.readAsDataURL(archivo);
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection