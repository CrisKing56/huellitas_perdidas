@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h1 class="text-gray-500 uppercase tracking-wide text-sm font-semibold mb-4">
            VETERINARIAS EN OCOSINGO
        </h1>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">

            <div class="flex items-center gap-2 w-full md:max-w-xl">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-full bg-white
                                  focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm shadow-sm"
                           placeholder="Buscar veterinarias...">
                </div>

                <a href="{{ route('refugios.index') }}"
                   class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-full text-sm font-medium transition shadow-sm">
                    Refugios
                </a>
            </div>

            <a href="{{ route('registro.veterinaria') }}"
               class="text-gray-600 hover:text-orange-500 text-sm font-medium flex items-center gap-1">
                Publicar veterinaria
                <span class="text-lg leading-none">+</span>
            </a>
        </div>

        <p class="text-gray-500 text-sm mb-6">
            {{ count($veterinarias ?? []) }} veterinarias encontradas
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse(($veterinarias ?? []) as $vet)
                @php
                    $direccion = collect([
                        $vet->calle_numero,
                        $vet->colonia,
                        $vet->ciudad,
                    ])->filter()->implode(', ');

                    $imagen = $vet->imagen
                        ? asset('storage/' . $vet->imagen)
                        : 'https://images.unsplash.com/photo-1581888227599-779811939961?auto=format&fit=crop&w=900&q=60';
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">

                    <div class="relative h-48">
                        <img src="{{ $imagen }}" alt="{{ $vet->nombre }}"
                             class="w-full h-full object-cover">

                        <span class="absolute top-4 left-4 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                            Disponible
                        </span>
                    </div>

                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $vet->nombre }}</h3>

                        <div class="space-y-2 mb-6 text-sm text-gray-500">
                            <div>{{ $direccion ?: 'Dirección no registrada' }}</div>
                            <div>{{ $vet->telefono }}</div>
                            <div>{{ \Illuminate\Support\Str::limit($vet->descripcion, 80) }}</div>
                        </div>
                        <a href="{{ route('veterinarias.show', $vet->id_organizacion) }}"
                        class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center font-medium py-2.5 rounded-lg transition">
                            Ver detalles
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-6 rounded-xl border text-gray-600">
                    No hay veterinarias registradas por el momento.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection