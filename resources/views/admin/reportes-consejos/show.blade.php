@extends('admin.layout')

@section('title', 'Detalle de reporte de consejo')
@section('top_title', 'Detalle de reporte')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Detalle administrativo</p>
                <h1 class="text-4xl font-bold mb-2">Reporte #{{ $reporte->id_reporte }}</h1>
                <p class="text-slate-200 text-lg">
                    Revisa la información del reporte y toma una decisión administrativa.
                </p>

                <div class="flex flex-wrap gap-3 mt-5">
                    @if($reporte->estado === 'ABIERTO')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Abierto
                        </span>
                    @elseif($reporte->estado === 'EN_REVISION')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            En revisión
                        </span>
                    @elseif($reporte->estado === 'RESUELTO')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            Resuelto
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-gray-200 text-gray-700">
                            Descartado
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reportes-consejos.index') }}"
                   class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition border border-white/15">
                    Volver
                </a>

                @if($reporte->estado === 'ABIERTO')
                    <form action="{{ route('admin.reportes-consejos.en-revision', $reporte->id_reporte) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="px-5 py-3 rounded-xl bg-yellow-500 hover:bg-yellow-600 text-white font-semibold transition">
                            Marcar en revisión
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Información del reporte</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Motivo:</span> {{ $reporte->motivo }}</p>
                <p><span class="font-semibold">Descripción:</span> {{ $reporte->descripcion ?: 'Sin descripción adicional' }}</p>
                <p><span class="font-semibold">Estado:</span> {{ $reporte->estado }}</p>
                <p><span class="font-semibold">Fecha:</span> {{ \Carbon\Carbon::parse($reporte->creado_en)->translatedFormat('d \d\e F, Y H:i') }}</p>
                <p><span class="font-semibold">Reportado por:</span> {{ $reporte->usuarioReporta->nombre ?? 'Usuario no disponible' }}</p>
                <p><span class="font-semibold">Correo:</span> {{ $reporte->usuarioReporta->correo ?? 'Sin correo' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Consejo reportado</h2>
            <div class="space-y-3 text-gray-700">
                <p><span class="font-semibold">Título:</span> {{ $reporte->consejo->titulo ?? 'Consejo eliminado' }}</p>
                <p><span class="font-semibold">Categoría:</span> {{ $reporte->consejo->categoria->nombre ?? 'General' }}</p>
                <p><span class="font-semibold">Organización:</span> {{ $reporte->consejo->organizacion->nombre ?? 'No disponible' }}</p>
                <p><span class="font-semibold">Estado del consejo:</span> {{ $reporte->consejo->estado_publicacion ?? 'No disponible' }}</p>
                @if($reporte->consejo)
                    <a href="{{ route('admin.consejos.show', $reporte->consejo->id_consejo) }}"
                       class="inline-flex items-center justify-center mt-3 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold hover:bg-blue-100 transition">
                        Ver consejo en admin
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Contenido del consejo</h2>
            <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $reporte->consejo->contenido ?? 'No disponible' }}
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Imágenes del consejo</h2>

            @if(!$reporte->consejo || $reporte->consejo->imagenes->isEmpty())
                <p class="text-gray-600">No hay imágenes registradas.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($reporte->consejo->imagenes as $imagen)
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-gray-50 shadow-sm">
                            <img
                                src="{{ asset('storage/' . $imagen->url) }}"
                                alt="Imagen del consejo"
                                class="w-full h-48 object-cover"
                            >
                            <div class="p-3 text-sm text-gray-600">
                                Imagen {{ $imagen->orden }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if(in_array($reporte->estado, ['ABIERTO', 'EN_REVISION']))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Resolver reporte</h2>
            <p class="text-gray-500 mb-5">
                Define el resultado del caso y registra la acción tomada.
            </p>

            <form action="{{ route('admin.reportes-consejos.resolver', $reporte->id_reporte) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Resultado</label>
                        <select
                            name="estado"
                            class="w-full rounded-2xl border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-50"
                        >
                            <option value="">Selecciona...</option>
                            <option value="RESUELTO" {{ old('estado') === 'RESUELTO' ? 'selected' : '' }}>Resuelto</option>
                            <option value="DESCARTADO" {{ old('estado') === 'DESCARTADO' ? 'selected' : '' }}>Descartado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Acción tomada</label>
                        <input
                            type="text"
                            name="accion_tomada"
                            value="{{ old('accion_tomada') }}"
                            placeholder="Ej. Revisado por administración"
                            class="w-full rounded-2xl border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-50"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Motivo de resolución</label>
                    <textarea
                        name="motivo_resolucion"
                        rows="4"
                        placeholder="Explica brevemente la decisión tomada..."
                        class="w-full rounded-2xl border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-50"
                    >{{ old('motivo_resolucion') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition">
                    Guardar resolución
                </button>
            </form>
        </div>
    @endif
</div>
@endsection