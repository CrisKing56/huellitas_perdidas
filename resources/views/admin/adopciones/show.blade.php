@extends('admin.layout')

@section('title', 'Detalle de publicación de adopción')
@section('top_title', 'Detalle de publicación')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Moderación</p>
                <h1 class="text-4xl font-bold mb-2">{{ $publicacion->nombre }}</h1>
                <p class="text-slate-200 text-lg">Consulta la información completa de la publicación y sus solicitudes relacionadas.</p>

                <div class="flex flex-wrap gap-3 mt-5">
                    @if($publicacion->estado === 'DISPONIBLE')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            Publicación disponible
                        </span>
                    @elseif($publicacion->estado === 'EN_PROCESO')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            Publicación en proceso
                        </span>
                    @elseif($publicacion->estado === 'PAUSADA')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Publicación pausada
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                            Publicación adoptada
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.adopciones.index') }}"
                   class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition border border-white/15">
                    Volver
                </a>

                <a href="{{ route('adopciones.show', $publicacion->id_publicacion) }}" target="_blank"
                   class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition">
                    Ver publicación pública
                </a>

                @if(in_array($publicacion->estado, ['DISPONIBLE', 'EN_PROCESO']))
                    <form action="{{ route('admin.adopciones.pausar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas pausar esta publicación?');">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                            Pausar publicación
                        </button>
                    </form>
                @elseif($publicacion->estado === 'PAUSADA')
                    <form action="{{ route('admin.adopciones.reactivar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas reactivar esta publicación?');">
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
            <p class="text-sm text-gray-500 mb-2">Solicitudes</p>
            <p class="text-2xl font-bold text-gray-800">{{ $publicacion->total_solicitudes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Enviadas</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $publicacion->solicitudes_enviadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Aceptadas</p>
            <p class="text-2xl font-bold text-green-600">{{ $publicacion->solicitudes_aceptadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Rechazadas</p>
            <p class="text-2xl font-bold text-red-600">{{ $publicacion->solicitudes_rechazadas }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información de la publicación</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                    <div><span class="font-semibold">Nombre:</span> {{ $publicacion->nombre }}</div>
                    <div><span class="font-semibold">Especie:</span> {{ $publicacion->especie_nombre ?? 'No disponible' }}</div>
                    <div><span class="font-semibold">Raza:</span> {{ $publicacion->raza_nombre ?? ($publicacion->otra_raza ?: 'No especificada') }}</div>
                    <div><span class="font-semibold">Edad:</span> {{ $publicacion->edad_anios ?? '—' }} años</div>
                    <div><span class="font-semibold">Sexo:</span> {{ $publicacion->sexo }}</div>
                    <div><span class="font-semibold">Tamaño:</span> {{ $publicacion->tamano }}</div>
                    <div><span class="font-semibold">Color predominante:</span> {{ $publicacion->color_predominante ?: 'No especificado' }}</div>
                    <div><span class="font-semibold">Esterilizado:</span> {{ $publicacion->esterilizado ? 'Sí' : 'No' }}</div>
                    <div class="md:col-span-2"><span class="font-semibold">Vacunas aplicadas:</span> {{ $publicacion->vacunas_aplicadas ?: 'No especificadas' }}</div>
                    <div><span class="font-semibold">Condición de salud:</span> {{ $publicacion->condicion_salud ?: 'No especificada' }}</div>
                    <div class="md:col-span-2"><span class="font-semibold">Descripción de salud:</span> {{ $publicacion->descripcion_salud ?: 'No disponible' }}</div>
                    <div class="md:col-span-2"><span class="font-semibold">Requisitos:</span> {{ $publicacion->requisitos ?: 'No especificados' }}</div>
                    <div class="md:col-span-2"><span class="font-semibold">Descripción:</span> {{ $publicacion->descripcion }}</div>
                    <div><span class="font-semibold">Colonia / barrio:</span> {{ $publicacion->colonia_barrio ?: 'No disponible' }}</div>
                    <div><span class="font-semibold">Calle y referencias:</span> {{ $publicacion->calle_referencias ?: 'No disponibles' }}</div>
                    <div><span class="font-semibold">Latitud:</span> {{ $publicacion->latitud ?: '—' }}</div>
                    <div><span class="font-semibold">Longitud:</span> {{ $publicacion->longitud ?: '—' }}</div>
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
                    <h2 class="text-xl font-bold text-gray-800">Solicitudes de adopción</h2>
                    <span class="text-sm text-gray-500">{{ $solicitudes->count() }} registradas</span>
                </div>

                @if($solicitudes->isEmpty())
                    <p class="text-gray-500">No hay solicitudes registradas para esta publicación.</p>
                @else
                    <div class="space-y-4">
                        @foreach($solicitudes as $solicitud)
                            <div class="rounded-2xl border border-gray-100 p-4 bg-gray-50">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="space-y-2 flex-1">
                                        <p class="font-semibold text-gray-800">
                                            Solicitud #{{ $solicitud->id_solicitud }} — {{ $solicitud->nombre_completo }}
                                        </p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                                            <p><span class="font-semibold">Usuario:</span> {{ $solicitud->usuario_nombre ?: 'No disponible' }}</p>
                                            <p><span class="font-semibold">Correo:</span> {{ $solicitud->usuario_correo ?: 'No disponible' }}</p>
                                            <p><span class="font-semibold">Edad:</span> {{ $solicitud->edad }}</p>
                                            <p><span class="font-semibold">Estado civil:</span> {{ $solicitud->estado_civil }}</p>
                                            <p><span class="font-semibold">Tipo de vivienda:</span> {{ $solicitud->tipo_vivienda }}</p>
                                            <p><span class="font-semibold">Tiene patio:</span> {{ $solicitud->tiene_patio ? 'Sí' : 'No' }}</p>
                                            <p><span class="font-semibold">Todos de acuerdo:</span> {{ $solicitud->todos_de_acuerdo ? 'Sí' : 'No' }}</p>
                                            <p><span class="font-semibold">Fecha:</span> {{ $solicitud->created_at ? \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') : '—' }}</p>
                                            <p class="md:col-span-2"><span class="font-semibold">Motivo de adopción:</span> {{ $solicitud->motivo_adopcion }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 items-start md:items-end">
                                        @if($solicitud->estado === 'ENVIADA')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                Enviada
                                            </span>
                                        @elseif($solicitud->estado === 'ACEPTADA')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                Aceptada
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                Rechazada
                                            </span>
                                        @endif
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
                    <p><span class="font-semibold">Nombre:</span> {{ $publicacion->organizacion_nombre ?: ($publicacion->autor_nombre ?: 'No disponible') }}</p>
                    <p><span class="font-semibold">Tipo organización:</span> {{ $publicacion->organizacion_tipo ?: 'Publicación de usuario' }}</p>
                    <p><span class="font-semibold">Correo:</span> {{ $publicacion->autor_correo ?: 'No disponible' }}</p>
                    <p><span class="font-semibold">Teléfono:</span> {{ $publicacion->autor_telefono ?: 'No disponible' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Estado administrativo</h2>
                <div class="space-y-3 text-gray-700">
                    <p><span class="font-semibold">Estado:</span> {{ $publicacion->estado }}</p>
                    <p><span class="font-semibold">Publicada en:</span> {{ $publicacion->created_at ? \Carbon\Carbon::parse($publicacion->created_at)->format('d/m/Y H:i') : '—' }}</p>
                    <p><span class="font-semibold">Última actualización:</span> {{ $publicacion->updated_at ? \Carbon\Carbon::parse($publicacion->updated_at)->format('d/m/Y H:i') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection