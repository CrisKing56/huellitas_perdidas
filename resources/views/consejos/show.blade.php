@extends('layout.app')

@section('content')
    <div class="relative w-full h-96 lg:h-[28rem] bg-gray-900 flex items-end justify-center">
        @if($consejo->imagenes->count() > 0)
            <img src="{{ asset('storage/' . $consejo->imagenes->first()->url) }}" alt="{{ $consejo->titulo }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
        @else
            <div class="absolute inset-0 w-full h-full bg-gray-800 object-cover opacity-60"></div>
        @endif
        
        <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-transparent"></div>

        <div class="relative w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 z-10">
            <a href="{{ route('consejos.index') }}" class="inline-flex items-center text-orange-500 hover:text-orange-700 font-semibold mb-6">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a consejos
            </a>

            <div class="mb-4">
                <span class="px-3 py-1 text-sm font-bold bg-orange-500 text-white rounded-md shadow-sm">
                    {{ $consejo->categoria ? $consejo->categoria->nombre : 'General' }}
                </span>
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-4">
                {{ $consejo->titulo }}
            </h1>

            <div class="flex items-center text-sm text-gray-600 font-medium">
                <span>Por <span class="text-gray-900 font-bold">{{ $consejo->organizacion->nombre }}</span></span>
                <span class="mx-3">•</span>
                <span>{{ \Carbon\Carbon::parse($consejo->creado_en)->translatedFormat('d \d\e F, Y') }}</span>
                <span class="mx-3">•</span>
                <span>5 min lectura</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex items-center space-x-6 border-b border-gray-200 pb-6 mb-8 text-gray-500 font-medium">
            <button class="flex items-center hover:text-orange-500 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                Guardar
            </button>
            <button class="flex items-center hover:text-orange-500 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                Compartir
            </button>
        </div>

        <div class="prose prose-lg prose-orange max-w-none text-gray-700 leading-relaxed mb-12">
            {!! nl2br(e($consejo->contenido)) !!}
        </div>

        @if($consejo->imagenes->count() > 1)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                @foreach($consejo->imagenes->skip(1) as $imagenExtra)
                    <img src="{{ asset('storage/' . $imagenExtra->url) }}" alt="Imagen ilustrativa" class="rounded-2xl shadow-sm w-full h-64 object-cover">
                @endforeach
            </div>
        @endif

        <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded-r-xl mb-12">
            <p class="text-orange-900 font-medium italic">
                Recuerda que la consulta con tu veterinario es fundamental para elegir el método de prevención más adecuado para tu mascota, considerando su edad, peso, estado de salud y estilo de vida. La prevención constante es la clave para mantener a tu mejor amigo feliz y saludable.
            </p>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-8 text-center mt-16">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">¿Necesitas ayuda profesional?</h3>
            <p class="text-gray-500 mb-6">Los especialistas de <span class="font-bold text-gray-700">{{ $consejo->organizacion->nombre }}</span> están disponibles para responder tus dudas.</p>
            
            <a href="#" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-lg shadow-sm transition-colors duration-200">
                Contactar a un especialista
            </a>
        </div>

    </div>
@endsection