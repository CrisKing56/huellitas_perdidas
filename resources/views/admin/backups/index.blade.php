@extends('admin.layout')

@section('title', 'Respaldos de Base de Datos')
@section('top_title', 'Panel Administrador')
@section('panel_title', 'Panel Administrador')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-500 mb-2">
                Seguridad y mantenimiento
            </p>
            <h1 class="text-3xl font-bold text-gray-900">Respaldos de la base de datos</h1>
            <p class="text-sm text-gray-500 mt-2 max-w-2xl">
                Genera respaldos manuales del sistema y administra los archivos guardados. También puedes dejar configurado el respaldo automático diario.
            </p>
        </div>

        <form action="{{ route('admin.backups.store') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-4-4m4 4l4-4"></path>
                </svg>
                Generar respaldo ahora
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Modo manual</p>
            <p class="text-lg font-bold text-gray-900 mt-2">Disponible desde panel</p>
            <p class="text-sm text-gray-500 mt-1">Puedes generar un respaldo cuando lo necesites.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Modo automático</p>
            <p class="text-lg font-bold text-gray-900 mt-2">Programable diario</p>
            <p class="text-sm text-gray-500 mt-1">Ideal para ejecutar una copia cada madrugada.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Retención</p>
            <p class="text-lg font-bold text-gray-900 mt-2">15 archivos</p>
            <p class="text-sm text-gray-500 mt-1">Los respaldos más antiguos se eliminan automáticamente.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Historial de respaldos</h2>
            <p class="text-sm text-gray-500 mt-1">Descarga o elimina archivos generados previamente.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Archivo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Tamaño</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5">
                                <p class="font-semibold text-gray-900">{{ $backup['name'] }}</p>
                            </td>

                            <td class="px-6 py-5 text-sm text-gray-600">
                                {{ $backup['size_human'] }}
                            </td>

                            <td class="px-6 py-5 text-sm text-gray-600">
                                {{ $backup['last_modified_human'] }}
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.backups.download', $backup['name']) }}"
                                       class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 transition text-sm font-semibold">
                                        Descargar
                                    </a>

                                    <form action="{{ route('admin.backups.destroy', $backup['name']) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este respaldo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition text-sm font-semibold">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Aún no hay respaldos generados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5">
        <h3 class="text-lg font-bold text-orange-900">Configuración automática</h3>
        <p class="text-sm text-orange-800 mt-2">
            Una vez agregado el comando programado en Laravel Scheduler y configurado el cron del servidor,
            el sistema podrá generar respaldos automáticamente todos los días.
        </p>
    </div>
</div>
@endsection