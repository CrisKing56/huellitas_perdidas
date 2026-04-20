@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center font-sans p-4">

    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden max-w-5xl w-full flex flex-col md:flex-row">
        
        <div class="hidden md:block w-1/2 bg-gray-100 relative">
            <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=1000&auto=format&fit=crop"
                 alt="Mascotas"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute bottom-0 left-0 right-0 p-12 bg-gradient-to-t from-black/80 via-black/40 to-transparent text-white">
                <h2 class="text-3xl font-bold mb-3">Únete a nuestra comunidad</h2>
                <p class="text-base opacity-90">
                    Ayuda a reunir familias con sus mascotas perdidas y conoce adopciones responsables.
                </p>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col justify-center">
            
            <div class="text-center md:text-left mb-10">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Iniciar sesión</h1>
                <p class="text-gray-500 text-sm">Bienvenido de nuevo, ingresa para continuar.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    <strong class="font-bold">¡Ups!</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Correo electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email"
                               name="correo"
                               value="{{ old('correo') }}"
                               placeholder="tu@email.com"
                               required
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-gray-50/50 text-gray-700 placeholder-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required
                               class="w-full pl-12 pr-12 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-gray-50/50 text-gray-700 placeholder-gray-400">

                        <button type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-orange-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-500 cursor-pointer hover:text-gray-700">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500 mr-2">
                        Recordarme
                    </label>

                    <a href="#" class="text-orange-500 hover:text-orange-600 font-medium hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition duration-300 shadow-lg shadow-orange-500/30 transform hover:-translate-y-0.5">
                    Entrar
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-400">o ingresa con</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <a href="{{ route('google.login') }}"
                   class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition text-gray-600 font-medium text-sm">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 w-5 mr-2" alt="Google">
                    Google
                </a>

                <a href="{{ route('facebook.login') }}">
                <button type="button"
                        class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition text-gray-600 font-medium text-sm">
                    <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" class="h-5 w-5 mr-2" alt="Facebook">
                    Facebook
                </button>
                </a>
            </div>

            <p class="text-center text-sm text-gray-500">
                ¿No tienes cuenta?
                <a href="{{ route('registro.usuario') }}" class="text-orange-500 font-bold hover:underline">Regístrate</a>
            </p>

            <p class="text-center text-sm text-gray-500 mt-3">
                ¿Tienes una veterinaria?
                <a href="{{ route('registro.veterinaria') }}" class="text-orange-500 font-bold hover:underline">
                    Registra tu veterinaria
                </a>
            </p>

            <p class="text-center text-sm text-gray-500 mt-3">
                ¿Tienes un refugio?
                <a href="{{ route('registro.refugio') }}" class="text-orange-500 font-bold hover:underline">
                    Registra tu refugio
                </a>
            </p>

            <div class="mt-6 rounded-2xl bg-orange-50 border border-orange-100 px-4 py-3 text-sm text-orange-700">
                Los usuarios institucionales aprobados serán enviados automáticamente a su panel correspondiente.
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        toggle?.addEventListener('click', function () {
            password.type = password.type === 'password' ? 'text' : 'password';
        });
    });
</script>
@endsection