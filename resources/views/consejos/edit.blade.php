@extends('layout.app')

@section('content')
<div class="bg-white min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <a href="{{ route('consejos.mis-consejos') }}"
           class="inline-flex items-center text-orange-500 hover:text-orange-600 mb-6 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a mis consejos
        </a>

        <div class="mb-8">
            <span class="inline-flex items-center rounded-full bg-orange-50 text-orange-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-orange-100">
                Panel institucional
            </span>

            <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Editar consejo
            </h1>

            <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                Al guardar cambios, el consejo volverá a revisión.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                <strong class="font-bold">Revisa estos campos:</strong>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('consejos.update', $consejo->id_consejo) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-10">
            @csrf
            @method('PUT')

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Información general</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Título <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="titulo"
                               value="{{ old('titulo', $consejo->titulo) }}"
                               maxlength="100"
                               class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/50">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Resumen <span class="text-red-500">*</span></label>
                        <textarea name="resumen"
                                  rows="3"
                                  maxlength="200"
                                  class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 p-4 text-gray-700 bg-gray-50/50">{{ old('resumen', $consejo->resumen) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Categoría <span class="text-red-500">*</span></label>
                        <select name="categoria_id"
                                class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/50">
                            <option value="">Selecciona...</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}" {{ old('categoria_id', $consejo->categoria_id) == $categoria->id_categoria ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Especie <span class="text-red-500">*</span></label>
                        <select name="especie_id"
                                class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-700 bg-gray-50/50">
                            <option value="">Selecciona...</option>
                            @foreach($especies as $especie)
                                <option value="{{ $especie->id_especie }}" {{ old('especie_id', $consejo->especie_id) == $especie->id_especie ? 'selected' : '' }}>
                                    {{ $especie->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Etiquetas</h2>

                <div class="flex flex-wrap gap-3">
                    @php
                        $etiquetasSeleccionadas = old('etiquetas', $consejo->etiquetas->pluck('id_etiqueta')->toArray());
                    @endphp

                    @foreach($etiquetas as $etiqueta)
                        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-orange-50 cursor-pointer">
                            <input type="checkbox"
                                   name="etiquetas[]"
                                   value="{{ $etiqueta->id_etiqueta }}"
                                   {{ in_array($etiqueta->id_etiqueta, $etiquetasSeleccionadas) ? 'checked' : '' }}
                                   class="text-orange-500 focus:ring-orange-500">
                            <span class="text-sm text-gray-700 font-medium">{{ $etiqueta->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Contenido</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Consejo completo <span class="text-red-500">*</span></label>
                    <textarea name="contenido"
                              rows="10"
                              class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 p-4 text-gray-700 bg-gray-50/50">{{ old('contenido', $consejo->contenido) }}</textarea>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Imágenes actuales</h2>

                @if($consejo->imagenes->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($consejo->imagenes as $imagen)
                            <div class="rounded-2xl border border-gray-100 bg-white overflow-hidden shadow-sm">
                                <img src="{{ asset('storage/' . $imagen->url) }}"
                                     alt="Imagen del consejo"
                                     class="w-full h-48 object-cover">

                                <div class="p-4">
                                    <label class="inline-flex items-center gap-2 text-sm text-red-600 font-medium cursor-pointer">
                                        <input type="checkbox"
                                               name="eliminar_imagenes[]"
                                               value="{{ $imagen->id_imagen }}"
                                               class="text-red-500 focus:ring-red-500">
                                        Eliminar esta imagen
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Este consejo no tiene imágenes registradas.</p>
                @endif
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Agregar nuevas imágenes</h2>

                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6">
                    <label class="block text-sm font-medium text-gray-500 mb-3">Puedes mantener hasta 3 imágenes en total</label>
                    <input type="file"
                           name="imagenes[]"
                           multiple
                           accept=".jpg,.jpeg,.png"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-500 file:text-white hover:file:bg-orange-600">
                </div>
            </section>

            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('consejos.mis-consejos') }}"
                   class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition text-center">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-orange-500 text-white font-medium hover:bg-orange-600 shadow-sm transition">
                    Guardar y enviar a revisión
                </button>
            </div>
        </form>
    </div>
</div>
@endsection