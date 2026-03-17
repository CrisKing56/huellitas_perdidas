@extends('layout.app')

@section('content')
    <div class="relative w-full h-80 bg-gray-900 flex items-end">
        @if($refugio->fotos->count() > 0)
            <img src="{{ asset('storage/' . $refugio->fotos->first()->url) }}" class="absolute inset-0 w-full h-full object-cover opacity-50">
        @else
            <div class="absolute inset-0 w-full h-full bg-orange-800 opacity-50"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>

        <div class="relative w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 z-10">
            <a href="{{ route('refugios.index') }}" class="inline-flex items-center text-gray-300 hover:text-white font-semibold mb-4 text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a refugios
            </a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2">{{ $refugio->nombre }}</h1>
            <p class="text-gray-300 flex items-center text-lg">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                {{ $refugio->direccion->ciudad }}, {{ $refugio->direccion->estado }}
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2 space-y-8">
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b pb-2">Acerca de nosotros</h2>
                    <p class="text-gray-700 leading-relaxed text-lg whitespace-pre-line">{{ $refugio->descripcion }}</p>
                </section>

                @if($refugio->refugioDetalle->requisitos_adopcion)
                <section class="bg-orange-50 rounded-xl p-6 border border-orange-100">
                    <h2 class="text-xl font-bold text-orange-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Requisitos de Adopción
                    </h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $refugio->refugioDetalle->requisitos_adopcion }}</p>
                </section>
                @endif

                @if($refugio->fotos->count() > 1)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Conoce nuestras instalaciones</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($refugio->fotos->skip(1) as $foto)
                            <div class="h-40 rounded-lg overflow-hidden shadow-sm">
                                <img src="{{ asset('storage/' . $foto->url) }}" class="w-full h-full object-cover hover:scale-110 transition-transform">
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Información de Contacto</h3>
                    
                    <div class="space-y-4 text-gray-600">
                        <p class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-orange-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span><strong class="block text-gray-900">Teléfono</strong> {{ $refugio->telefono }}</span>
                        </p>
                        @if($refugio->whatsapp)
                        <p class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-green-500 mt-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"></path></svg>
                            <span><strong class="block text-gray-900">WhatsApp</strong> <a href="https://wa.me/52{{ $refugio->whatsapp }}" target="_blank" class="text-green-600 hover:underline">{{ $refugio->whatsapp }}</a></span>
                        </p>
                        @endif
                        <p class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-orange-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span><strong class="block text-gray-900">Dirección</strong> {{ $refugio->direccion->calle_numero }}, {{ $refugio->direccion->colonia }}. C.P. {{ $refugio->direccion->codigo_postal }}</span>
                        </p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md border border-green-100 p-6">
                    <h3 class="text-lg font-bold text-green-900 mb-2">Apoya nuestra labor</h3>
                    @if($refugio->refugioDetalle->acepta_donaciones)
                        <p class="text-green-800 text-sm mb-4">Aceptamos donaciones en especie para seguir cuidando a nuestros peluditos.</p>
                        @if($refugio->refugioDetalle->tipo_donaciones)
                            <div class="bg-white/60 p-3 rounded-lg text-sm text-green-900 font-medium">
                                <strong>Necesitamos:</strong> {{ $refugio->refugioDetalle->tipo_donaciones }}
                            </div>
                        @endif
                    @else
                        <p class="text-green-800 text-sm">Por el momento no estamos recibiendo donaciones en especie. ¡Pero puedes ayudar compartiendo a nuestros rescatados!</p>
                    @endif
                </div>

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-center">
                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Nuestra Familia Actual</span>
                    <div class="flex justify-center space-x-8">
                        <div>
                            <span class="block text-3xl font-black text-gray-900">{{ $refugio->refugioDetalle->capacidad_perros }}</span>
                            <span class="text-sm text-gray-600">Perros</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-black text-gray-900">{{ $refugio->refugioDetalle->capacidad_gatos }}</span>
                            <span class="text-sm text-gray-600">Gatos</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
