@extends('layout.app')

@section('title', 'Editar Adopción')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar publicación</h1>
                <p class="text-gray-500 mt-1">Actualiza los datos de la mascota en adopción</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('adopciones.mis-adopciones') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ← Volver a mis adopciones
                </a>
            </div>
        </div>

        <form action="{{ route('adopciones.update', $adopcion->id_publicacion) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-10">
                
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-green-100 text-green-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Información básica
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre', $adopcion->nombre) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especie <span class="text-red-500">*</span></label>
                            <select name="especie_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3 bg-white">
                                <option value="1" {{ old('especie_id', $adopcion->especie_id) == 1 ? 'selected' : '' }}>Perro</option>
                                <option value="2" {{ old('especie_id', $adopcion->especie_id) == 2 ? 'selected' : '' }}>Gato</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Raza</label>
                            <input type="text" name="otra_raza" value="{{ old('otra_raza', $adopcion->otra_raza) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Edad (Años) <span class="text-red-500">*</span></label>
                            <input type="number" name="edad_anios" value="{{ old('edad_anios', $adopcion->edad_anios) }}" min="0" max="30" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                            <select name="sexo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3 bg-white">
                                <option value="MACHO" {{ old('sexo', $adopcion->sexo) == 'MACHO' ? 'selected' : '' }}>Macho</option>
                                <option value="HEMBRA" {{ old('sexo', $adopcion->sexo) == 'HEMBRA' ? 'selected' : '' }}>Hembra</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño <span class="text-red-500">*</span></label>
                            <select name="tamano" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3 bg-white">
                                <option value="CHICO" {{ old('tamano', $adopcion->tamano) == 'CHICO' ? 'selected' : '' }}>Chico / Pequeño</option>
                                <option value="MEDIANO" {{ old('tamano', $adopcion->tamano) == 'MEDIANO' ? 'selected' : '' }}>Mediano</option>
                                <option value="GRANDE" {{ old('tamano', $adopcion->tamano) == 'GRANDE' ? 'selected' : '' }}>Grande</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color predominante <span class="text-red-500">*</span></label>
                            <input type="text" name="color_predominante" value="{{ old('color_predominante', $adopcion->color_predominante) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Historia / Descripción <span class="text-red-500">*</span></label>
                            <textarea name="descripcion" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-white">{{ old('descripcion', $adopcion->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-green-100 text-green-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </span>
                        Estado de salud
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vacunas aplicadas</label>
                            <input type="text" name="vacunas_aplicadas" value="{{ old('vacunas_aplicadas', $adopcion->vacunas_aplicadas) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">¿Está esterilizado?</label>
                            <select name="esterilizado" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3 bg-white">
                                <option value="1" {{ old('esterilizado', $adopcion->esterilizado) == 1 ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ old('esterilizado', $adopcion->esterilizado) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Condición de salud (Breve)</label>
                        <input type="text" name="condicion_salud" value="{{ old('condicion_salud', $adopcion->condicion_salud) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción detallada de salud</label>
                        <textarea name="descripcion_salud" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-white">{{ old('descripcion_salud', $adopcion->descripcion_salud) }}</textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-green-100 text-green-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                        Requisitos para adoptantes
                    </h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Requisitos <span class="text-red-500">*</span></label>
                        <textarea name="requisitos" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 bg-white">{{ old('requisitos', $adopcion->requisitos) }}</textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-green-100 text-green-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        Fotografía
                    </h2>
                    
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                        @if($adopcion->fotoPrincipal)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-2">Imagen actual:</p>
                                <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}" class="h-32 w-auto mx-auto rounded-lg shadow-md object-cover">
                            </div>
                        @endif

                        <div class="flex flex-col items-center">
                            <label class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full shadow cursor-pointer transition transform hover:scale-105">
                                Cambiar fotografía
                                <input type="file" name="foto" class="hidden">
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Deja vacío para mantener la actual.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-green-100 text-green-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        Ubicación
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Colonia / Barrio <span class="text-red-500">*</span></label>
                            <input type="text" name="colonia_barrio" value="{{ old('colonia_barrio', $adopcion->colonia_barrio) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Calle / Referencias</label>
                            <input type="text" name="calle_referencias" value="{{ old('calle_referencias', $adopcion->calle_referencias) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-3">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="{{ route('adopciones.mis-adopciones') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition transform hover:-translate-y-0.5">
                        Guardar cambios
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection