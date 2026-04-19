@extends('admin.layout')

@section('title', 'Reportes de publicaciones')
@section('top_title', 'Reportes de publicaciones')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Moderación</p>
                <h1 class="text-4xl font-bold mb-2">Reportes de publicaciones</h1>
                <p class="text-slate-200 text-lg">Revisa, analiza y resuelve los reportes enviados por usuarios sobre publicaciones de extravío.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Enviados</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['enviados'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">En revisión</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['revision'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Resueltos</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['resueltos'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Desestimados</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['desestimados'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.reportes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Mascota, reportante, dueño, motivo o ID..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Estado</label>
                <select name="estado" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Todos</option>
                    <option value="ENVIADO" {{ $estado === 'ENVIADO' ? 'selected' : '' }}>Enviado</option>
                    <option value="EN_REVISION" {{ $estado === 'EN_REVISION' ? 'selected' : '' }}>En revisión</option>
                    <option value="RESUELTO" {{ $estado === 'RESUELTO' ? 'selected' : '' }}>Resuelto</option>
                    <option value="DESESTIMADO" {{ $estado === 'DESESTIMADO' ? 'selected' : '' }}>Desestimado</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit" class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 transition">
                    Filtrar
                </button>
                <a href="{{ route('admin.reportes.index') }}" class="w-full text-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-left text-gray-600">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Mascota</th>
                        <th class="px-6 py-4 font-semibold">Motivo</th>
                        <th class="px-6 py-4 font-semibold">Reportante</th>
                        <th class="px-6 py-4 font-semibold">Dueño</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reportes as $reporte)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-800">#{{ $reporte->id_reporte }}</td>

                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $reporte->mascota_nombre ?? 'Publicación no disponible' }}</div>
                                <div class="text-xs text-gray-500">{{ $reporte->colonia_barrio ?? 'Sin zona' }}</div>
                            </td>

                            <td class="px-6 py-4 text-gray-700">{{ $reporte->motivo_nombre ?? 'Sin motivo' }}</td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $reporte->reportante_nombre ?? 'No disponible' }}</div>
                                <div class="text-xs text-gray-500 break-all">{{ $reporte->reportante_correo ?? 'Sin correo' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $reporte->dueno_nombre ?? 'No disponible' }}</div>
                                <div class="text-xs text-gray-500 break-all">{{ $reporte->dueno_correo ?? 'Sin correo' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                @if($reporte->estado === 'ENVIADO')
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Enviado
                                    </span>
                                @elseif($reporte->estado === 'EN_REVISION')
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        En revisión
                                    </span>
                                @elseif($reporte->estado === 'RESUELTO')
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Resuelto
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Desestimado
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $reporte->creado_en ? \Carbon\Carbon::parse($reporte->creado_en)->format('d/m/Y H:i') : '—' }}
                            </td>

                            <td class="px-6 py-4">
                                <a href="{{ route('admin.reportes.show', $reporte->id_reporte) }}"
                                   class="inline-flex items-center px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold transition">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No hay reportes para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $reportes->links() }}
        </div>
    </div>
</div>
@endsection