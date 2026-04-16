@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Verifica tu correo</h1>
            <p class="text-sm text-gray-500 mt-2">
                Te enviamos un enlace de verificación a tu correo electrónico.
                Debes confirmar tu cuenta para continuar.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('status') === 'verification-link-sent')
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Te enviamos un nuevo enlace de verificación.
            </div>
        @endif

        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-sm text-gray-700 mb-6">
            <p class="font-semibold text-orange-600 mb-1">¿No ves el correo?</p>
            <ul class="list-disc ml-5 space-y-1">
                <li>Revisa tu bandeja de spam o promociones.</li>
                <li>Confirma que escribiste bien tu correo.</li>
                <li>Solicita otro enlace si no te llegó.</li>
            </ul>
        </div>

        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition">
                    Reenviar correo de verificación
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>
@endsection