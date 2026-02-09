@extends('layout.app')

@section('title', 'Publicar Mascota en Adopción')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="container mx-auto px-4 lg:px-20">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Publicar mascota en adopción</h1>
            <p class="text-gray-500">Comparte los datos de la mascota para encontrarle un hogar responsable.</p>
        </div>

        <form action="{{ route('adopciones.store') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @csrf 

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Información de la mascota
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la mascota <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Luna" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especie <span class="text-red-500">*</span></label>
                        <select name="especie_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="1" {{ old('especie_id') == 1 ? 'selected' : '' }}>Perro</option>
                            <option value="2" {{ old('especie_id') == 2 ? 'selected' : '' }}>Gato</option>
                        </select>
                        @error('especie_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raza</label>
                        <input type="text" name="otra_raza" value="{{ old('otra_raza') }}" placeholder="Ej: Mestizo, Labrador" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Edad (Años) <span class="text-red-500">*</span></label>
                        <input type="number" name="edad_anios" value="{{ old('edad_anios') }}" min="0" max="30" placeholder="Ej: 2" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                        @error('edad_anios') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                        <select name="sexo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="MACHO" {{ old('sexo') == 'MACHO' ? 'selected' : '' }}>Macho</option>
                            <option value="HEMBRA" {{ old('sexo') == 'HEMBRA' ? 'selected' : '' }}>Hembra</option>
                        </select>
                        @error('sexo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño <span class="text-red-500">*</span></label>
                        <select name="tamano" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="">Seleccionar...</option>
                            <option value="CHICO" {{ old('tamano') == 'CHICO' ? 'selected' : '' }}>Chico / Pequeño</option>
                            <option value="MEDIANO" {{ old('tamano') == 'MEDIANO' ? 'selected' : '' }}>Mediano</option>
                            <option value="GRANDE" {{ old('tamano') == 'GRANDE' ? 'selected' : '' }}>Grande</option>
                        </select>
                        @error('tamano') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color predominante <span class="text-red-500">*</span></label>
                    <input type="text" name="color_predominante" value="{{ old('color_predominante') }}" placeholder="Ej: Marrón, blanco y negro" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    @error('color_predominante') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Historia / Descripción <span class="text-red-500">*</span></label>
                    <textarea name="descripcion" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4 bg-white border" placeholder="Cuenta un poco sobre su personalidad, historia, etc.">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Estado de salud y vacunas
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vacunas aplicadas</label>
                        <input type="text" name="vacunas_aplicadas" value="{{ old('vacunas_aplicadas') }}" placeholder="Ej: Parvovirus, Rabia" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Esterilizado/Castrado <span class="text-red-500">*</span></label>
                        <select name="esterilizado" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                            <option value="0" {{ old('esterilizado') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('esterilizado') == '1' ? 'selected' : '' }}>Sí</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">*Importante para adopción responsable</p>
                    </div>
                </div>

                <div class="mb-4">
                     <label class="block text-sm font-medium text-gray-700 mb-1">Condición de salud (Breve)</label>
                     <input type="text" name="condicion_salud" value="{{ old('condicion_salud') }}" placeholder="Ej: Buena, Requiere medicación..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción detallada de salud</label>
                    <textarea name="descripcion_salud" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4 bg-white border placeholder-gray-400" placeholder="Detalles sobre tratamientos, alergias, operaciones previas...">{{ old('descripcion_salud') }}</textarea>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </span>
                    Fotografía
                </h2>

                <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-10 text-center hover:bg-gray-100 transition relative">
                    <input type="file" name="foto" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                    <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                         <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">Haz clic aquí para subir la foto</p>
                    <p class="text-xs text-gray-500">JPG o PNG, máx 2MB</p>
                </div>
                @error('foto') <span class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    Requisitos para adoptantes
                </h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Describe qué buscas en el adoptante <span class="text-red-500">*</span></label>
                    <textarea name="requisitos" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4 bg-white border placeholder-gray-400" placeholder="Ejemplo: Ser mayor de edad, casa propia (no indispensable), seguimiento con fotos...">{{ old('requisitos') }}</textarea>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    Ubicación actual
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Colonia o barrio <span class="text-red-500">*</span></label>
                        <input type="text" name="colonia_barrio" value="{{ old('colonia_barrio') }}" placeholder="Ej: Centro" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                        @error('colonia_barrio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Calle y referencias</label>
                        <input type="text" name="calle_referencias" value="{{ old('calle_referencias') }}" placeholder="Ej: Cerca del parque" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    Datos de contacto
                </h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del responsable</label>
                    <input type="text" value="{{ Auth::user()->nombre ?? 'Usuario' }}" readonly class="w-full border-gray-200 rounded-lg bg-gray-100 text-gray-500 py-2.5 px-3 cursor-not-allowed">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de contacto</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="+52..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2.5 px-3 bg-white border">
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-8">
                <button type="submit" class="w-full md:w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:scale-[1.02]">
                    ✓ Publicar en adopción
                </button>
                <a href="{{ route('adopciones.mis-adopciones') }}" class="w-full md:w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-lg transition text-center">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>
@endsection