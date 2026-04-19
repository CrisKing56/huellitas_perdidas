@extends('admin.layout')

@section('title', 'Detalle de refugio')
@section('top_title', 'Detalle de refugio')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Detalle administrativo</p>
                <h1 class="text-4xl font-bold mb-2">{{ $refugio->nombre }}</h1>
                <p class="text-slate-200 text-lg">Consulta la información completa y administra el estado de esta cuenta.</p>

                <div class="flex flex-wrap gap-3 mt-5">
                    @if($refugio->estado_revision === 'APROBADA')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            Solicitud aprobada
                        </span>
                    @elseif($refugio->estado_revision === 'PENDIENTE')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            Solicitud pendiente
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Solicitud rechazada
                        </span>
                    @endif

                    @if($refugio->estado_usuario === 'SUSPENDIDA')
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
                <a href="{{ route('admin.refugios.index') }}"
                   class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition border border-white/15">
                    Volver
                </a>

                @if($refugio->estado_revision !== 'APROBADA')
                    <form action="{{ route('admin.refugios.aprobar', $refugio->id_organizacion) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-semibold transition">
                            Aprobar solicitud
                        </button>
                    </form>
                @endif

                @if($refugio->estado_revision === 'APROBADA' && $refugio->estado_usuario === 'ACTIVA')
                    <form action="{{ route('admin.refugios.suspender', $refugio->id_organizacion) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                            Suspender cuenta
                        </button>
                    </form>
                @endif

                @if($refugio->estado_revision === 'APROBADA' && $refugio->estado_usuario === 'SUSPENDIDA')
                    <form action="{{ route('admin.refugios.reactivar', $refugio->id_organizacion) }}" method="POST">
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
            <p class="text-2xl font-bold text-gray-800">{{ $refugio->estado_revision }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Estado de cuenta</p>
            <p class="text-2xl font-bold text-gray-800">{{ $refugio->estado_usuario }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Correo asociado</p>
            <p class="text-lg font-semibold text-gray-800 break-all">{{ $refugio->correo }}</p>
        </div>
    </div>

    @if($refugio->motivo_rechazo)
        <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-red-700 mb-3">Motivo del rechazo</h2>
            <p class="text-red-800 whitespace-pre-line">{{ $refugio->motivo_rechazo }}</p>
        </div>
    @endif

    @if($refugio->estado_revision === 'PENDIENTE')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Rechazar solicitud</h2>
            <p class="text-gray-500 mb-5">Usa esta opción solo cuando la solicitud no cumple con los requisitos o la información enviada es insuficiente.</p>

            <form action="{{ route('admin.refugios.rechazar', $refugio->id_organizacion) }}" method="POST" class="space-y-4">
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
                <p><span class="font-semibold">Nombre:</span> {{ $refugio->nombre }}</p>
                <p><span class="font-semibold">Descripción:</span> {{ $refugio->descripcion }}</p>
                <p><span class="font-semibold">Estado de revisión:</span> {{ $refugio->estado_revision }}</p>
                <p><span class="font-semibold">Teléfono:</span> {{ $refugio->telefono }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Cuenta asociada</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Responsable de cuenta:</span> {{ $refugio->nombre_usuario }}</p>
                <p><span class="font-semibold">Correo:</span> {{ $refugio->correo }}</p>
                <p><span class="font-semibold">Teléfono usuario:</span> {{ $refugio->telefono_usuario }}</p>
                <p><span class="font-semibold">WhatsApp:</span> {{ $refugio->whatsapp ?? 'No registrado' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Dirección</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Calle y número:</span> {{ $refugio->calle_numero }}</p>
                <p><span class="font-semibold">Colonia:</span> {{ $refugio->colonia }}</p>
                <p><span class="font-semibold">Código postal:</span> {{ $refugio->codigo_postal }}</p>
                <p><span class="font-semibold">Ciudad:</span> {{ $refugio->ciudad }}</p>
                <p><span class="font-semibold">Estado:</span> {{ $refugio->estado_direccion }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Detalle del refugio</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Capacidad de perros:</span> {{ $refugio->capacidad_perros ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Capacidad de gatos:</span> {{ $refugio->capacidad_gatos ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Instalaciones:</span> {{ $refugio->instalaciones_descripcion ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Requisitos de adopción:</span> {{ $refugio->requisitos_adopcion ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Acepta donaciones:</span> {{ isset($refugio->acepta_donaciones) ? ($refugio->acepta_donaciones ? 'Sí' : 'No') : 'No registrado' }}</p>
                <p><span class="font-semibold">Tipo de donaciones:</span> {{ $refugio->tipo_donaciones ?? 'No registrado' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Especies aceptadas</h2>
            @if($especies->isEmpty())
                <p class="text-gray-600">No hay especies registradas.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($especies as $especie)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-700">
                            {{ $especie }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Ubicación</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <p><span class="font-semibold">Latitud:</span> {{ $refugio->latitud ?? 'No registrada' }}</p>
                <p><span class="font-semibold">Longitud:</span> {{ $refugio->longitud ?? 'No registrada' }}</p>
            </div>
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
                                 alt="Foto de refugio"
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