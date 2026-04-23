@extends('layout.app')

@section('content')
<div class="bg-gray-50 flex items-center justify-center min-h-screen font-sans w-full p-4">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden max-w-5xl w-full flex flex-col md:flex-row">

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
                <a href="{{ route('inicio') }}" class="text-sm text-gray-500 hover:text-orange-500">Volver al inicio</a>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear cuenta</h1>
            <p class="text-gray-500 text-sm mb-8">Regístrate para reportar, buscar y ayudar.</p>

            @if ($errors->any())
                <div class="mb-5 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <strong class="font-semibold">Corrige lo siguiente:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('registro.store') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        placeholder="Juan Pérez"
                        required
                        maxlength="120"
                        autocomplete="name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                    >
                    @error('nombre')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input
                        type="email"
                        name="correo"
                        value="{{ old('correo') }}"
                        placeholder="tu@email.com"
                        required
                        maxlength="120"
                        autocomplete="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                    >
                    @error('correo')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input
                        type="tel"
                        name="telefono"
                        value="{{ old('telefono') }}"
                        placeholder="9191234567"
                        required
                        maxlength="10"
                        inputmode="numeric"
                        autocomplete="tel"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Solo números, 10 dígitos.</p>
                    @error('telefono')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                        >
                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-orange-500 transition"
                            aria-label="Mostrar u ocultar contraseña"
                        >
                            <svg id="iconPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-3 p-3 rounded-xl bg-gray-50 border border-gray-200">
                        <p class="text-xs font-semibold text-gray-700 mb-2">La contraseña debe cumplir:</p>
                        <ul class="space-y-1 text-xs">
                            <li id="rule-length" class="text-gray-500">• Mínimo 8 caracteres</li>
                            <li id="rule-letter" class="text-gray-500">• Al menos una letra</li>
                            <li id="rule-number" class="text-gray-500">• Al menos un número</li>
                        </ul>
                    </div>

                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="••••••••"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                        >
                        <button
                            type="button"
                            id="togglePasswordConfirmation"
                            class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-orange-500 transition"
                            aria-label="Mostrar u ocultar confirmación de contraseña"
                        >
                            <svg id="iconPasswordConfirmation" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="password-match-message" class="text-xs mt-2 text-gray-500">Confirma la misma contraseña.</p>
                </div>

                <div class="flex items-start text-xs text-gray-500 mt-2">
                    <input
                        type="checkbox"
                        name="terminos"
                        value="1"
                        required
                        class="mr-2 mt-0.5 rounded text-orange-500 focus:ring-orange-500"
                    >
                    <span>
                        Acepto los
                        <a href="#" class="text-orange-500 underline">términos y condiciones</a>.
                    </span>
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition duration-300 shadow-md mt-4">
                    Crear cuenta
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="text-orange-500 font-bold hover:underline">Inicia sesión</a>
            </p>

            <p class="text-center text-sm text-gray-500 mt-3">
                ¿Quieres registrar una veterinaria?
                <a href="{{ route('registro.veterinaria') }}" class="text-orange-500 font-bold hover:underline">
                    Ir al formulario de veterinaria
                </a>
            </p>

            <p class="text-center text-sm text-gray-500 mt-3">
                ¿Tienes un refugio?
                <a href="{{ route('registro.refugio') }}" class="text-orange-500 font-bold hover:underline">
                    Ir al formulario de refugio
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

        const ruleLength = document.getElementById('rule-length');
        const ruleLetter = document.getElementById('rule-letter');
        const ruleNumber = document.getElementById('rule-number');
        const matchMessage = document.getElementById('password-match-message');

        function toggleVisibility(input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        togglePassword?.addEventListener('click', function () {
            toggleVisibility(password);
        });

        togglePasswordConfirmation?.addEventListener('click', function () {
            toggleVisibility(passwordConfirmation);
        });

        function markRule(element, valid) {
            element.classList.remove('text-gray-500', 'text-red-500', 'text-green-600');
            element.classList.add(valid ? 'text-green-600' : 'text-red-500');
        }

        function validatePasswordRules() {
            const value = password.value || '';

            markRule(ruleLength, value.length >= 8);
            markRule(ruleLetter, /[A-Za-z]/.test(value));
            markRule(ruleNumber, /\d/.test(value));
        }

        function validatePasswordMatch() {
            if (!passwordConfirmation.value) {
                matchMessage.textContent = 'Confirma la misma contraseña.';
                matchMessage.className = 'text-xs mt-2 text-gray-500';
                return;
            }

            if (password.value === passwordConfirmation.value) {
                matchMessage.textContent = 'Las contraseñas coinciden.';
                matchMessage.className = 'text-xs mt-2 text-green-600';
            } else {
                matchMessage.textContent = 'Las contraseñas no coinciden.';
                matchMessage.className = 'text-xs mt-2 text-red-500';
            }
        }

        password?.addEventListener('input', function () {
            validatePasswordRules();
            validatePasswordMatch();
        });

        passwordConfirmation?.addEventListener('input', validatePasswordMatch);
    });
</script>
@endsection