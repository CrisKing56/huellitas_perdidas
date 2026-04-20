@extends('layout.app')

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Consejos y Cuidados</h1>
                <p class="text-gray-500 mt-2">Aprende recomendaciones útiles para el bienestar y cuidado responsable de las mascotas.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if($puedePublicarConsejo)
                    <a href="{{ route('consejos.create') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-orange-500 border border-transparent rounded-xl font-semibold text-white hover:bg-orange-600 transition shadow-sm">
                        Publicar consejo
                    </a>

                    <a href="{{ route('consejos.mis-consejos') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-200 transition">
                        Mis consejos
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <aside class="lg:col-span-3">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm lg:sticky lg:top-6 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900">Filtros</h2>
                    </div>

                    <form method="GET" action="{{ route('consejos.index') }}" class="p-5 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                            <input type="text"
                                   name="q"
                                   value="{{ $filtros['q'] }}"
                                   placeholder="Título, resumen u organización"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
                            <select name="categoria"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Todas</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id_categoria }}" {{ $filtros['categoria'] == $categoria->id_categoria ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Especie</label>
                            <select name="especie"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Todas</option>
                                @foreach($especies as $especie)
                                    <option value="{{ $especie->id_especie }}" {{ $filtros['especie'] == $especie->id_especie ? 'selected' : '' }}>
                                        {{ $especie->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Etiqueta</label>
                            <select name="etiqueta"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Todas</option>
                                @foreach($etiquetas as $etiqueta)
                                    <option value="{{ $etiqueta->id_etiqueta }}" {{ $filtros['etiqueta'] == $etiqueta->id_etiqueta ? 'selected' : '' }}>
                                        {{ $etiqueta->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-3 pt-2">
                            <button type="submit"
                                    class="w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600 transition">
                                Aplicar filtros
                            </button>

                            <a href="{{ route('consejos.index') }}"
                               class="w-full rounded-xl bg-gray-100 px-4 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-200 transition">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <section class="lg:col-span-9">
                @if($consejos->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                        @foreach($consejos as $consejo)
                            @php
                                $rutaOrganizacion = $consejo->organizacion
                                    ? ($consejo->organizacion->tipo === 'REFUGIO'
                                        ? route('refugios.show', $consejo->organizacion->id_organizacion)
                                        : route('veterinarias.show', $consejo->organizacion->id_organizacion))
                                    : '#';
                            @endphp

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                                <div class="h-52 bg-gray-100 relative overflow-hidden group">
                                    @if($consejo->imagenes->count() > 0)
                                        <img src="{{ asset('storage/' . $consejo->imagenes->first()->url) }}"
                                             alt="{{ $consejo->titulo }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 text-xs font-bold bg-white text-orange-600 rounded-full shadow-sm">
                                            {{ $consejo->categoria?->nombre ?? 'General' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-6 flex flex-col h-full">
                                    <div class="mb-4">
                                        <h2 class="text-xl font-bold text-gray-900 leading-tight line-clamp-2">
                                            {{ $consejo->titulo }}
                                        </h2>

                                        <p class="text-sm text-gray-500 mt-2 line-clamp-3">
                                            {{ $consejo->resumen }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach($consejo->etiquetas->take(3) as $etiqueta)
                                            <span class="px-2.5 py-1 rounded-full bg-orange-50 border border-orange-100 text-orange-700 text-xs font-semibold">
                                                {{ $etiqueta->nombre }}
                                            </span>
                                        @endforeach
                                    </div>

                                    <div class="mt-auto pt-4 border-t border-gray-100">
                                        <a href="{{ $rutaOrganizacion }}" class="block text-sm font-semibold text-gray-800 hover:text-orange-600 transition">
                                            {{ $consejo->organizacion?->nombre ?? 'Organización' }}
                                        </a>

                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($consejo->creado_en)->translatedFormat('d \d\e F, Y') }}
                                        </p>

                                        <a href="{{ route('consejos.show', $consejo->id_consejo) }}"
                                           class="mt-4 inline-flex items-center justify-center w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600 transition">
                                            Ver consejo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $consejos->links() }}
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-gray-200 bg-white py-16 text-center">
                        <h3 class="text-xl font-bold text-gray-900">No hay consejos para mostrar</h3>
                        <p class="text-gray-500 mt-2">Prueba con otros filtros o vuelve más tarde.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection