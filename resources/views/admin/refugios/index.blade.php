@extends('admin.layout')

@section('title', 'Refugios - Panel Administrador')
@section('top_title', 'Gestión de refugios')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Gestión de refugios</h1>
        <p class="text-gray-500 text-lg">Administra refugios activos y solicitudes pendientes.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Refugios aprobados</p>
            <p class="text-3xl font-bold text-green-600">{{ $activos->count() }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Solicitudes pendientes</p>
            <p class="text-3xl font-bold text-yellow-500">{{ $pendientes->count() }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Solicitudes rechazadas</p>
            <p class="text-3xl font-bold text-red-500">{{ $rechazados->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Listado de refugios</h2>
        </div>

        @if($refugios->isEmpty())
            <div class="p-6 text-gray-500">
                No hay refugios registrados todavía.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-4 font-semibold">Refugio</th>
                            <th class="px-6 py-4 font-semibold">Contacto</th>
                            <th class="px-6 py-4 font-semibold">Ubicación</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($refugios as $ref)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-5 align-top">
                                    <div class="font-bold text-gray-800 text-base">{{ $ref->nombre }}</div>
                                    <div class="text-gray-500 text-sm mt-1">ID organización: {{ $ref->id_organizacion }}</div>
                                    <div class="text-gray-600 text-sm mt-2">
                                        {{ $ref->descripcion }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="text-gray-800 font-medium">{{ $ref->nombre_usuario }}</div>
                                    <div class="text-gray-600 mt-1">{{ $ref->correo }}</div>
                                    <div class="text-gray-600 mt-1">{{ $ref->telefono }}</div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    <div>{{ $ref->calle_numero }}</div>
                                    <div>{{ $ref->colonia }}</div>
                                    <div>{{ $ref->ciudad }}, {{ $ref->estado_direccion }}</div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($ref->estado_revision === 'APROBADA')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            APROBADA
                                        </span>
                                    @elseif($ref->estado_revision === 'PENDIENTE')
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
                                    <div class="flex flex-col md:flex-row gap-2 justify-center">
                                        <a href="{{ route('admin.refugios.show', $ref->id_organizacion) }}"
                                           class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition text-center">
                                            Ver detalle
                                        </a>

                                        @if($ref->estado_revision !== 'APROBADA')
                                            <form action="{{ route('admin.refugios.aprobar', $ref->id_organizacion) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold transition">
                                                    Aprobar
                                                </button>
                                            </form>
                                        @endif

                                        @if($ref->estado_revision !== 'RECHAZADA')
                                            <form action="{{ route('admin.refugios.rechazar', $ref->id_organizacion) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                                                    Rechazar
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