@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-10">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3">Solicitud rechazada</h1>

            <p class="text-gray-600 leading-relaxed">
                Tu solicitud de
                <span class="font-semibold text-gray-900">
                    {{ $tipo === 'refugio' ? 'refugio' : 'veterinaria' }}
                </span>
                fue rechazada por el administrador.
            </p>

            @if($correo)
                <p class="text-gray-600 mt-2">
                    Correo asociado:
                    <span class="font-semibold text-gray-900">{{ $correo }}</span>
                </p>
            @endif

            @if(session('motivo_rechazo'))
                <div class="mt-6 rounded-2xl bg-red-50 border border-red-100 px-5 py-4 text-sm text-red-800 text-left">
                    <p class="font-semibold mb-2">Motivo del rechazo</p>
                    <p class="whitespace-pre-line">{{ session('motivo_rechazo') }}</p>
                </div>
            @endif

            <div class="mt-6 rounded-2xl bg-red-50 border border-red-100 px-5 py-4 text-sm text-red-800 text-left">
                <p class="font-semibold mb-2">¿Qué puedes hacer?</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Revisar cuidadosamente la información enviada.</li>
                    <li>Corregir los datos necesarios.</li>
                    <li>Volver a registrarte con información actualizada.</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a
                href="{{ $tipo === 'refugio' ? route('registro.refugio') : route('registro.veterinaria') }}"
                class="flex-1 text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-5 rounded-xl transition"
            >
                Registrar nuevamente
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