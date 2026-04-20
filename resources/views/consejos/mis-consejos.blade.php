@extends('layout.app')

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        <div class="mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-wide uppercase border {{ $organizacion->tipo === 'REFUGIO' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-orange-50 text-orange-700 border-orange-100' }}">
                            Panel institucional
                        </span>

                        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                            Mis consejos
                        </h1>

                        <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                            Gestiona los consejos publicados por <span class="font-semibold text-gray-800">{{ $organizacion->nombre }}</span>.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('consejos.create') }}"
                           class="inline-flex items-center justify-center px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-2xl shadow-sm transition">
                            Publicar consejo
                        </a>

                        <a href="{{ $organizacion->tipo === 'REFUGIO' ? route('refugio.dashboard') : route('veterinaria.dashboard') }}"
                           class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 font-medium rounded-2xl hover:bg-gray-200 transition">
                            Volver al panel
                        </a>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <p class="text-sm text-gray-500">Pendientes</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-2">{{ $stats['pendientes'] }}</p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <p class="text-sm text-gray-500">Aprobados</p>
                <p class="text-3xl font-extrabold text-green-600 mt-2">{{ $stats['aprobados'] }}</p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                <p class="text-sm text-gray-500">Rechazados</p>
                <p class="text-3xl font-extrabold text-red-600 mt-2">{{ $stats['rechazados'] }}</p>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($consejos as $consejo)
                @php
                    $clasesEstado = [
                        'PENDIENTE' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'APROBADO' => 'bg-green-100 text-green-700 border-green-200',
                        'RECHAZADO' => 'bg-red-100 text-red-700 border-red-200',
                    ];

                    $claseEstado = $clasesEstado[$consejo->estado_publicacion] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp

                <article class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <div class="w-full lg:w-44 shrink-0">
                                <div class="w-full h-52 lg:h-44 rounded-2xl overflow-hidden border border-gray-100 bg-gray-100">
                                    @if($consejo->imagenes->count())
                                        <img src="{{ asset('storage/' . $consejo->imagenes->first()->url) }}"
                                             class="w-full h-full object-cover"
                                             alt="{{ $consejo->titulo }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <span class="text-sm">Sin imagen</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-3 mb-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide border {{ $claseEstado }}">
                                                {{ $consejo->estado_publicacion }}
                                            </span>

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide border bg-orange-50 text-orange-600 border-orange-100">
                                                {{ $consejo->categoria?->nombre ?? 'General' }}
                                            </span>

                                            <span class="text-sm text-gray-400">
                                                {{ \Carbon\Carbon::parse($consejo->creado_en)->locale('es')->diffForHumans() }}
                                            </span>
                                        </div>

                                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">
                                            {{ $consejo->titulo }}
                                        </h2>

                                        <p class="mt-3 text-sm text-gray-600 leading-relaxed max-w-3xl">
                                            {{ $consejo->resumen }}
                                        </p>

                                        @if($consejo->estado_publicacion === 'RECHAZADO' && $consejo->motivo_rechazo)
                                            <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-4">
                                                <p class="text-xs uppercase tracking-wide text-red-500 font-semibold mb-1">Motivo de rechazo</p>
                                                <p class="text-sm text-red-700">{{ $consejo->motivo_rechazo }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="shrink-0">
                                        <a href="{{ route('consejos.show', $consejo->id_consejo) }}"
                                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 font-semibold text-sm hover:bg-orange-100 transition">
                                            Ver detalle
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="{{ route('consejos.edit', $consejo->id_consejo) }}"
                                       class="inline-flex items-center gap-2 text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                                        Editar
                                    </a>

                                    <form action="{{ route('consejos.destroy', $consejo->id_consejo) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar este consejo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
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
                    <h3 class="text-lg font-bold text-gray-900">Aún no tienes consejos</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        Cuando publiques un consejo desde tu panel institucional, aparecerá aquí.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('consejos.create') }}"
                           class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-2xl transition shadow-sm">
                            Publicar consejo
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($consejos->hasPages())
            <div class="mt-8">
                {{ $consejos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection