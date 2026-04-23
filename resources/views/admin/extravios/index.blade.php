@extends('admin.layout')

@section('title', 'Publicaciones de extravío - Panel Administrador')
@section('top_title', 'Publicaciones de extravío')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <h1 class="text-4xl font-bold mb-2">Publicaciones de extravío</h1>
        <p class="text-slate-200 text-lg">
            Revisa el listado general de mascotas extraviadas y administra su visibilidad dentro de la plataforma.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Activas</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['activas'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Resueltas</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['resueltas'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Ocultas</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['eliminadas'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Con reportes</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['con_reportes'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.extravios.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="ID, nombre, especie, autor, correo o zona..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Estado</label>
                <select name="estado" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Todos</option>
                    <option value="ACTIVA" {{ $estado === 'ACTIVA' ? 'selected' : '' }}>Activa</option>
                    <option value="RESUELTA" {{ $estado === 'RESUELTA' ? 'selected' : '' }}>Resuelta</option>
                    <option value="ELIMINADA" {{ $estado === 'ELIMINADA' ? 'selected' : '' }}>Oculta</option>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-2 text-transparent select-none hidden md:block">&nbsp;</label>
                
                <div class="flex flex-col gap-3">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="submit" class="w-full h-12 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition flex items-center justify-center">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.extravios.index') }}" class="w-full h-12 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition flex items-center justify-center">
                            Limpiar
                        </a>
                    </div>
                    
                    <a href="{{ route('reportes.mascotas.pdf', request()->query()) }}" 
                       class="w-full h-12 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Listado de publicaciones</h2>
            <p class="text-sm text-gray-500 mt-1">
                Desde aquí puedes revisar detalle, ver actividad relacionada y ocultar o reactivar publicaciones.
            </p>
        </div>

        @if($publicaciones->isEmpty())
            <div class="p-6 text-gray-500">
                No hay publicaciones de extravío registradas todavía.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-4 font-semibold">Mascota</th>
                            <th class="px-6 py-4 font-semibold">Autor</th>
                            <th class="px-6 py-4 font-semibold">Clasificación</th>
                            <th class="px-6 py-4 font-semibold">Actividad</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($publicaciones as $publicacion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-5 align-top">
                                    <div class="flex gap-4">
                                        <div class="w-24 h-20 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                                            @if($publicacion->foto_principal)
                                                <img
                                                    src="{{ asset('storage/' . $publicacion->foto_principal) }}"
                                                    alt="{{ $publicacion->nombre }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                    Sin foto
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="font-bold text-gray-800 text-base">{{ $publicacion->nombre }}</div>
                                            <div class="text-gray-500 text-sm mt-1">ID publicación: {{ $publicacion->id_publicacion }}</div>
                                            <div class="text-gray-600 text-sm mt-2">
                                                {{ \Illuminate\Support\Str::limit($publicacion->descripcion, 90) }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-2">
                                                {{ $publicacion->colonia_barrio }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="font-semibold text-gray-800">{{ $publicacion->autor_nombre ?? 'Sin autor' }}</div>
                                    <div class="text-gray-500 text-sm mt-1 break-all">{{ $publicacion->autor_correo ?? 'Sin correo' }}</div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-700">
                                    <div><span class="font-semibold">Especie:</span> {{ $publicacion->especie_nombre ?? 'No disponible' }}</div>
                                    <div class="mt-1"><span class="font-semibold">Raza:</span> {{ $publicacion->raza_nombre ?? 'No especificada' }}</div>
                                    <div class="mt-1"><span class="font-semibold">Color:</span> {{ $publicacion->color }}</div>
                                    <div class="mt-1"><span class="font-semibold">Tamaño:</span> {{ $publicacion->tamano }}</div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-700">
                                    <div><span class="font-semibold">Comentarios:</span> {{ $publicacion->total_comentarios }}</div>
                                    <div class="mt-1"><span class="font-semibold">Avistamientos:</span> {{ $publicacion->total_avistamientos }}</div>
                                    <div class="mt-1"><span class="font-semibold">Reportes:</span> {{ $publicacion->total_reportes }}</div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($publicacion->estado === 'ACTIVA')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            Activa
                                        </span>
                                    @elseif($publicacion->estado === 'RESUELTA')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Resuelta
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Oculta
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    {{ $publicacion->created_at ? \Carbon\Carbon::parse($publicacion->created_at)->format('d/m/Y H:i') : '—' }}
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col gap-2 min-w-[150px]">
                                        <a href="{{ route('admin.extravios.show', $publicacion->id_publicacion) }}"
                                           class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold transition">
                                            Ver detalle
                                        </a>

                                        @if($publicacion->estado === 'ACTIVA')
                                            <form action="{{ route('admin.extravios.ocultar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas ocultar esta publicación?');">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-semibold transition">
                                                    Ocultar
                                                </button>
                                            </form>
                                        @elseif($publicacion->estado === 'ELIMINADA')
                                            <form action="{{ route('admin.extravios.reactivar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas reactivar esta publicación?');">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-green-50 hover:bg-green-100 text-green-700 font-semibold transition">
                                                    Reactivar
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

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $publicaciones->links() }}
        </div>
    </div>
</div>
@endsection