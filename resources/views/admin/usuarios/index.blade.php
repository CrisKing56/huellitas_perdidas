@extends('admin.layout')

@section('title', 'Administración de Usuarios')
@section('top_title', 'Panel Administrador')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Administración de usuarios</h1>
    <p class="text-gray-600 text-sm mt-1">Gestiona todos los usuarios registrados</p>
</div>

<div class="flex items-center justify-between mb-6 gap-4">
    <form method="GET" class="relative flex-1 max-w-md">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input name="q" value="{{ $q ?? '' }}" type="text" placeholder="Buscar por nombre, correo o ID..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
    </form>

    <a href="{{ route('admin.usuarios.create') }}"
       class="flex items-center gap-2 bg-primary hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-medium transition shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Agregar usuario
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Usuario</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
        @forelse($usuarios as $u)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-orange-500 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($u->nombre, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $u->nombre }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $u->id_usuario }}</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">{{ $u->correo }}</td>

                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $u->rol }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    @php
                        $bg = $u->estado === 'ACTIVA' ? 'bg-green-100 text-green-800' : ($u->estado === 'SUSPENDIDA' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-800');
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bg }}">
                        {{ $u->estado }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <!-- Editar (FUNCIONA) -->
                        <a href="{{ route('admin.usuarios.edit', $u->id_usuario) }}" class="text-yellow-600 hover:text-yellow-800" title="Editar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        <!-- Eliminar (FUNCIONA) -->
                        <form action="{{ route('admin.usuarios.destroy', $u->id_usuario) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar usuario?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-800" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                    No hay usuarios para mostrar.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
