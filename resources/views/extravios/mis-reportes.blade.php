@extends('layout.app')

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        {{-- ENCABEZADO --}}
        <div class="mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center rounded-full bg-orange-50 text-orange-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-orange-100">
                            Panel personal
                        </span>

                        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                            Mis publicaciones
                        </h1>

                        <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                            Consulta, actualiza y administra tus reportes de mascotas extraviadas desde un solo lugar.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('mascotas.create') }}"
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-2xl shadow-sm transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Reportar mascota perdida
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- MENSAJES --}}
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- MÉTRICAS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total de publicaciones</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-medium">Activas</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['activas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-medium">Resueltas</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['resueltas'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- PESTAÑAS --}}
        <div class="mb-8 border-b border-gray-200">
            <nav class="-mb-px flex flex-wrap gap-8">
                <a href="#"
                   class="border-orange-500 text-orange-600 whitespace-nowrap pb-4 px-1 border-b-2 font-semibold text-sm">
                    Mascotas extraviadas
                    <span class="bg-orange-100 text-orange-600 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                        {{ $stats['total'] }}
                    </span>
                </a>

                <a href="{{ route('adopciones.mis-adopciones') }}"
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition">
                    Mascotas en adopción
                </a>
            </nav>
        </div>

        {{-- LISTADO --}}
        <div class="space-y-6">
            @forelse ($mascotas as $pub)
                @php
                    $fechaPublicacion = $pub->created_at ?? null;

                    $colores = [
                        'ACTIVA' => 'bg-green-100 text-green-700 border-green-200',
                        'REVISION' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'RESUELTA' => 'bg-blue-100 text-blue-700 border-blue-200',
                    ];

                    $claseColor = $colores[$pub->estado] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp

                <article class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col lg:flex-row gap-6">

                            {{-- FOTO --}}
                            <div class="w-full lg:w-44 shrink-0">
                                <div class="w-full h-52 lg:h-44 rounded-2xl overflow-hidden border border-gray-100 bg-gray-100">
                                    @if($pub->fotoPrincipal)
                                        <img src="{{ asset('storage/' . $pub->fotoPrincipal->url) }}"
                                             class="w-full h-full object-cover"
                                             alt="{{ $pub->nombre }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- CONTENIDO --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-3 mb-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide border {{ $claseColor }}">
                                                {{ $pub->estado }}
                                            </span>

                                            @if($fechaPublicacion)
                                                <span class="text-sm text-gray-400">
                                                    Publicado {{ \Carbon\Carbon::parse($fechaPublicacion)->locale('es')->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>

                                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">
                                            {{ $pub->nombre }}
                                        </h2>

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                            <span>Última zona reportada</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span class="font-medium text-gray-700">{{ $pub->colonia_barrio }}</span>
                                        </div>

                                        @if(!empty($pub->descripcion))
                                            <p class="mt-4 text-sm text-gray-600 leading-relaxed max-w-3xl">
                                                {{ \Illuminate\Support\Str::limit($pub->descripcion, 180) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="shrink-0">
                                        <a href="{{ route('extravios.show', $pub->id_publicacion) }}"
                                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 font-semibold text-sm hover:bg-orange-100 transition">
                                            Ver reporte
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="{{ route('extravios.show', $pub->id_publicacion) }}"
                                       class="inline-flex items-center gap-2 text-orange-600 bg-orange-50 hover:bg-orange-100 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver
                                    </a>

                                    @if($pub->estado === 'ACTIVA')
                                        <a href="{{ route('extravios.edit', $pub->id_publicacion) }}"
                                           class="inline-flex items-center gap-2 text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            Editar
                                        </a>

                                        <form action="{{ route('extravios.resolve', $pub->id_publicacion) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Deseas marcar este reporte como encontrado?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-2 text-green-700 bg-green-50 hover:bg-green-100 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Marcar como encontrada
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('extravios.destroy', $pub->id_publicacion) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar este reporte?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-14 bg-white rounded-3xl shadow-sm border border-dashed border-gray-200">
                    <div class="mx-auto w-16 h-16 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900">No tienes reportes todavía</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        Empieza reportando una mascota extraviada para que la comunidad pueda ayudarte a encontrarla.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('mascotas.create') }}"
                           class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-2xl transition shadow-sm">
                            Reportar mascota perdida
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($mascotas->hasPages())
            <div class="mt-8">
                {{ $mascotas->links() }}
            </div>
        @endif

    </div>
</div>
@endsection