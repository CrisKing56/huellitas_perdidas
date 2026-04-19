@extends('admin.layout')

@section('title', 'Detalle de reporte')
@section('top_title', 'Detalle de reporte')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <p class="text-slate-200 text-sm uppercase tracking-[0.2em] mb-3">Moderación</p>
                <h1 class="text-4xl font-bold mb-2">Reporte #{{ $reporte->id_reporte }}</h1>
                <p class="text-slate-200 text-lg">Consulta la información completa del reporte y toma una decisión administrativa.</p>

                <div class="flex flex-wrap gap-3 mt-5">
                    @if($reporte->estado === 'ENVIADO')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            Enviado
                        </span>
                    @elseif($reporte->estado === 'EN_REVISION')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                            En revisión
                        </span>
                    @elseif($reporte->estado === 'RESUELTO')
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            Resuelto
                        </span>
                    @else
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Desestimado
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reportes.index') }}"
                   class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition border border-white/15">
                    Volver
                </a>

                @if($reporte->estado === 'ENVIADO')
                    <form action="{{ route('admin.reportes.enRevision', $reporte->id_reporte) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-semibold transition">
                            Marcar en revisión
                        </button>
                    </form>
                @endif

                @if($reporte->id_publicacion)
                    <a href="{{ route('extravios.show', $reporte->id_publicacion) }}" target="_blank"
                       class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition">
                        Ver publicación
                    </a>
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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Estado del reporte</p>
            <p class="text-2xl font-bold text-gray-800">{{ $reporte->estado }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Motivo</p>
            <p class="text-lg font-bold text-gray-800">{{ $reporte->motivo_nombre ?? 'No disponible' }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Fecha de envío</p>
            <p class="text-lg font-bold text-gray-800">
                {{ $reporte->creado_en ? \Carbon\Carbon::parse($reporte->creado_en)->format('d/m/Y H:i') : '—' }}
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Revisado por</p>
            <p class="text-lg font-bold text-gray-800">{{ $reporte->admin_revisor_nombre ?? 'Aún no asignado' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información del reporte</h2>
                <div class="space-y-3 text-gray-700">
                    <p><span class="font-semibold">ID del reporte:</span> #{{ $reporte->id_reporte }}</p>
                    <p><span class="font-semibold">Motivo:</span> {{ $reporte->motivo_nombre ?? 'No disponible' }}</p>
                    <p><span class="font-semibold">Descripción adicional:</span> {{ $reporte->descripcion_adicional ?: 'No proporcionada' }}</p>
                    <p><span class="font-semibold">Estado actual:</span> {{ $reporte->estado }}</p>
                    <p><span class="font-semibold">Fecha de envío:</span> {{ $reporte->creado_en ? \Carbon\Carbon::parse($reporte->creado_en)->format('d/m/Y H:i') : '—' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Publicación reportada</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        @if($fotoPrincipal)
                            <img src="{{ asset('storage/' . $fotoPrincipal->url) }}" alt="Foto principal"
                                 class="w-full h-72 object-cover rounded-2xl border border-gray-200">
                        @else
                            <div class="w-full h-72 rounded-2xl border border-gray-200 bg-gray-100 flex items-center justify-center text-gray-400">
                                Sin imagen
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 text-gray-700">
                        <p><span class="font-semibold">Mascota:</span> {{ $reporte->mascota_nombre ?? 'Publicación no disponible' }}</p>
                        <p><span class="font-semibold">Zona:</span> {{ $reporte->colonia_barrio ?? 'No disponible' }}</p>
                        <p><span class="font-semibold">Calle y referencias:</span> {{ $reporte->calle_referencias ?: 'No disponibles' }}</p>
                        <p><span class="font-semibold">Estado de la publicación:</span> {{ $reporte->estado_publicacion ?? 'No disponible' }}</p>
                        <p><span class="font-semibold">Fecha de extravío:</span>
                            {{ $reporte->fecha_extravio ? \Carbon\Carbon::parse($reporte->fecha_extravio)->format('d/m/Y') : 'No disponible' }}
                        </p>
                        <p><span class="font-semibold">Descripción:</span> {{ $reporte->publicacion_descripcion ?: 'No disponible' }}</p>
                    </div>
                </div>
            </div>

            @if($reporte->nota_resolucion || $reporte->revisado_en)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Resolución registrada</h2>
                    <div class="space-y-3 text-gray-700">
                        <p><span class="font-semibold">Estado final:</span> {{ $reporte->estado }}</p>
                        <p><span class="font-semibold">Revisado por:</span> {{ $reporte->admin_revisor_nombre ?? 'No disponible' }}</p>
                        <p><span class="font-semibold">Fecha de revisión:</span>
                            {{ $reporte->revisado_en ? \Carbon\Carbon::parse($reporte->revisado_en)->format('d/m/Y H:i') : 'No disponible' }}
                        </p>
                        <p><span class="font-semibold">Nota:</span> {{ $reporte->nota_resolucion ?: 'Sin nota de resolución.' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Reportante</h2>
                <div class="space-y-3 text-gray-700">
                    <p><span class="font-semibold">Nombre:</span> {{ $reporte->reportante_nombre ?? 'No disponible' }}</p>
                    <p><span class="font-semibold">Correo:</span> {{ $reporte->reportante_correo ?? 'No disponible' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Dueño de la publicación</h2>
                <div class="space-y-3 text-gray-700">
                    <p><span class="font-semibold">Nombre:</span> {{ $reporte->dueno_nombre ?? 'No disponible' }}</p>
                    <p><span class="font-semibold">Correo:</span> {{ $reporte->dueno_correo ?? 'No disponible' }}</p>
                </div>
            </div>

            @if(in_array($reporte->estado, ['ENVIADO', 'EN_REVISION']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Resolver reporte</h2>
                    <p class="text-gray-500 mb-4">
                        Selecciona si el reporte procede o si debe ser desestimado. Puedes agregar una nota administrativa.
                    </p>

                    <form action="{{ route('admin.reportes.resolver', $reporte->id_reporte) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Resolución</label>
                            <select name="estado_final"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar...</option>
                                <option value="RESUELTO" {{ old('estado_final') === 'RESUELTO' ? 'selected' : '' }}>Resuelto</option>
                                <option value="DESESTIMADO" {{ old('estado_final') === 'DESESTIMADO' ? 'selected' : '' }}>Desestimado</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nota de resolución</label>
                            <textarea
                                name="nota_resolucion"
                                rows="5"
                                placeholder="Escribe una nota administrativa opcional..."
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500"
                            >{{ old('nota_resolucion') }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 transition">
                            Guardar resolución
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection