@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-10">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3">Correo verificado</h1>

            <p class="text-gray-600 leading-relaxed">
                Tu correo fue verificado correctamente y tu solicitud de
                <span class="font-semibold text-gray-900">
                    {{ $tipo === 'refugio' ? 'refugio' : 'veterinaria' }}
                </span>
                ya quedó registrada.
            </p>

            @if($correo)
                <p class="text-gray-600 mt-2">
                    Correo validado:
                    <span class="font-semibold text-gray-900">{{ $correo }}</span>
                </p>
            @endif

            <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 px-5 py-4 text-sm text-blue-800 text-left">
                <p class="font-semibold mb-2">¿Qué sigue ahora?</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Tu solicitud ya fue recibida correctamente.</li>
                    <li>Un administrador revisará la información registrada.</li>
                    <li>Mientras esté en revisión, todavía no podrás acceder al panel.</li>
                    <li>Cuando sea aprobada, ya podrás iniciar sesión normalmente.</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a
                href="{{ route('login') }}"
                class="flex-1 text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-5 rounded-xl transition"
            >
                Ir al inicio de sesión
            </a>

            <a
                href="{{ route('inicio') }}"
                class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-5 rounded-xl transition"
            >
                Volver al inicio
            </a>
        </div>
    </div>
</div>
@endsection