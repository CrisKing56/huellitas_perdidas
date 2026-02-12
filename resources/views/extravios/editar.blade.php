@extends('layout.app') 
@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar publicación</h1>
                <p class="text-gray-500 mt-1">Actualiza los datos de tu mascota</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('extravios.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    ← Volver al menú
                </a>
            </div>
        </div>

        <form action="{{ route('extravios.update', $publicacion->id_publicacion) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @csrf
            @method('PUT') <div class="p-8 space-y-10">
                
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-orange-100 text-orange-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Información de la mascota
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la mascota <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre', $publicacion->nombre) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especie <span class="text-red-500">*</span></label>
                            <select name="especie_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3 bg-white">
                                <option value="1" {{ $publicacion->especie_id == 1 ? 'selected' : '' }}>Perro</option>
                                <option value="2" {{ $publicacion->especie_id == 2 ? 'selected' : '' }}>Gato</option>
                                <option value="3" {{ $publicacion->especie_id == 3 ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Raza <span class="text-red-500">*</span></label>
                            <input type="text" name="raza" value="{{ old('raza', $publicacion->raza ?? '') }}" placeholder="Ej: Golden Retriever" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Extravío <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_extravio" value="{{ old('fecha_extravio', $publicacion->fecha_extravio) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3 text-gray-600">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                            <select name="sexo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3 bg-white">
                                <option value="Macho" {{ $publicacion->sexo == 'Macho' ? 'selected' : '' }}>Macho</option>
                                <option value="Hembra" {{ $publicacion->sexo == 'Hembra' ? 'selected' : '' }}>Hembra</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño <span class="text-red-500">*</span></label>
                            <select name="tamano" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3 bg-white">
                                <option value="Pequeño" {{ $publicacion->tamano == 'Pequeño' ? 'selected' : '' }}>Pequeño</option>
                                <option value="Mediano" {{ $publicacion->tamano == 'Mediano' ? 'selected' : '' }}>Mediano</option>
                                <option value="Grande" {{ $publicacion->tamano == 'Grande' ? 'selected' : '' }}>Grande</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color <span class="text-red-500">*</span></label>
                            <input type="text" name="color" value="{{ old('color', $publicacion->color) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-red-500">*</span></label>
                            <textarea name="descripcion" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-3 px-4 bg-white" placeholder="Describe las características...">{{ old('descripcion', $publicacion->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-orange-100 text-orange-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        Fotografía
                    </h2>
                    
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                        @if($publicacion->fotoPrincipal)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-2">Imagen actual:</p>
                                <img src="{{ asset('storage/' . $publicacion->fotoPrincipal->url) }}" class="h-32 w-auto mx-auto rounded-lg shadow-md object-cover">
                            </div>
                        @endif

                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="text-gray-900 font-medium mb-1">Sube una nueva fotografía (opcional)</p>
                            <p class="text-xs text-gray-500 mb-4">Archivos JPG o PNG. Máximo 5MB.</p>
                            
                            <label class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-full shadow cursor-pointer transition transform hover:scale-105">
                                Seleccionar archivo
                                <input type="file" name="foto" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-orange-100 text-orange-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        Ubicación del último avistamiento
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Colonia / Barrio <span class="text-red-500">*</span></label>
                            <input type="text" name="colonia_barrio" value="{{ old('colonia_barrio', $publicacion->colonia_barrio) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Calle / Referencias</label>
                            <input type="text" name="calle" value="{{ old('calle', $publicacion->calle_referencias) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3">
                        </div>
                    </div>

                    <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 border border-gray-300">
                        <div class="text-center">
                            <svg class="w-10 h-10 mx-auto text-orange-400 mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            <p class="font-medium">Mapa interactivo</p>
                            <p class="text-xs">Haz clic para marcar la ubicación</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                     <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="bg-orange-100 text-orange-600 p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </span>
                        Datos de contacto
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del contacto</label>
                             <input type="text" value="{{ Auth::user()->nombre ?? 'Usuario' }}" readonly class="w-full border-gray-200 bg-gray-100 rounded-lg py-2.5 px-3 text-gray-500 cursor-not-allowed">
                        </div>
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                             <input type="text" name="telefono" value="{{ old('telefono', '9191234567') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-3">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="{{ route('extravios.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-orange-500 text-white font-bold rounded-lg shadow-lg hover:bg-orange-600 transition transform hover:-translate-y-0.5">
                        Guardar cambios
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection