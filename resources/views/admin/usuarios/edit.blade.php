@extends('admin.layout')

@section('title', 'Editar usuario')
@section('top_title', 'Panel Administrador')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Editar usuario</h1>
    <p class="text-gray-600 text-sm mb-6">Actualiza la información del usuario.</p>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
            <input name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Correo</label>
            <input name="correo" value="{{ old('correo', $usuario->correo) }}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
            <input name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Rol</label>
                <select name="rol" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    @foreach(['USUARIO','ADMIN','VETERINARIA','REFUGIO'] as $rol)
                        <option value="{{ $rol }}" @selected(old('rol', $usuario->rol) === $rol)>{{ $rol }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    @foreach(['ACTIVA','SUSPENDIDA','ELIMINADA'] as $estado)
                        <option value="{{ $estado }}" @selected(old('estado', $usuario->estado) === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nueva contraseña (opcional)</label>
            <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Dejar vacío para no cambiar">
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-orange-600">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection
