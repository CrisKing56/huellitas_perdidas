@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl">

        <div class="text-center">
            <img src="{{ asset('img/logo1.png') }}" alt="Huellitas Perdidas" class="h-16 mx-auto mb-4">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                Restablecer contraseña
            </h2>
            <p class="text-sm text-gray-500">
                Ingresa una nueva contraseña para tu cuenta.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="correo" value="{{ old('correo', $correo) }}">

            @if(!empty($correo))
                <div class="rounded-xl bg-orange-50 border border-orange-100 px-4 py-3 text-sm text-orange-700">
                    Restableciendo contraseña para: <strong>{{ $correo }}</strong>
                </div>
            @endif

            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Nueva contraseña</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <input type="password"
                           name="password"
                           id="password"
                           required
                           minlength="8"
                           autocomplete="new-password"
                           class="block w-full pl-12 pr-12 py-3 rounded-xl border border-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition bg-gray-50/30"
                           placeholder="••••••••">

                    <button type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-orange-400 hover:text-orange-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Confirmar nueva contraseña</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           required
                           minlength="8"
                           autocomplete="new-password"
                           class="block w-full pl-12 pr-12 py-3 rounded-xl border border-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition bg-gray-50/30"
                           placeholder="••••••••">

                    <button type="button"
                            id="togglePasswordConfirmation"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-orange-400 hover:text-orange-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <p class="mt-2 text-xs text-gray-400">
                    Tu contraseña debe tener al menos 8 caracteres, una letra y un número.
                </p>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-lg shadow-orange-500/30 transition duration-300 transform hover:-translate-y-0.5">
                    Actualizar contraseña
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-400">
                ¿Ya tienes acceso?
                <a href="{{ route('login') }}" class="font-bold text-orange-500 hover:text-orange-600 transition hover:underline ml-1">
                    Iniciar sesión
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');

        togglePassword?.addEventListener('click', function () {
            password.type = password.type === 'password' ? 'text' : 'password';
        });

        togglePasswordConfirmation?.addEventListener('click', function () {
            passwordConfirmation.type = passwordConfirmation.type === 'password' ? 'text' : 'password';
        });
    });
</script>
@endsection