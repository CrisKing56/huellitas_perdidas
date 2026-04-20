@extends('admin.layout')

@section('title', 'Consejos - Panel Administrador')
@section('top_title', 'Revisión de consejos')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <h1 class="text-4xl font-bold mb-2">Revisión de consejos</h1>
        <p class="text-slate-200 text-lg">
            Administra las publicaciones enviadas por veterinarias y refugios, aprueba contenido útil y rechaza lo que no cumpla con las normas.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Consejos pendientes</p>
            <p class="text-3xl font-bold text-yellow-500">{{ $stats['pendientes'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Consejos aprobados</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['aprobados'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Consejos rechazados</p>
            <p class="text-3xl font-bold text-red-500">{{ $stats['rechazados'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Total registrados</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.consejos.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $filtros['q'] }}"
                    placeholder="Título u organización..."
                    class="w-full rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-50"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Estado</label>
                <select
                    name="estado"
                    class="w-full rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-50"
                >
                    <option value="">Todos</option>
                    <option value="PENDIENTE" {{ $filtros['estado'] === 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                    <option value="APROBADO" {{ $filtros['estado'] === 'APROBADO' ? 'selected' : '' }}>Aprobado</option>
                    <option value="RECHAZADO" {{ $filtros['estado'] === 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition w-full"
                >
                    Filtrar
                </button>

                <a
                    href="{{ route('admin.consejos.index') }}"
                    class="px-5 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition w-full text-center"
                >
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Listado de consejos</h2>
            <p class="text-sm text-gray-500 mt-1">
                Puedes revisar el detalle, aprobar directamente o rechazar con motivo desde aquí.
            </p>
        </div>

        @if($consejos->isEmpty())
            <div class="p-6 text-gray-500">
                No hay consejos registrados para mostrar.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-4 font-semibold">Consejo</th>
                            <th class="px-6 py-4 font-semibold">Organización</th>
                            <th class="px-6 py-4 font-semibold">Clasificación</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($consejos as $consejo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-5 align-top">
                                    <div class="flex gap-4">
                                        <div class="w-24 h-20 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                                            @if($consejo->imagenes->count())
                                                <img
                                                    src="{{ asset('storage/' . $consejo->imagenes->first()->url) }}"
                                                    alt="{{ $consejo->titulo }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                    Sin imagen
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="font-bold text-gray-800 text-base">{{ $consejo->titulo }}</div>
                                            <div class="text-gray-500 text-sm mt-1">ID consejo: {{ $consejo->id_consejo }}</div>
                                            <div class="text-gray-600 text-sm mt-2 line-clamp-2">
                                                {{ $consejo->resumen }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="font-semibold text-gray-800">{{ $consejo->organizacion->nombre ?? 'Sin organización' }}</div>
                                    <div class="text-gray-600 mt-1">{{ $consejo->organizacion->tipo ?? 'N/D' }}</div>
                                    <div class="text-gray-500 mt-1 text-sm">
                                        {{ $consejo->organizacion->usuarioDueno->correo ?? $consejo->organizacion->usuarioDueno->email ?? 'Sin correo' }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    <div><span class="font-medium text-gray-800">Categoría:</span> {{ $consejo->categoria->nombre ?? 'General' }}</div>
                                    <div class="mt-1"><span class="font-medium text-gray-800">Especie:</span> {{ $consejo->especie->nombre ?? 'No especificada' }}</div>

                                    @if($consejo->etiquetas->count())
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach($consejo->etiquetas->take(3) as $etiqueta)
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                                    {{ $etiqueta->nombre }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($consejo->estado_publicacion === 'APROBADO')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            APROBADO
                                        </span>
                                    @elseif($consejo->estado_publicacion === 'PENDIENTE')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            PENDIENTE
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            RECHAZADO
                                        </span>
                                    @endif

                                    @if($consejo->motivo_rechazo)
                                        <div class="mt-3 text-xs text-red-600 max-w-xs">
                                            <span class="font-semibold">Motivo:</span> {{ $consejo->motivo_rechazo }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    <div>{{ \Carbon\Carbon::parse($consejo->creado_en)->translatedFormat('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($consejo->creado_en)->format('H:i') }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col md:flex-row gap-2 justify-center">
                                        <a
                                            href="{{ route('admin.consejos.show', $consejo->id_consejo) }}"
                                            class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition text-center"
                                        >
                                            Ver detalle
                                        </a>

                                        @if($consejo->estado_publicacion !== 'APROBADO')
                                            <form action="{{ route('admin.consejos.aprobar', $consejo->id_consejo) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold transition"
                                                >
                                                    Aprobar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $consejos->links() }}
        </div>
    </div>
</div>
@endsection