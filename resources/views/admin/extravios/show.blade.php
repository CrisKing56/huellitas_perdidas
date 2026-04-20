@extends('admin.layout')

@section('title', 'Detalle de publicación de extravío')
@section('top_title', 'Detalle de publicación')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Moderación</p>
                <h1 class="text-4xl font-bold mb-2">{{ $publicacion->nombre }}</h1>
                <p class="text-slate-200 text-lg">Consulta la información completa de la publicación y su actividad relacionada.</p>

                <div class="flex flex-wrap gap-3 mt-5">
                    @if($publicacion->estado === 'ACTIVA')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            Publicación activa
                        </span>
                    @elseif($publicacion->estado === 'RESUELTA')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            Publicación resuelta
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Publicación oculta
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.extravios.index') }}"
                   class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition border border-white/15">
                    Volver
                </a>

                <a href="{{ route('extravios.show', $publicacion->id_publicacion) }}" target="_blank"
                   class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition">
                    Ver publicación pública
                </a>

                @if($publicacion->estado === 'ACTIVA')
                    <form action="{{ route('admin.extravios.ocultar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas ocultar esta publicación?');">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                            Ocultar publicación
                        </button>
                    </form>
                @elseif($publicacion->estado === 'ELIMINADA')
                    <form action="{{ route('admin.extravios.reactivar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas reactivar esta publicación?');">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-semibold transition">
                            Reactivar publicación
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Comentarios</p>
            <p class="text-2xl font-bold text-gray-800">{{ $publicacion->total_comentarios }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Avistamientos</p>
            <p class="text-2xl font-bold text-blue-600">{{ $publicacion->total_avistamientos }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Reportes</p>
            <p class="text-2xl font-bold text-red-600">{{ $publicacion->total_reportes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Fecha de publicación</p>
            <p class="text-lg font-bold text-gray-800">
                {{ $publicacion->created_at ? \Carbon\Carbon::parse($publicacion->created_at)->format('d/m/Y H:i') : '—' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información de la publicación</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                    <div><span class="font-semibold">Nombre:</span> {{ $publicacion->nombre }}</div>
                    <div><span class="font-semibold">Especie:</span> {{ $publicacion->especie_nombre ?? 'No disponible' }}</div>
                    <div><span class="font-semibold">Raza:</span> {{ $publicacion->raza_nombre ?? 'No especificada' }}</div>
                    <div><span class="font-semibold">Color:</span> {{ $publicacion->color }}</div>
                    <div><span class="font-semibold">Tamaño:</span> {{ $publicacion->tamano }}</div>
                    <div><span class="font-semibold">Sexo:</span> {{ $publicacion->sexo }}</div>
                    <div><span class="font-semibold">Fecha de extravío:</span> {{ $publicacion->fecha_extravio ? \Carbon\Carbon::parse($publicacion->fecha_extravio)->format('d/m/Y') : '—' }}</div>
                    <div><span class="font-semibold">Colonia / barrio:</span> {{ $publicacion->colonia_barrio }}</div>
                    <div class="md:col-span-2"><span class="font-semibold">Calle y referencias:</span> {{ $publicacion->calle_referencias ?: 'No disponibles' }}</div>
                    <div class="md:col-span-2"><span class="font-semibold">Descripción:</span> {{ $publicacion->descripcion }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Galería de fotos</h2>

                @if($fotos->isEmpty())
                    <p class="text-gray-500">No hay fotos registradas.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($fotos as $foto)
                            <div class="border border-gray-200 rounded-2xl overflow-hidden bg-gray-50 shadow-sm">
                                <img
                                    src="{{ asset('storage/' . $foto->url) }}"
                                    alt="Foto de la publicación"
                                    class="w-full h-56 object-cover"
                                >
                                <div class="p-3 text-sm text-gray-600">
                                    Foto {{ $foto->orden }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Comentarios</h2>
                    <span class="text-sm text-gray-500">{{ $comentarios->count() }} registrados</span>
                </div>

                @if($comentarios->isEmpty())
                    <p class="text-gray-500">No hay comentarios registrados.</p>
                @else
                    <div class="space-y-4">
                        @foreach($comentarios as $comentario)
                            <div class="rounded-2xl border border-gray-100 p-4 bg-gray-50">
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $comentario->autor_nombre ?? 'Usuario no disponible' }}</p>
                                        <p class="text-xs text-gray-500">{{ $comentario->autor_correo ?? 'Sin correo' }}</p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($comentario->estado === 'VISIBLE')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Visible</span>
                                        @elseif($comentario->estado === 'OCULTO')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Oculto</span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Eliminado</span>
                                        @endif

                                        <span class="text-xs text-gray-500">
                                            {{ $comentario->creado_en ? \Carbon\Carbon::parse($comentario->creado_en)->format('d/m/Y H:i') : '—' }}
                                        </span>
                                    </div>
                                </div>

                                <p class="text-gray-700">{{ $comentario->comentario }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Avistamientos</h2>
                    <span class="text-sm text-gray-500">{{ $avistamientos->count() }} registrados</span>
                </div>

                @if($avistamientos->isEmpty())
                    <p class="text-gray-500">No hay avistamientos registrados.</p>
                @else
                    <div class="space-y-4">
                        @foreach($avistamientos as $avistamiento)
                            <div class="rounded-2xl border border-gray-100 p-4 bg-gray-50">
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $avistamiento->reportante_nombre ?: ($avistamiento->nombre_contacto ?: 'Sin nombre disponible') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $avistamiento->reportante_correo ?: ($avistamiento->telefono_contacto ?: 'Sin contacto') }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ $avistamiento->estado }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $avistamiento->creado_en ? \Carbon\Carbon::parse($avistamiento->creado_en)->format('d/m/Y H:i') : '—' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
                                    <div><span class="font-semibold">Fecha de avistamiento:</span> {{ $avistamiento->fecha_avistamiento ? \Carbon\Carbon::parse($avistamiento->fecha_avistamiento)->format('d/m/Y') : '—' }}</div>
                                    <div><span class="font-semibold">Colonia / barrio:</span> {{ $avistamiento->colonia_barrio ?: 'No disponible' }}</div>
                                    <div class="md:col-span-2"><span class="font-semibold">Calle y referencias:</span> {{ $avistamiento->calle_referencias ?: 'No disponibles' }}</div>
                                    <div class="md:col-span-2"><span class="font-semibold">Descripción:</span> {{ $avistamiento->descripcion }}</div>
                                </div>

                                @if($avistamiento->foto_url)
                                    <div class="mt-4">
                                        <img src="{{ asset('storage/' . $avistamiento->foto_url) }}"
                                             alt="Foto del avistamiento"
                                             class="w-full max-w-sm rounded-2xl border border-gray-200 object-cover">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Reportes recibidos</h2>
                    <span class="text-sm text-gray-500">{{ $reportes->count() }} registrados</span>
                </div>

                @if($reportes->isEmpty())
                    <p class="text-gray-500">No hay reportes registrados para esta publicación.</p>
                @else
                    <div class="space-y-4">
                        @foreach($reportes as $reporte)
                            <div class="rounded-2xl border border-gray-100 p-4 bg-gray-50">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="space-y-2">
                                        <p class="font-semibold text-gray-800">
                                            Reporte #{{ $reporte->id_reporte }} — {{ $reporte->motivo_nombre ?? 'Sin motivo' }}
                                        </p>
                                        <p class="text-sm text-gray-700">
                                            <span class="font-semibold">Reportante:</span> {{ $reporte->reportante_nombre ?? 'No disponible' }}
                                        </p>
                                        <p class="text-sm text-gray-700">
                                            <span class="font-semibold">Descripción:</span> {{ $reporte->descripcion_adicional ?: 'Sin descripción adicional' }}
                                        </p>
                                        @if($reporte->nota_resolucion)
                                            <p class="text-sm text-gray-700">
                                                <span class="font-semibold">Nota de resolución:</span> {{ $reporte->nota_resolucion }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-2 items-start md:items-end">
                                        @if($reporte->estado === 'ENVIADO')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Enviado</span>
                                        @elseif($reporte->estado === 'EN_REVISION')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">En revisión</span>
                                        @elseif($reporte->estado === 'RESUELTO')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Resuelto</span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Desestimado</span>
                                        @endif

                                        <a href="{{ route('admin.reportes.show', $reporte->id_reporte) }}"
                                           class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold transition">
                                            Ver reporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Autor de la publicación</h2>
                <div class="space-y-3 text-gray-700">
                    <p><span class="font-semibold">Nombre:</span> {{ $publicacion->autor_nombre ?? 'No disponible' }}</p>
                    <p><span class="font-semibold">Correo:</span> {{ $publicacion->autor_correo ?? 'No disponible' }}</p>
                    <p><span class="font-semibold">Teléfono:</span> {{ $publicacion->autor_telefono ?? 'No disponible' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Estado administrativo</h2>
                <div class="space-y-3 text-gray-700">
                    <p><span class="font-semibold">Estado:</span> {{ $publicacion->estado }}</p>
                    <p><span class="font-semibold">Publicada en:</span> {{ $publicacion->created_at ? \Carbon\Carbon::parse($publicacion->created_at)->format('d/m/Y H:i') : '—' }}</p>
                    <p><span class="font-semibold">Última actualización:</span> {{ $publicacion->updated_at ? \Carbon\Carbon::parse($publicacion->updated_at)->format('d/m/Y H:i') : '—' }}</p>
                    <p><span class="font-semibold">Resuelta en:</span> {{ $publicacion->resuelta_en ? \Carbon\Carbon::parse($publicacion->resuelta_en)->format('d/m/Y H:i') : '—' }}</p>
                    <p><span class="font-semibold">Oculta en:</span> {{ $publicacion->eliminado_en ? \Carbon\Carbon::parse($publicacion->eliminado_en)->format('d/m/Y H:i') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection