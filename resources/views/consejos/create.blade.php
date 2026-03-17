@extends('layout.app')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <a href="{{ route('inicio') }}" class="inline-flex items-center text-orange-500 hover:text-orange-600 mb-6 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Volver al menú
        </a>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Publicar consejo</h1>
            <p class="text-gray-500 mt-2">Comparte tus conocimientos y ayuda a la comunidad con consejos útiles sobre cuidado animal</p>
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

        <form action="{{ route('consejos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-8">
            @csrf

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Información básica
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Título del consejo <span class="text-red-500">*</span></label>
                        <input type="text" name="titulo" value="{{ old('titulo') }}" placeholder="Ej: Cómo cuidar a tu cachorro durante el primer mes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required maxlength="100">
                        <p class="mt-1 text-xs text-gray-400">Máximo 100 caracteres</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Resumen breve <span class="text-red-500">*</span></label>
                        <textarea name="resumen" rows="2" placeholder="Escribe un resumen corto que aparecerá en el catálogo de consejos..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required maxlength="200">{{ old('resumen') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Máximo 200 caracteres</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Categoría y audiencia
                </h2>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría <span class="text-red-500">*</span></label>
                        <select name="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="">Selecciona una categoría</option>
                            <option value="1">Salud</option>
                            <option value="2">Alimentación</option>
                            <option value="3">Higiene</option>
                            <option value="4">Entrenamiento</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Especie <span class="text-red-500">*</span></label>
                        <select name="especie_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="">Selecciona una especie</option>
                            <option value="1">Perro</option>
                            <option value="2">Gato</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Contenido del consejo
                </h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción completa <span class="text-red-500">*</span></label>
                    <textarea name="contenido" rows="8" placeholder="Escribe el contenido completo del consejo. Puedes incluir pasos, recomendaciones, precauciones, etc." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>{{ old('contenido') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Sé claro y específico. Incluye toda la información relevante que ayude a los dueños de mascotas.</p>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Imágenes ilustrativas (opcional)
                </h2>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-orange-500 transition-colors bg-gray-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-orange-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="imagenes" class="relative cursor-pointer bg-orange-500 rounded-md py-1 px-3 font-medium text-white hover:bg-orange-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                <span>Seleccionar archivos</span>
                                <input id="imagenes" name="imagenes[]" type="file" class="sr-only" multiple accept="image/png, image/jpeg, image/jpg">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Sube imágenes relacionadas con tu consejo. Archivos JPG o PNG. Máximo 5MB cada una.</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6 text-sm text-blue-800">
                <span class="font-bold flex items-center mb-1">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    Antes de publicar
                </span>
                <ul class="list-disc list-inside mt-2 space-y-1 ml-1 text-blue-700">
                    <li>Tu consejo pasará a un estado de PENDIENTE para revisión.</li>
                    <li>Asegúrate de que la información sea precisa y profesional.</li>
                </ul>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                <button type="submit" name="accion" value="borrador" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-md font-medium hover:bg-gray-200 transition-colors">
                    Guardar borrador
                </button>
                <button type="submit" name="accion" value="publicar" class="bg-orange-500 text-white px-6 py-2 rounded-md font-medium shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    Enviar a revisión
                </button>
            </div>
        </form>
    </div>
@endsection