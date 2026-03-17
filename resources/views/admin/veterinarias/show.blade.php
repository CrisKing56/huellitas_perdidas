@extends('admin.layout')

@section('title', 'Detalle de veterinaria')
@section('top_title', 'Detalle de veterinaria')

@section('content')
@php
    $dias = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];
@endphp

<div class="space-y-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $veterinaria->nombre }}</h1>
            <p class="text-gray-500 text-lg">Información completa de la veterinaria registrada.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.veterinarias.index') }}"
               class="px-5 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition">
                Volver
            </a>

            @if($veterinaria->estado_revision !== 'APROBADA')
                <form action="{{ route('admin.veterinarias.aprobar', $veterinaria->id_organizacion) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-3 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold transition">
                        Aprobar
                    </button>
                </form>
            @endif

            @if($veterinaria->estado_revision !== 'RECHAZADA')
                <form action="{{ route('admin.veterinarias.rechazar', $veterinaria->id_organizacion) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                        Rechazar
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div>
        @if($veterinaria->estado_revision === 'APROBADA')
            <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                APROBADA
            </span>
        @elseif($veterinaria->estado_revision === 'PENDIENTE')
            <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                PENDIENTE
            </span>
        @else
            <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                RECHAZADA
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Información general</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Nombre:</span> {{ $veterinaria->nombre }}</p>
                <p><span class="font-semibold">Descripción:</span> {{ $veterinaria->descripcion }}</p>
                <p><span class="font-semibold">Teléfono de la veterinaria:</span> {{ $veterinaria->telefono }}</p>
                <p><span class="font-semibold">Estado de revisión:</span> {{ $veterinaria->estado_revision }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Cuenta asociada</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Nombre de cuenta:</span> {{ $veterinaria->nombre_usuario }}</p>
                <p><span class="font-semibold">Correo:</span> {{ $veterinaria->correo }}</p>
                <p><span class="font-semibold">Teléfono usuario:</span> {{ $veterinaria->telefono_usuario }}</p>
                <p><span class="font-semibold">WhatsApp:</span> {{ $veterinaria->whatsapp ?? 'No registrado' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Dirección</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Calle y número:</span> {{ $veterinaria->calle_numero }}</p>
                <p><span class="font-semibold">Colonia:</span> {{ $veterinaria->colonia }}</p>
                <p><span class="font-semibold">Código postal:</span> {{ $veterinaria->codigo_postal }}</p>
                <p><span class="font-semibold">Ciudad:</span> {{ $veterinaria->ciudad }}</p>
                <p><span class="font-semibold">Estado:</span> {{ $veterinaria->estado_direccion }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Detalle veterinario</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Médico responsable:</span> {{ $veterinaria->medico_responsable }}</p>
                <p><span class="font-semibold">Cédula profesional:</span> {{ $veterinaria->cedula_profesional }}</p>
                <p><span class="font-semibold">Número de veterinarios:</span> {{ $veterinaria->num_veterinarios ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Otros servicios:</span> {{ $veterinaria->otros_servicios ?? 'No registrado' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Ubicación</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Latitud:</span> {{ $veterinaria->latitud ?? 'No registrada' }}</p>
                <p><span class="font-semibold">Longitud:</span> {{ $veterinaria->longitud ?? 'No registrada' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Horarios de atención</h2>

            @if($horarios->isEmpty())
                <p class="text-gray-600">No hay horarios registrados.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($horarios as $horario)
                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                            <p class="font-semibold text-gray-800">{{ $dias[$horario->dia_semana] ?? 'Día' }}</p>

                            @if($horario->cerrado)
                                <p class="text-red-500 mt-1">Cerrado</p>
                            @else
                                <p class="text-gray-600 mt-1">
                                    {{ \Carbon\Carbon::parse($horario->hora_apertura)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($horario->hora_cierre)->format('H:i') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Servicios registrados</h2>

            @if($servicios->isEmpty())
                <p class="text-gray-600">No hay servicios registrados.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($servicios as $servicio)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-700">
                            {{ $servicio }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Costos registrados</h2>

            @if($costos->isEmpty())
                <p class="text-gray-600">No hay costos registrados.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-600 border-b border-gray-200">
                                <th class="py-3 pr-4 font-semibold">Servicio</th>
                                <th class="py-3 pr-4 font-semibold">Precio</th>
                                <th class="py-3 pr-4 font-semibold">Moneda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($costos as $costo)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 pr-4 text-gray-800">{{ $costo->nombre }}</td>
                                    <td class="py-3 pr-4 text-gray-700">{{ number_format($costo->precio, 2) }}</td>
                                    <td class="py-3 pr-4 text-gray-700">{{ $costo->moneda }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Fotografías</h2>

            @if($fotos->isEmpty())
                <p class="text-gray-600">No hay fotografías registradas.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($fotos as $foto)
                        <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50 shadow-sm">
                            <img src="{{ asset('storage/' . $foto->url) }}"
                                 alt="Foto de veterinaria"
                                 class="w-full h-48 object-cover">
                            <div class="p-3 text-sm text-gray-600">
                                Foto {{ $foto->orden }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection