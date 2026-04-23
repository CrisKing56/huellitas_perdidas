@extends('admin.layout')

@section('title', 'Administración de Usuarios')
@section('top_title', 'Panel Administrador')
@section('panel_title', 'Panel Administrador')

@section('content')
@php
    $resumen = $resumen ?? [
        'total' => 0,
        'activas' => 0,
        'suspendidas' => 0,
        'eliminadas' => 0,
        'admins' => 0,
    ];

    $badgeRol = function ($rol) {
        return match ($rol) {
            'ADMIN' => 'bg-purple-100 text-purple-700 border-purple-200',
            'VETERINARIA' => 'bg-sky-100 text-sky-700 border-sky-200',
            'REFUGIO' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            default => 'bg-orange-100 text-orange-700 border-orange-200',
        };
    };

    $badgeEstado = function ($estado) {
        return match ($estado) {
            'ACTIVA' => 'bg-green-100 text-green-700 border-green-200',
            'SUSPENDIDA' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'ELIMINADA' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    };
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Administración de usuarios</h1>
        <p class="text-gray-600 text-sm mt-1">Gestiona todos los usuarios registrados</p>
    </div>
    
    <a href="{{ route('admin.usuarios.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Agregar usuario
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-600 mb-2">Buscar</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nombre, correo o ID..." class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
        </div>

        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-600 mb-2">Rol</label>
            <select name="rol" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                <option value="">Todos los roles</option>
                <option value="ADMIN" {{ request('rol') == 'ADMIN' ? 'selected' : '' }}>Administrador</option>
                <option value="USUARIO" {{ request('rol') == 'USUARIO' ? 'selected' : '' }}>Usuario normal</option>
            </select>
        </div>

        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-600 mb-2">Estado</label>
            <select name="estado" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                <option value="">Todos los estados</option>
                <option value="ACTIVA" {{ request('estado') == 'ACTIVA' ? 'selected' : '' }}>Activa</option>
                <option value="SUSPENDIDA" {{ request('estado') == 'SUSPENDIDA' ? 'selected' : '' }}>Suspendida</option>
            </select>
        </div>

        <div>
            <label class="block text-sm mb-2 text-transparent select-none hidden md:block">&nbsp;</label>
            <div class="flex flex-col gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" class="w-full h-12 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition flex items-center justify-center">Filtrar</button>
                    <a href="{{ route('admin.usuarios.index') }}" class="w-full h-12 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition flex items-center justify-center">Limpiar</a>
                </div>
                
                <a href="{{ route('reportes.usuarios.pdf', request()->query()) }}" class="w-full h-12 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Exportar PDF
                </a>
            </div>
        </div>
    </form>
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

        
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Total</p>
            <p class="text-3xl font-black text-gray-900 mt-2">{{ $resumen['total'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Usuarios registrados</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Activas</p>
            <p class="text-3xl font-black text-green-600 mt-2">{{ $resumen['activas'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Cuentas funcionando</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Suspendidas</p>
            <p class="text-3xl font-black text-yellow-600 mt-2">{{ $resumen['suspendidas'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Acceso bloqueado</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Eliminadas</p>
            <p class="text-3xl font-black text-red-600 mt-2">{{ $resumen['eliminadas'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Marcadas como eliminadas</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Admins</p>
            <p class="text-3xl font-black text-purple-600 mt-2">{{ $resumen['admins'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Cuentas administrativas</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="xl:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input name="q"
                           value="{{ $q ?? '' }}"
                           type="text"
                           placeholder="Buscar por nombre, correo o ID..."
                           class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                <select name="rol"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    <option value="">Todos</option>
                    @foreach(['USUARIO','ADMIN','VETERINARIA','REFUGIO'] as $itemRol)
                        <option value="{{ $itemRol }}" @selected(($rol ?? '') === $itemRol)>{{ $itemRol }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <select name="estado"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    <option value="">Todos</option>
                    @foreach(['ACTIVA','SUSPENDIDA','ELIMINADA'] as $itemEstado)
                        <option value="{{ $itemEstado }}" @selected(($estado ?? '') === $itemEstado)>{{ $itemEstado }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="inline-flex items-center justify-center px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-sm transition w-full">
                    Filtrar
                </button>

                <a href="{{ route('admin.usuarios.index') }}"
                   class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Usuario</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Contacto</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Rol</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($usuarios as $u)
                        @php
                            $esUsuarioActual = auth()->check() && ((int) auth()->user()->id_usuario === (int) $u->id_usuario);
                        @endphp

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                        {{ strtoupper(mb_substr($u->nombre, 0, 1)) }}
                                    </div>

                                    <div>
                                        <p class="font-bold text-gray-900">{{ $u->nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-1">ID: {{ $u->id_usuario }}</p>
                                        @if($esUsuarioActual)
                                            <span class="inline-flex mt-2 px-2 py-1 rounded-full bg-orange-100 text-orange-700 text-[11px] font-bold uppercase">
                                                Tu cuenta
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <p class="text-sm font-medium text-gray-800">{{ $u->correo }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $u->telefono ?: 'Sin teléfono' }}</p>
                            </td>

                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeRol($u->rol) }}">
                                    {{ $u->rol }}
                                </span>
                            </td>

                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeEstado($u->estado) }}">
                                    {{ $u->estado }}
                                </span>
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.usuarios.edit', $u->id_usuario) }}"
                                       class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 transition text-sm font-semibold">
                                        Editar
                                    </a>

                                    @if($u->estado !== 'ACTIVA')
                                        <form action="{{ route('admin.usuarios.activate', $u->id_usuario) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition text-sm font-semibold">
                                                Activar
                                            </button>
                                        </form>
                                    @endif

                                    @if($u->estado === 'ACTIVA' && !$esUsuarioActual)
                                        <form action="{{ route('admin.usuarios.suspend', $u->id_usuario) }}" method="POST"
                                              onsubmit="return confirm('¿Suspender esta cuenta?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 transition text-sm font-semibold">
                                                Suspender
                                            </button>
                                        </form>
                                    @endif

                                    @if($u->estado !== 'ELIMINADA' && !$esUsuarioActual)
                                        <form action="{{ route('admin.usuarios.destroy', $u->id_usuario) }}" method="POST"
                                              onsubmit="return confirm('¿Marcar esta cuenta como eliminada?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition text-sm font-semibold">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No hay usuarios para mostrar con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($usuarios, 'links'))
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection