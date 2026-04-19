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
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Detalle administrativo</p>
                <h1 class="text-4xl font-bold mb-2">{{ $veterinaria->nombre }}</h1>
                <p class="text-slate-200 text-lg">Consulta la información completa y administra el estado de esta cuenta.</p>

                <div class="flex flex-wrap gap-3 mt-5">
                    @if($veterinaria->estado_revision === 'APROBADA')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            Solicitud aprobada
                        </span>
                    @elseif($veterinaria->estado_revision === 'PENDIENTE')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            Solicitud pendiente
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Solicitud rechazada
                        </span>
                    @endif

                    @if($veterinaria->estado_usuario === 'SUSPENDIDA')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Cuenta suspendida
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                            Cuenta activa
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.veterinarias.index') }}"
                   class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition border border-white/15">
                    Volver
                </a>

                @if($veterinaria->estado_revision !== 'APROBADA')
                    <form action="{{ route('admin.veterinarias.aprobar', $veterinaria->id_organizacion) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-semibold transition">
                            Aprobar solicitud
                        </button>
                    </form>
                @endif

                @if($veterinaria->estado_revision === 'APROBADA' && $veterinaria->estado_usuario === 'ACTIVA')
                    <form action="{{ route('admin.veterinarias.suspender', $veterinaria->id_organizacion) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                            Suspender cuenta
                        </button>
                    </form>
                @endif

                @if($veterinaria->estado_revision === 'APROBADA' && $veterinaria->estado_usuario === 'SUSPENDIDA')
                    <form action="{{ route('admin.veterinarias.reactivar', $veterinaria->id_organizacion) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-semibold transition">
                            Reactivar cuenta
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            <p class="font-semibold mb-2">Revisa estos campos:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Estado de revisión</p>
            <p class="text-2xl font-bold text-gray-800">{{ $veterinaria->estado_revision }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Estado de cuenta</p>
            <p class="text-2xl font-bold text-gray-800">{{ $veterinaria->estado_usuario }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Correo asociado</p>
            <p class="text-lg font-semibold text-gray-800 break-all">{{ $veterinaria->correo }}</p>
        </div>
    </div>

    @if($veterinaria->motivo_rechazo)
        <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-red-700 mb-3">Motivo del rechazo</h2>
            <p class="text-red-800 whitespace-pre-line">{{ $veterinaria->motivo_rechazo }}</p>
        </div>
    @endif

    @if($veterinaria->estado_revision === 'PENDIENTE')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Rechazar solicitud</h2>
            <p class="text-gray-500 mb-5">Usa esta opción solo cuando la solicitud no cumple con los requisitos o la información enviada es insuficiente.</p>

            <form action="{{ route('admin.veterinarias.rechazar', $veterinaria->id_organizacion) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Motivo del rechazo</label>
                    <textarea
                        name="motivo_rechazo"
                        rows="5"
                        placeholder="Escribe claramente por qué se rechaza esta solicitud..."
                        class="w-full rounded-2xl border border-gray-300 focus:border-red-500 focus:ring-red-500 py-3 px-4 text-gray-800 bg-gray-50"
                    >{{ old('motivo_rechazo') }}</textarea>
                </div>

                <button type="submit"
                    class="px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                    Rechazar solicitud
                </button>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Información general</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Nombre:</span> {{ $veterinaria->nombre }}</p>
                <p><span class="font-semibold">Descripción:</span> {{ $veterinaria->descripcion }}</p>
                <p><span class="font-semibold">Teléfono de la veterinaria:</span> {{ $veterinaria->telefono }}</p>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <p><span class="font-semibold">Latitud:</span> {{ $veterinaria->latitud ?? 'No registrada' }}</p>
                <p><span class="font-semibold">Longitud:</span> {{ $veterinaria->longitud ?? 'No registrada' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Horarios de atención</h2>

            @if($horarios->isEmpty())
                <p class="text-gray-600">No hay horarios registrados.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($horarios as $horario)
                        <div class="border border-gray-200 rounded-2xl p-4 bg-gray-50">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($fotos as $foto)
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-gray-50 shadow-sm">
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