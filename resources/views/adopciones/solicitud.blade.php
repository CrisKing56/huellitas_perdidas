@extends('layout.app')

@section('content')
@php
    $especieTexto = $mascota->especie->nombre ?? match ((int) $mascota->especie_id) {
        1 => 'Perro',
        2 => 'Gato',
        default => 'Mascota',
    };

    $razaTexto = $mascota->raza->nombre ?? ($mascota->otra_raza ?? 'No especificada');
@endphp

<div class="bg-white min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <a href="{{ route('adopciones.show', $mascota->id_publicacion) }}"
           class="inline-flex items-center text-green-600 font-medium mb-6 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver al detalle
        </a>

        <div class="mb-8">
            <span class="inline-flex items-center rounded-full bg-green-50 text-green-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-green-100">
                Formulario de adopción
            </span>

            <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Solicitud de adopción
            </h1>

            <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                Completa este formulario para solicitar la adopción responsable de esta mascota.
            </p>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                <strong class="font-bold">Faltan o están incorrectos algunos campos:</strong>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-start gap-5">
            <div class="w-full md:w-28 h-28 rounded-2xl overflow-hidden flex-shrink-0 bg-gray-100">
                @if($mascota->fotoPrincipal)
                    <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}"
                         alt="{{ $mascota->nombre }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $mascota->nombre }}</h2>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full uppercase">
                        {{ str_replace('_', ' ', $mascota->estado) }}
                    </span>
                </div>

                <p class="text-gray-500 text-sm">
                    {{ $especieTexto }} · {{ $razaTexto }} · {{ $mascota->edad_anios ?? 'N/D' }} años · {{ ucfirst(strtolower($mascota->sexo ?? 'Desconocido')) }}
                </p>

                <p class="text-sm text-gray-500 mt-2">
                    Responsable: <span class="font-medium text-gray-700">{{ $mascota->autor->nombre ?? $mascota->autor->name ?? 'Usuario' }}</span>
                </p>
            </div>
        </div>

        <form action="{{ route('adopciones.solicitudes.store', $mascota->id_publicacion) }}"
              method="POST"
              class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-10">
            @csrf

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-green-100 p-2 rounded-full text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold">Datos personales</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="nombre_completo"
                               value="{{ old('nombre_completo', auth()->user()->nombre ?? '') }}"
                               placeholder="Ej: María González López"
                               class="w-full rounded-xl py-3 px-4 text-gray-700 bg-gray-50/50 border {{ $errors->has('nombre_completo') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-green-500 focus:ring-green-500' }}">
                        @error('nombre_completo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Edad <span class="text-red-500">*</span></label>
                        <input type="number"
                               name="edad"
                               value="{{ old('edad') }}"
                               placeholder="Ej: 28"
                               min="18"
                               max="120"
                               class="w-full rounded-xl py-3 px-4 text-gray-700 bg-gray-50/50 border {{ $errors->has('edad') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-green-500 focus:ring-green-500' }}">
                        @error('edad')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado civil <span class="text-red-500">*</span></label>
                        <select name="estado_civil"
                                class="w-full rounded-xl py-3 px-4 text-gray-700 bg-gray-50/50 border {{ $errors->has('estado_civil') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-green-500 focus:ring-green-500' }}">
                            <option value="">Selecciona...</option>
                            <option value="SOLTERO" {{ old('estado_civil') == 'SOLTERO' ? 'selected' : '' }}>Soltero/a</option>
                            <option value="CASADO" {{ old('estado_civil') == 'CASADO' ? 'selected' : '' }}>Casado/a</option>
                            <option value="UNION_LIBRE" {{ old('estado_civil') == 'UNION_LIBRE' ? 'selected' : '' }}>Unión libre</option>
                        </select>
                        @error('estado_civil')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cuenta del sistema</label>
                        <input type="text"
                               value="{{ auth()->user()->correo ?? 'Sin correo' }}"
                               readonly
                               class="w-full rounded-xl border-gray-200 py-3 px-4 text-gray-500 bg-gray-100 cursor-not-allowed">
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-green-100 p-2 rounded-full text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold">Información de vivienda</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de vivienda <span class="text-red-500">*</span></label>
                        <select name="tipo_vivienda"
                                class="w-full rounded-xl py-3 px-4 text-gray-700 bg-gray-50/50 border {{ $errors->has('tipo_vivienda') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-green-500 focus:ring-green-500' }}">
                            <option value="">Selecciona...</option>
                            <option value="CASA" {{ old('tipo_vivienda') == 'CASA' ? 'selected' : '' }}>Casa</option>
                            <option value="DEPARTAMENTO" {{ old('tipo_vivienda') == 'DEPARTAMENTO' ? 'selected' : '' }}>Departamento</option>
                            <option value="CUARTO" {{ old('tipo_vivienda') == 'CUARTO' ? 'selected' : '' }}>Cuarto</option>
                            <option value="OTRO" {{ old('tipo_vivienda') == 'OTRO' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_vivienda')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">¿Tiene patio? <span class="text-red-500">*</span></label>
                        <select name="tiene_patio"
                                class="w-full rounded-xl py-3 px-4 text-gray-700 bg-gray-50/50 border {{ $errors->has('tiene_patio') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-green-500 focus:ring-green-500' }}">
                            <option value="">Selecciona...</option>
                            <option value="1" {{ old('tiene_patio') === '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ old('tiene_patio') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('tiene_patio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">¿Todos están de acuerdo con la adopción? <span class="text-red-500">*</span></label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="todos_de_acuerdo" value="1" {{ old('todos_de_acuerdo') === '1' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500">
                            <span class="text-gray-700">Sí</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="todos_de_acuerdo" value="0" {{ old('todos_de_acuerdo') === '0' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500">
                            <span class="text-gray-700">No</span>
                        </label>
                    </div>
                    @error('todos_de_acuerdo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-green-100 p-2 rounded-full text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold">Motivo de adopción</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">¿Por qué deseas adoptar a esta mascota? <span class="text-red-500">*</span></label>
                    <textarea name="motivo_adopcion"
                              rows="5"
                              placeholder="Comparte tus razones para adoptar y cómo sería el hogar que le ofrecerías..."
                              class="w-full rounded-xl p-4 text-gray-700 bg-gray-50/50 border {{ $errors->has('motivo_adopcion') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-green-500 focus:ring-green-500' }}">{{ old('motivo_adopcion') }}</textarea>
                    @error('motivo_adopcion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <div class="bg-green-50 border border-green-100 rounded-2xl p-6">
                <h4 class="font-bold text-gray-800 mb-2 text-sm">Compromiso de adopción responsable</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Acepto que la información proporcionada es verídica y me comprometo a brindar un hogar responsable, amoroso y permanente a la mascota.
                </p>
            </div>

            <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('adopciones.show', $mascota->id_publicacion) }}"
                   class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-green-600 text-white font-medium hover:bg-green-700 shadow-sm transition">
                    Enviar solicitud
                </button>
            </div>

        </form>
    </div>
</div>
@endsection