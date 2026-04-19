@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-10">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3">Revisa tu correo</h1>

            <p class="text-gray-600 leading-relaxed">
                Tu solicitud de
                <span class="font-semibold text-gray-900">
                    {{ $tipo === 'refugio' ? 'refugio' : 'veterinaria' }}
                </span>
                fue enviada correctamente.
            </p>

            @if($correo)
                <p class="text-gray-600 mt-2">
                    Enviamos un enlace de verificación a:
                    <span class="font-semibold text-gray-900">{{ $correo }}</span>
                </p>
            @endif

            <div class="mt-6 rounded-2xl bg-orange-50 border border-orange-100 px-5 py-4 text-sm text-orange-800 text-left">
                <p class="font-semibold mb-2">Sigue estos pasos:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Abre el correo de verificación.</li>
                    <li>Da clic en el enlace para confirmar tu correo.</li>
                    <li>Después, espera la revisión del administrador.</li>
                    <li>Solo cuando tu solicitud sea aprobada podrás iniciar sesión.</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8">
            <form action="{{ route('registro.organizacion.reenviar') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Reenviar verificación a este correo
                    </label>
                    <input
                        type="email"
                        name="correo"
                        value="{{ old('correo', $correo) }}"
                        placeholder="tu@email.com"
                        class="w-full rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-orange-500 py-3 px-4 text-gray-800 bg-gray-100"
                    >
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        type="submit"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-5 rounded-xl transition"
                    >
                        Reenviar correo
                    </button>

                    <a
                        href="{{ route('login') }}"
                        class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-5 rounded-xl transition"
                    >
                        Ir al login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection