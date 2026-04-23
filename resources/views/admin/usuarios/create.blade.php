@extends('admin.layout')

@section('title', 'Crear usuario - Huellitas Perdidas')
@section('top_title', 'Panel Administrador')
@section('panel_title', 'Panel Administrador')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-500 mb-2">
            Gestión de cuentas
        </p>
        <h1 class="text-3xl font-bold text-gray-900">Agregar usuario</h1>
        <p class="text-sm text-gray-500 mt-2">
            Crea una nueva cuenta dentro del sistema. La cuenta se registrará automáticamente como <strong>ACTIVA</strong>.
        </p>
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

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6 md:p-8 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Datos principales</h2>
            <p class="text-sm text-gray-500 mt-1">Completa la información básica de la cuenta.</p>
        </div>

        <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                <input
                    name="nombre"
                    value="{{ old('nombre') }}"
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
                    value="{{ old('correo') }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    maxlength="120"
                    required
                >
                <p class="text-xs text-gray-500 mt-2">Debe ser un correo único dentro de la plataforma.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                <input
                    name="telefono"
                    value="{{ old('telefono') }}"
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
                        <option value="{{ $rol }}" @selected(old('rol') === $rol)>{{ $rol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                <input
                    type="password"
                    name="password"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    required
                >
                <p class="text-xs text-gray-500 mt-2">
                    Mínimo 8 caracteres, con al menos una letra y un número.
                </p>
            </div>

            <div class="md:col-span-2">
                <div class="rounded-2xl bg-orange-50 border border-orange-100 p-4">
                    <p class="text-sm font-semibold text-orange-800">Estado inicial de la cuenta</p>
                    <p class="text-sm text-orange-700 mt-1">
                        La cuenta se guardará como <strong>ACTIVA</strong>. Si después necesitas bloquearla, podrás hacerlo desde la lista de usuarios.
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 md:px-8 py-5 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3">
            <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition shadow-sm">
                Guardar usuario
            </button>

            <a href="{{ route('admin.usuarios.index') }}"
               class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition font-semibold">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection