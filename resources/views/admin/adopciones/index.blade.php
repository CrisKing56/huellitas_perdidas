@extends('admin.layout')

@section('title', 'Publicaciones de adopción - Panel Administrador')
@section('top_title', 'Publicaciones de adopción')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 text-white shadow-sm">
        <h1 class="text-4xl font-bold mb-2">Publicaciones de adopción</h1>
        <p class="text-slate-200 text-lg">
            Revisa el listado general de mascotas en adopción y administra su disponibilidad dentro de la plataforma.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Disponibles</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['disponibles'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">En proceso</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['en_proceso'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Pausadas</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['pausadas'] }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Adoptadas</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['adoptadas'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.adopciones.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="ID, nombre, especie, autor, organización o zona..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Estado</label>
                <select name="estado" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Todos</option>
                    <option value="DISPONIBLE" {{ $estado === 'DISPONIBLE' ? 'selected' : '' }}>Disponible</option>
                    <option value="EN_PROCESO" {{ $estado === 'EN_PROCESO' ? 'selected' : '' }}>En proceso</option>
                    <option value="PAUSADA" {{ $estado === 'PAUSADA' ? 'selected' : '' }}>Pausada</option>
                    <option value="ADOPTADA" {{ $estado === 'ADOPTADA' ? 'selected' : '' }}>Adoptada</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit" class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 transition">
                    Filtrar
                </button>
                <a href="{{ route('admin.adopciones.index') }}" class="w-full text-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Listado de publicaciones</h2>
            <p class="text-sm text-gray-500 mt-1">
                Desde aquí puedes revisar detalle, ver solicitudes relacionadas y pausar o reactivar publicaciones.
            </p>
        </div>

        @if($publicaciones->isEmpty())
            <div class="p-6 text-gray-500">
                No hay publicaciones de adopción registradas todavía.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-4 font-semibold">Mascota</th>
                            <th class="px-6 py-4 font-semibold">Autor</th>
                            <th class="px-6 py-4 font-semibold">Clasificación</th>
                            <th class="px-6 py-4 font-semibold">Solicitudes</th>
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
                                    <div class="font-semibold text-gray-800">
                                        {{ $publicacion->organizacion_nombre ?: ($publicacion->autor_nombre ?: 'Sin autor') }}
                                    </div>
                                    <div class="text-gray-500 text-sm mt-1 break-all">
                                        {{ $publicacion->autor_correo ?: 'Sin correo' }}
                                    </div>
                                    @if($publicacion->organizacion_tipo)
                                        <div class="text-xs text-gray-500 mt-2">
                                            {{ $publicacion->organizacion_tipo }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top text-gray-700">
                                    <div><span class="font-semibold">Especie:</span> {{ $publicacion->especie_nombre ?? 'No disponible' }}</div>
                                    <div class="mt-1"><span class="font-semibold">Raza:</span> {{ $publicacion->raza_nombre ?? 'No especificada' }}</div>
                                    <div class="mt-1"><span class="font-semibold">Edad:</span> {{ $publicacion->edad_anios ?? '—' }} años</div>
                                    <div class="mt-1"><span class="font-semibold">Sexo:</span> {{ $publicacion->sexo }}</div>
                                    <div class="mt-1"><span class="font-semibold">Tamaño:</span> {{ $publicacion->tamano }}</div>
                                </td>

                                <td class="px-6 py-5 align-top text-gray-700">
                                    <div><span class="font-semibold">Total:</span> {{ $publicacion->total_solicitudes }}</div>
                                    <div class="mt-1"><span class="font-semibold">Enviadas:</span> {{ $publicacion->solicitudes_enviadas }}</div>
                                    <div class="mt-1"><span class="font-semibold">Aceptadas:</span> {{ $publicacion->solicitudes_aceptadas }}</div>
                                    <div class="mt-1"><span class="font-semibold">Rechazadas:</span> {{ $publicacion->solicitudes_rechazadas }}</div>
                                </td>

                                <td class="px-6 py-5 align-top">
                                    @if($publicacion->estado === 'DISPONIBLE')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Disponible
                                        </span>
                                    @elseif($publicacion->estado === 'EN_PROCESO')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            En proceso
                                        </span>
                                    @elseif($publicacion->estado === 'PAUSADA')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Pausada
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            Adoptada
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 align-top text-gray-600">
                                    {{ $publicacion->created_at ? \Carbon\Carbon::parse($publicacion->created_at)->format('d/m/Y H:i') : '—' }}
                                </td>

                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col gap-2 min-w-[150px]">
                                        <a href="{{ route('admin.adopciones.show', $publicacion->id_publicacion) }}"
                                           class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold transition">
                                            Ver detalle
                                        </a>

                                        @if(in_array($publicacion->estado, ['DISPONIBLE', 'EN_PROCESO']))
                                            <form action="{{ route('admin.adopciones.pausar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas pausar esta publicación?');">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-semibold transition">
                                                    Pausar
                                                </button>
                                            </form>
                                        @elseif($publicacion->estado === 'PAUSADA')
                                            <form action="{{ route('admin.adopciones.reactivar', $publicacion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Deseas reactivar esta publicación?');">
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