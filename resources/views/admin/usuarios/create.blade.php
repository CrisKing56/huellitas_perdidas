@extends('admin.layout')

@section('title','Crear usuario - Huellitas Perdidas')
@section('panel_title','Panel Administrador')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Agregar usuario</h1>
    <p class="text-gray-600 text-sm mt-1">Crea un usuario con rol y estado.</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
            <input name="nombre" value="{{ old('nombre') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Correo</label>
            <input type="email" name="correo" value="{{ old('correo') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
            <input name="telefono" value="{{ old('telefono') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                <select name="rol" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    <option value="USUARIO">USUARIO</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="VETERINARIA">VETERINARIA</option>
                    <option value="REFUGIO">REFUGIO</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    <option value="ACTIVA">ACTIVA</option>
                    <option value="SUSPENDIDA">SUSPENDIDA</option>
                    <option value="ELIMINADA">ELIMINADA</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
            <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres.</p>
        </div>

        <div class="flex gap-3">
            <button class="bg-primary hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition">
                Guardar
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
