@extends('admin.layout')

@section('title', 'Editar usuario - Huellitas Perdidas')
@section('top_title', 'Panel Administrador')
@section('panel_title', 'Panel Administrador')

@section('content')
@php
    $esUsuarioActual = auth()->check() && ((int) auth()->user()->id_usuario === (int) $usuario->id_usuario);

    $badgeRol = match ($usuario->rol) {
        'ADMIN' => 'bg-purple-100 text-purple-700 border-purple-200',
        'VETERINARIA' => 'bg-sky-100 text-sky-700 border-sky-200',
        'REFUGIO' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        default => 'bg-orange-100 text-orange-700 border-orange-200',
    };

    $badgeEstado = match ($usuario->estado) {
        'ACTIVA' => 'bg-green-100 text-green-700 border-green-200',
        'SUSPENDIDA' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'ELIMINADA' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };
@endphp

<div class="max-w-5xl space-y-6">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-500 mb-2">
                Gestión de cuentas
            </p>
            <h1 class="text-3xl font-bold text-gray-900">Editar usuario</h1>
            <p class="text-sm text-gray-500 mt-2">
                Actualiza la información general de la cuenta sin modificar su contraseña.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeRol }}">
                {{ $usuario->rol }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeEstado }}">
                {{ $usuario->estado }}
            </span>
            @if($esUsuarioActual)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-orange-100 text-orange-700 border-orange-200">
                    Tu cuenta
                </span>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-red-700">
            <p class="font-bold mb-2">Corrige los siguientes errores:</p>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <form action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" method="POST" class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-6 md:p-8 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Información de la cuenta</h2>
                <p class="text-sm text-gray-500 mt-1">Edita nombre, correo, teléfono y rol.</p>
            </div>

            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                    <input
                        name="nombre"
                        value="{{ old('nombre', $usuario->nombre) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        maxlength="120"
                        required
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Correo electrónico</label>
                    <input
                        type="email"
                        name="correo"
                        value="{{ old('correo', $usuario->correo) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        maxlength="120"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                    <input
                        name="telefono"
                        value="{{ old('telefono', $usuario->telefono) }}"
                        inputmode="numeric"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        oninput="this.value=this.value.replace(/[^0-9]/g, '').slice(0,10)"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="10 dígitos"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-2">Solo números. Ejemplo: 9191234567</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                    <select
                        name="rol"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        required
                    >
                        @foreach(['USUARIO','ADMIN','VETERINARIA','REFUGIO'] as $rol)
                            <option value="{{ $rol }}" @selected(old('rol', $usuario->rol) === $rol)>{{ $rol }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                        <p class="text-sm font-semibold text-gray-800">Contraseña</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Desde esta pantalla no se modifica la contraseña del usuario. Aquí solo administras los datos generales de la cuenta.
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 md:px-8 py-5 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3">
                <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition shadow-sm">
                    Guardar cambios
                </button>

                <a href="{{ route('admin.usuarios.index') }}"
                   class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition font-semibold">
                    Volver
                </a>
            </div>
        </form>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen rápido</h3>

                <div class="space-y-4">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">ID de usuario</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $usuario->id_usuario }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Proveedor de acceso</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $usuario->auth_provider ?? 'LOCAL' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-bold">Estado actual</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $usuario->estado }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Acciones de cuenta</h3>

                <div class="space-y-3">
                    @if($usuario->estado !== 'ACTIVA')
                        <form action="{{ route('admin.usuarios.activate', $usuario->id_usuario) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition font-semibold">
                                Activar cuenta
                            </button>
                        </form>
                    @endif

                    @if($usuario->estado === 'ACTIVA' && !$esUsuarioActual)
                        <form action="{{ route('admin.usuarios.suspend', $usuario->id_usuario) }}" method="POST"
                              onsubmit="return confirm('¿Suspender esta cuenta?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 transition font-semibold">
                                Suspender cuenta
                            </button>
                        </form>
                    @endif

                    @if($usuario->estado !== 'ELIMINADA' && !$esUsuarioActual)
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST"
                              onsubmit="return confirm('¿Marcar esta cuenta como eliminada?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition font-semibold">
                                Eliminar cuenta
                            </button>
                        </form>
                    @endif

                    @if($esUsuarioActual)
                        <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-700">
                            Por seguridad no puedes suspender o eliminar tu propia cuenta desde esta pantalla.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection