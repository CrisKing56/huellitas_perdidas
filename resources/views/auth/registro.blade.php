@extends('layout.app')

@section('content')

    <div class="bg-gray-50 flex items-center justify-center min-h-screen font-sans w-full">
        
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden max-w-5xl w-full flex flex-col md:flex-row m-4">
            
            <div class="hidden md:block w-1/2 bg-gray-100 relative">
                <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=1000&auto=format&fit=crop" 
                     alt="Perro" 
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-black/70 to-transparent text-white">
                    <h2 class="text-2xl font-bold mb-2">Únete a nuestra comunidad</h2>
                    <p class="text-sm opacity-90">Ayuda a reunir familias en Ocosingo.</p>
                </div>
            </div>

        <div class="w-full md:w-1/2 p-8 md:p-12 overflow-y-auto">
            <div class="text-right mb-6">
                <a href="/" class="text-sm text-gray-500 hover:text-orange-500">Volver al inicio</a>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear cuenta</h1>
            <p class="text-gray-500 text-sm mb-8">Regístrate para reportar, buscar y ayudar.</p>

            <form action="{{ route('registro.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" name="nombre" placeholder="Juan Pérez" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input type="email" name="correo" placeholder="tu@email.com" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    @error('correo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" placeholder="919 123 4567" required maxlength="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                </div>

                <div class="flex items-center text-xs text-gray-500 mt-2">
                    <input type="checkbox" required class="mr-2 rounded text-orange-500 focus:ring-orange-500">
                    <span>Acepto los <a href="#" class="text-orange-500 underline">términos y condiciones</a>.</span>
                </div>

                <button type="submit" 
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-md mt-4">
                    Crear cuenta
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                ¿Ya tienes cuenta? <a href="{{route ('login')}}" class="text-orange-500 font-bold hover:underline">Inicia sesión</a>
            </p>

            <p class="text-center text-sm text-gray-500 mt-3">
                ¿Quieres registrar una veterinaria?
                <a href="{{ route('registro.veterinaria') }}" class="text-orange-500 font-bold hover:underline">
                    Ir al formulario de veterinaria
                </a>
            </p>
        </div>
    </div>

@endsection