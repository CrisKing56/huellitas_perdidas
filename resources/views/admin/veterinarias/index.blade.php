@extends('admin.layout')

@section('title', 'Veterinarias - Panel Administrador')
@section('top_title', 'Gestión de veterinarias')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <h1 class="text-4xl font-bold mb-2">Gestión de veterinarias</h1>
        <p class="text-slate-200 text-lg">Revisa solicitudes, aprueba registros y administra cuentas activas o suspendidas.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Veterinarias aprobadas</p>
            <p class="text-3xl font-bold text-green-600">{{ $activas->count() }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Solicitudes pendientes</p>
            <p class="text-3xl font-bold text-yellow-500">{{ $pendientes->count() }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Solicitudes rechazadas</p>
            <p class="text-3xl font-bold text-red-500">{{ $rechazadas->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-2">
        <form method="GET" action="{{ route('admin.veterinarias.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Nombre, correo, o ubicación..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Estado de Revisión</label>
                <select name="estado_revision" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Todos</option>
                    <option value="APROBADA" {{ request('estado_revision') == 'APROBADA' ? 'selected' : '' }}>Aprobada</option>
                    <option value="PENDIENTE" {{ request('estado_revision') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                    <option value="RECHAZADA" {{ request('estado_revision') == 'RECHAZADA' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-2 text-transparent select-none hidden md:block">&nbsp;</label>
                
                <div class="flex flex-col gap-3">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="submit" class="w-full h-12 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition flex items-center justify-center">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.veterinarias.index') }}" class="w-full h-12 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition flex items-center justify-center">
                            Limpiar
                        </a>
                    </div>
                    
                    <button 
                    type="submit" 
                    formmethod="GET" 
                    formaction="{{ route('reportes.usuarios.pdf') }}" 
                    class="w-full h-12 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition flex items-center justify-center gap-2"
                    >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Exportar PDF
                    </button>
                </div>
            </div>

        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Listado de veterinarias</h2>
            <p class="text-sm text-gray-500 mt-1">Para rechazar una solicitud, entra al detalle y escribe el motivo.</p>
        </div>

        @if($veterinarias->isEmpty())
            <div class="p-6 text-gray-500">
                No hay veterinarias registradas todavía.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-4 font-semibold">Veterinaria</th>
                            <th class="px-6 py-4 font-semibold">Contacto</th>
                            <th class="px-6 py-4 font-semibold">Ubicación</th>
                            <th class="px-6 py-4 font-semibold">Revisión</th>
                            <th class="px-6 py-4 font-semibold">Cuenta</th>
                            <th class="px-6 py-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($veterinarias as $vet)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-5 align-top">
                                    <div class="font-bold text-gray-800 text-base">{{ $vet->nombre }}</div>
                                    <div class="text-gray-500 text-sm mt-1">ID organización: {{ $vet->id_organizacion }}</div>
                                    <div class="text-gray-600 text-sm mt-2 line-clamp-2">
                                        {{ $vet->descripcion }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="text-gray-800 font-medium">{{ $vet->nombre_usuario }}</div>
                                    <div class="text-gray-600 mt-1">{{ $vet->correo }}</div>
                                    <div class="text-gray-600 mt-1">{{ $vet->telefono }}</div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    <div>{{ $vet->calle_numero }}</div>
                                    <div>{{ $vet->colonia }}</div>
                                    <div>{{ $vet->ciudad }}, {{ $vet->estado_direccion }}</div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($vet->estado_revision === 'APROBADA')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            APROBADA
                                        </span>
                                    @elseif($vet->estado_revision === 'PENDIENTE')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            PENDIENTE
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            RECHAZADA
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($vet->estado_revision === 'APROBADA')
                                        @if($vet->estado_usuario === 'SUSPENDIDA')
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                SUSPENDIDA
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                ACTIVA
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            N/A
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col md:flex-row gap-2 justify-center">
                                        <a href="{{ route('admin.veterinarias.show', $vet->id_organizacion) }}"
                                           class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition text-center">
                                            Ver detalle
                                        </a>

                                        @if($vet->estado_revision !== 'APROBADA')
                                            <form action="{{ route('admin.veterinarias.aprobar', $vet->id_organizacion) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold transition">
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
    </div>
</div>
@endsection