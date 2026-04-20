@extends('admin.layout')

@section('title', 'Reportes de consejos - Panel Administrador')
@section('top_title', 'Reportes de consejos')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <h1 class="text-4xl font-bold mb-2">Reportes de consejos</h1>
        <p class="text-slate-200 text-lg">
            Revisa los reportes enviados por los usuarios sobre consejos publicados en la plataforma.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Abiertos</p>
            <p class="text-3xl font-bold text-red-500">{{ $stats['abiertos'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">En revisión</p>
            <p class="text-3xl font-bold text-yellow-500">{{ $stats['en_revision'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Resueltos</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['resueltos'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Descartados</p>
            <p class="text-3xl font-bold text-gray-700">{{ $stats['descartados'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.reportes-consejos.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $filtros['q'] }}"
                    placeholder="Motivo, consejo o usuario..."
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
                    <option value="ABIERTO" {{ $filtros['estado'] === 'ABIERTO' ? 'selected' : '' }}>Abierto</option>
                    <option value="EN_REVISION" {{ $filtros['estado'] === 'EN_REVISION' ? 'selected' : '' }}>En revisión</option>
                    <option value="RESUELTO" {{ $filtros['estado'] === 'RESUELTO' ? 'selected' : '' }}>Resuelto</option>
                    <option value="DESCARTADO" {{ $filtros['estado'] === 'DESCARTADO' ? 'selected' : '' }}>Descartado</option>
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
                    href="{{ route('admin.reportes-consejos.index') }}"
                    class="px-5 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition w-full text-center"
                >
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Listado de reportes</h2>
            <p class="text-sm text-gray-500 mt-1">
                Desde aquí puedes revisar cada caso y darle seguimiento.
            </p>
        </div>

        @if($reportes->isEmpty())
            <div class="p-6 text-gray-500">
                No hay reportes de consejos registrados todavía.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-4 font-semibold">Reporte</th>
                            <th class="px-6 py-4 font-semibold">Consejo</th>
                            <th class="px-6 py-4 font-semibold">Usuario</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($reportes as $reporte)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-5 align-top">
                                    <div class="font-bold text-gray-800 text-base">{{ $reporte->motivo }}</div>
                                    <div class="text-gray-500 text-sm mt-1">ID reporte: {{ $reporte->id_reporte }}</div>
                                    @if($reporte->descripcion)
                                        <div class="text-gray-600 text-sm mt-2 line-clamp-2">
                                            {{ $reporte->descripcion }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="font-semibold text-gray-800">{{ $reporte->consejo->titulo ?? 'Consejo eliminado' }}</div>
                                    <div class="text-gray-500 text-sm mt-1">
                                        {{ $reporte->consejo->organizacion->nombre ?? 'Sin organización' }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    <div>{{ $reporte->usuarioReporta->nombre ?? 'Usuario no disponible' }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $reporte->usuarioReporta->correo ?? 'Sin correo' }}</div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($reporte->estado === 'ABIERTO')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            ABIERTO
                                        </span>
                                    @elseif($reporte->estado === 'EN_REVISION')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            EN REVISIÓN
                                        </span>
                                    @elseif($reporte->estado === 'RESUELTO')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            RESUELTO
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                                            DESCARTADO
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    <div>{{ \Carbon\Carbon::parse($reporte->creado_en)->translatedFormat('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($reporte->creado_en)->format('H:i') }}</div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col md:flex-row gap-2 justify-center">
                                        <a
                                            href="{{ route('admin.reportes-consejos.show', $reporte->id_reporte) }}"
                                            class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition text-center">
                                            Ver detalle
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $reportes->links() }}
        </div>
    </div>
</div>
@endsection