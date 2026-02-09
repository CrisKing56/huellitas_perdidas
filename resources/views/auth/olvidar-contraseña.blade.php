@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl">
        
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                ¿Olvidaste tu contraseña?
            </h2>
            <p class="text-sm text-gray-500">
                Ingresa tu correo para enviarte un enlace de recuperación.
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Correo electrónico</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" required 
                           class="block w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition bg-gray-50/30" 
                           placeholder="tucorreo@ejemplo.com">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-lg shadow-orange-500/30 transition duration-300 transform hover:-translate-y-0.5">
                    Enviar enlace de recuperación
                </button>
            </div>

            <p class="text-center text-xs text-gray-400 mt-4">
                Revisa tu bandeja de entrada y spam.
            </p>
        </form>

        <div class="text-center border-t border-gray-100 pt-6">
            <p class="text-sm text-gray-400">
                ¿Recordaste tu contraseña? 
                <a href="{{ route('login') }}" class="font-bold text-orange-500 hover:text-orange-600 transition hover:underline ml-1">
                    Iniciar sesión
                </a>
            </p>
        </div>

    </div>
</div>
@endsection