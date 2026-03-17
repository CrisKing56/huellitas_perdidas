@extends('admin.layout')

@section('title', 'Detalle de refugio')
@section('top_title', 'Detalle de refugio')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $refugio->nombre }}</h1>
            <p class="text-gray-500 text-lg">Información completa del refugio registrado.</p>
        </div>

        <a href="{{ route('admin.refugios.index') }}"
           class="px-5 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition">
            Volver
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Información general</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Nombre:</span> {{ $refugio->nombre }}</p>
                <p><span class="font-semibold">Descripción:</span> {{ $refugio->descripcion }}</p>
                <p><span class="font-semibold">Estado de revisión:</span> {{ $refugio->estado_revision }}</p>
                <p><span class="font-semibold">Teléfono:</span> {{ $refugio->telefono }}</p>
                <p><span class="font-semibold">Tipo de organización:</span> {{ $refugio->tipo_organizacion ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Año de fundación:</span> {{ $refugio->anio_fundacion ?? 'No registrado' }}</p>
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
                <p><span class="font-semibold">Capacidad total:</span> {{ $refugio->capacidad_total ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Animales actuales:</span> {{ $refugio->animales_actuales ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Animales dados en adopción:</span> {{ $refugio->animales_dados_adopcion ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Años de operación:</span> {{ $refugio->anios_operacion ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Nombre responsable:</span> {{ $refugio->nombre_responsable ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Cargo responsable:</span> {{ $refugio->cargo_responsable ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Número de voluntarios:</span> {{ $refugio->num_voluntarios ?? 'No registrado' }}</p>
                <p><span class="font-semibold">Otras especies:</span> {{ $refugio->otras_especies ?? 'No registrado' }}</p>
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
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Latitud:</span> {{ $refugio->latitud ?? 'No registrada' }}</p>
                <p><span class="font-semibold">Longitud:</span> {{ $refugio->longitud ?? 'No registrada' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection