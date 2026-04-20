@extends('layout.app')

@section('content')
@php
    $rutaOrganizacion = $consejo->organizacion
        ? ($consejo->organizacion->tipo === 'REFUGIO'
            ? route('refugios.show', $consejo->organizacion->id_organizacion)
            : route('veterinarias.show', $consejo->organizacion->id_organizacion))
        : '#';

    $textoTipo = $consejo->organizacion?->tipo === 'REFUGIO' ? 'Refugio' : 'Veterinaria';
@endphp

<div class="relative w-full h-96 lg:h-[28rem] bg-gray-900 flex items-end justify-center">
    @if($consejo->imagenes->count() > 0)
        <img src="{{ asset('storage/' . $consejo->imagenes->first()->url) }}"
             alt="{{ $consejo->titulo }}"
             class="absolute inset-0 w-full h-full object-cover opacity-60">
    @else
        <div class="absolute inset-0 w-full h-full bg-gray-800 opacity-60"></div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-transparent"></div>

    <div class="relative w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 z-10">
        <a href="{{ route('consejos.index') }}" class="inline-flex items-center text-orange-500 hover:text-orange-700 font-semibold mb-6">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver a consejos
        </a>

        <div class="mb-4 flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 text-sm font-bold bg-orange-500 text-white rounded-md shadow-sm">
                {{ $consejo->categoria?->nombre ?? 'General' }}
            </span>

            @foreach($consejo->etiquetas as $etiqueta)
                <span class="px-3 py-1 text-xs font-bold bg-white text-orange-600 rounded-full shadow-sm border border-orange-100">
                    {{ $etiqueta->nombre }}
                </span>
            @endforeach
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-4">
            {{ $consejo->titulo }}
        </h1>

        <div class="flex flex-wrap items-center text-sm text-gray-600 font-medium gap-3">
            <span>Por <span class="text-gray-900 font-bold">{{ $consejo->organizacion?->nombre ?? 'Organización' }}</span></span>
            <span>•</span>
            <span>{{ \Carbon\Carbon::parse($consejo->creado_en)->translatedFormat('d \d\e F, Y') }}</span>
            <span>•</span>
            <span>{{ $textoTipo }}</span>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8">
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed mb-12">
                {!! nl2br(e($consejo->contenido)) !!}
            </div>

            @if($consejo->imagenes->count() > 1)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    @foreach($consejo->imagenes->skip(1) as $imagenExtra)
                        <img src="{{ asset('storage/' . $imagenExtra->url) }}"
                             alt="Imagen ilustrativa"
                             class="rounded-2xl shadow-sm w-full h-64 object-cover">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lg:col-span-4">
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 sticky top-24 space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Autor del consejo</h3>

                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ $textoTipo }}</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $consejo->organizacion?->nombre ?? 'Organización' }}</p>

                        @if($consejo->organizacion?->descripcion)
                            <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($consejo->organizacion->descripcion, 180) }}
                            </p>
                        @endif

                        <a href="{{ $rutaOrganizacion }}"
                           class="mt-5 inline-flex items-center justify-center w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600 transition">
                            Ver perfil de {{ strtolower($textoTipo) }}
                        </a>
                    </div>
                </div>

                @auth
                    @php
                        $esAutorConsejo = (int) (auth()->user()->id_usuario ?? 0) === (int) ($consejo->organizacion->usuario_dueno_id ?? -1);
                    @endphp

                    @if(!$esAutorConsejo)
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Reportar consejo</h3>

                            <form action="{{ route('consejos.reportar', $consejo->id_consejo) }}" method="POST" class="rounded-2xl bg-red-50 border border-red-100 p-5 space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-sm font-medium text-red-700 mb-2">Motivo</label>
                                    <select name="motivo"
                                            class="w-full rounded-xl border-red-200 bg-white px-4 py-3 text-sm text-gray-700 focus:border-red-500 focus:ring-red-500">
                                        <option value="">Selecciona un motivo</option>
                                        <option value="SPAM" {{ old('motivo') === 'SPAM' ? 'selected' : '' }}>Spam</option>
                                        <option value="DUPLICADO" {{ old('motivo') === 'DUPLICADO' ? 'selected' : '' }}>Duplicado</option>
                                        <option value="CONTENIDO_INAPROPIADO" {{ old('motivo') === 'CONTENIDO_INAPROPIADO' ? 'selected' : '' }}>Contenido inapropiado</option>
                                        <option value="INFORMACION_FALSA" {{ old('motivo') === 'INFORMACION_FALSA' ? 'selected' : '' }}>Información falsa</option>
                                        <option value="OTRO" {{ old('motivo') === 'OTRO' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-red-700 mb-2">Descripción adicional</label>
                                    <textarea name="descripcion"
                                              rows="4"
                                              maxlength="500"
                                              placeholder="Describe brevemente por qué reportas este consejo..."
                                              class="w-full rounded-xl border-red-200 bg-white px-4 py-3 text-sm text-gray-700 focus:border-red-500 focus:ring-red-500">{{ old('descripcion') }}</textarea>
                                </div>

                                <button type="submit"
                                        class="w-full rounded-xl bg-red-500 px-4 py-3 text-sm font-semibold text-white hover:bg-red-600 transition">
                                    Enviar reporte
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

                @guest
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <p class="text-sm text-gray-600">
                            Inicia sesión para poder reportar este consejo si consideras que infringe las normas.
                        </p>
                    </div>
                @endguest
            </div>
        </div>
    </div>

    @if($consejosRelacionados->count())
        <div class="mt-16">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Consejos que te podrían interesar</h2>
                    <p class="text-sm text-gray-500 mt-1">Más recomendaciones relacionadas con esta temática.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($consejosRelacionados as $relacionado)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                        <div class="h-48 bg-gray-100">
                            @if($relacionado->imagenes->count())
                                <img src="{{ asset('storage/' . $relacionado->imagenes->first()->url) }}"
                                     alt="{{ $relacionado->titulo }}"
                                     class="w-full h-full object-cover">
                            @endif
                        </div>

                        <div class="p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-orange-600">
                                {{ $relacionado->categoria?->nombre ?? 'General' }}
                            </p>

                            <h3 class="text-lg font-bold text-gray-900 mt-2 line-clamp-2">
                                {{ $relacionado->titulo }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-2 line-clamp-3">
                                {{ $relacionado->resumen }}
                            </p>

                            <a href="{{ route('consejos.show', $relacionado->id_consejo) }}"
                               class="mt-4 inline-flex items-center justify-center w-full rounded-xl bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-600 hover:bg-orange-100 transition">
                                Ver consejo
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection