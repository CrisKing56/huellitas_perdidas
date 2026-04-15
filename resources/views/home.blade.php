@extends('layout.app')

@section('content')
@php
    $slides = [
        [
            'image' => 'https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&w=1950&q=80',
            'title' => 'Reporta o busca una mascota fácilmente.',
            'subtitle' => 'Conectando familias con mascotas perdidas, encontradas y en adopción.',
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=1950&q=80',
            'title' => 'Da visibilidad a cada huellita que necesita ayuda.',
            'subtitle' => 'Publica reportes, comparte información y ayuda a reencontrarlas.',
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=1950&q=80',
            'title' => 'Encuentra compañeros que buscan un hogar.',
            'subtitle' => 'Adopta con más confianza y conoce mascotas que esperan una familia.',
        ],
    ];

    $mapaEspecie = [
        1 => 'Perro',
        2 => 'Gato',
    ];
@endphp

<style>
    .hero-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.9s ease-in-out;
    }

    .hero-slide.active {
        opacity: 1;
        z-index: 1;
    }

    .hero-dark-overlay {
        background:
            linear-gradient(to right, rgba(0, 0, 0, 0.20), rgba(0, 0, 0, 0.35)),
            linear-gradient(to top, rgba(0, 0, 0, 0.10), rgba(0, 0, 0, 0.10));
    }

    .hero-dot.active {
        background-color: white;
        transform: scale(1.15);
        opacity: 1;
    }
</style>

<header class="relative w-full h-[560px] overflow-hidden">
    @foreach($slides as $index => $slide)
        <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-slide>
            <img src="{{ $slide['image'] }}"
                 alt="Slide {{ $index + 1 }}"
                 class="absolute w-full h-full object-cover brightness-[0.65]">
        </div>
    @endforeach

    <div class="absolute inset-0 hero-dark-overlay z-10"></div>

    <div class="relative z-20 h-full max-w-7xl mx-auto px-6">
        <div class="h-full flex items-center">
            <div class="max-w-3xl">
                <span class="inline-flex items-center rounded-full bg-white/10 border border-white/15 backdrop-blur-sm px-4 py-2 text-sm font-semibold text-white/95 mb-5">
                    Plataforma de apoyo para mascotas y familias
                </span>

                <div id="hero-texts">
                    @foreach($slides as $index => $slide)
                        <div class="{{ $index === 0 ? 'block' : 'hidden' }}" data-slide-text>
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4 drop-shadow-[0_4px_16px_rgba(0,0,0,0.45)]">
                                {{ $slide['title'] }}
                            </h1>
                            <p class="text-white/90 text-lg md:text-xl mb-8 max-w-2xl drop-shadow-[0_2px_10px_rgba(0,0,0,0.4)]">
                                {{ $slide['subtitle'] }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    @guest
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center bg-primary hover:bg-orange-600 text-white font-bold py-3.5 px-8 rounded-xl shadow-xl shadow-orange-500/20 transition transform hover:-translate-y-0.5">
                            Reportar mascota perdida
                        </a>
                    @endguest

                    @auth
                        <a href="{{ route('mascotas.create') }}"
                           class="inline-flex items-center justify-center bg-primary hover:bg-orange-600 text-white font-bold py-3.5 px-8 rounded-xl shadow-xl shadow-orange-500/20 transition transform hover:-translate-y-0.5">
                            Reportar mascota perdida
                        </a>
                    @endauth

                    <a href="{{ route('adopciones.index') }}"
                       class="inline-flex items-center justify-center bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/20 text-white font-semibold py-3.5 px-8 rounded-xl transition">
                        Ver mascotas en adopción
                    </a>
                </div>

                <div class="mt-8 flex items-center gap-3">
                    @foreach($slides as $index => $slide)
                        <button type="button"
                                class="hero-dot h-2.5 w-2.5 rounded-full bg-white/40 transition"
                                data-dot
                                data-index="{{ $index }}"
                                aria-label="Ir al slide {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <button type="button"
            id="hero-prev"
            class="absolute left-4 md:left-6 top-1/2 -translate-y-1/2 z-20 h-11 w-11 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white flex items-center justify-center transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>

    <button type="button"
            id="hero-next"
            class="absolute right-4 md:right-6 top-1/2 -translate-y-1/2 z-20 h-11 w-11 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white flex items-center justify-center transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
</header>

<section class="max-w-7xl mx-auto px-6 py-14">
    <div class="flex items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Mascotas perdidas recientemente</h2>
            <p class="text-sm text-gray-500 mt-1">Publicaciones más recientes para ayudarte a identificar y compartir.</p>
        </div>

        <a href="{{ route('mascotas.index2') }}"
           class="hidden md:inline-flex items-center text-sm font-semibold text-orange-500 hover:text-orange-600 transition">
            Ver todas
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @forelse($mascotasRecientes as $mascota)
            @php
                $especieTexto = $mapaEspecie[$mascota->especie_id] ?? 'Mascota';
            @endphp

            <a href="{{ route('extravios.show', $mascota->id_publicacion) }}">
                <div class="bg-white rounded-3xl shadow-sm hover:shadow-lg transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">
                    
                    <div class="h-64 relative overflow-hidden bg-gray-100">
                        @if($mascota->fotoPrincipal)
                            <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}"
                                 class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                <span class="text-sm">Sin imagen</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>

                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold shadow-sm uppercase tracking-wide bg-red-100 text-red-600">
                            Perdida
                        </span>

                        <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm bg-white/90 text-gray-700 backdrop-blur-sm">
                            {{ $especieTexto }}
                        </span>

                        <div class="absolute bottom-3 right-3 flex items-center gap-2 px-3 py-2 rounded-xl bg-black/30 backdrop-blur-sm">
                            <img src="{{ asset('img/logo1.png') }}"
                                 alt="Huellitas Perdidas"
                                 class="h-6 w-6 object-contain brightness-0 invert opacity-95">
                            <span class="text-white text-[11px] font-bold tracking-wide">
                                Huellitas Perdidas
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $mascota->nombre }}</h3>
                        
                        <p class="text-sm text-gray-500 mb-3 min-h-[44px] leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($mascota->descripcion, 85) }}
                        </p>
                        
                        <div class="mt-auto flex items-center text-gray-500 text-xs font-medium mb-4">
                            <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $mascota->colonia_barrio }}
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <span class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-50 text-orange-600 font-semibold py-2.5 border border-orange-100 group-hover:bg-orange-500 group-hover:text-white group-hover:border-orange-500 transition">
                                Reportar avistamiento
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-1 md:col-span-2 xl:col-span-4 text-center py-12 rounded-3xl border border-dashed border-gray-200 bg-white">
                <p class="text-gray-500 text-lg">No hay mascotas perdidas recientemente.</p>
            </div>
        @endforelse
    </div>
</section>

<section class="bg-white py-14 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Mascotas que necesitan un hogar</h2>
                <p class="text-sm text-gray-500 mt-1">Conoce algunas publicaciones recientes de adopción.</p>
            </div>

            <a href="{{ route('adopciones.index') }}"
               class="hidden md:inline-flex items-center text-sm font-semibold text-green-600 hover:text-green-700 transition">
                Ver todas
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @forelse($adopcionesRecientes as $adopcion)
                @php
                    $especieTexto = $mapaEspecie[$adopcion->especie_id] ?? 'Mascota';
                @endphp

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition flex flex-col group h-full">
                    <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}">
                        <div class="relative h-64 overflow-hidden bg-gray-100">
                            @if($adopcion->fotoPrincipal)
                                <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

                            <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                En adopción
                            </span>

                            <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm bg-white/90 text-gray-700 backdrop-blur-sm">
                                {{ $especieTexto }}
                            </span>

                            <div class="absolute bottom-3 right-3 flex items-center gap-2 px-3 py-2 rounded-xl bg-black/30 backdrop-blur-sm">
                                <img src="{{ asset('img/logo1.png') }}"
                                     alt="Huellitas Perdidas"
                                     class="h-6 w-6 object-contain brightness-0 invert opacity-95">
                                <span class="text-white text-[11px] font-bold tracking-wide">
                                    Huellitas Perdidas
                                </span>
                            </div>
                        </div>
                    </a>

                    <div class="p-5 flex-grow flex flex-col">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <h3 class="font-bold text-gray-900 text-lg truncate pr-2">{{ $adopcion->nombre }}</h3>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full whitespace-nowrap font-medium">
                                {{ $adopcion->edad_anios ?? 'N/D' }} años
                            </span>
                        </div>
                        
                        <p class="text-xs text-gray-500 mb-3 font-semibold uppercase tracking-wide">
                            {{ $especieTexto }}
                            @if($adopcion->otra_raza)
                                • {{ \Illuminate\Support\Str::limit($adopcion->otra_raza, 18) }}
                            @endif
                        </p>
                        
                        <p class="text-sm text-gray-600 mb-4 flex-grow min-h-[44px] leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($adopcion->descripcion, 90) }}
                        </p>
                        
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-5">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="truncate">{{ $adopcion->colonia_barrio }}</span>
                        </div>
                        
                        <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}"
                           class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-green-50 text-green-600 font-semibold py-2.5 border border-green-100 group-hover:bg-green-600 group-hover:text-white group-hover:border-green-600 transition">
                            ¡Adoptar!
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 xl:col-span-4 text-center py-12 rounded-3xl border border-dashed border-gray-200 bg-gray-50">
                    <p class="text-gray-500 text-lg">No hay mascotas en adopción disponibles.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slides = document.querySelectorAll('[data-slide]');
        const texts = document.querySelectorAll('[data-slide-text]');
        const dots = document.querySelectorAll('[data-dot]');
        const prevBtn = document.getElementById('hero-prev');
        const nextBtn = document.getElementById('hero-next');

        if (!slides.length) return;

        let current = 0;
        let interval = null;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });

            texts.forEach((text, i) => {
                text.classList.toggle('hidden', i !== index);
                text.classList.toggle('block', i === index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });

            current = index;
        }

        function nextSlide() {
            const next = (current + 1) % slides.length;
            showSlide(next);
        }

        function prevSlide() {
            const prev = (current - 1 + slides.length) % slides.length;
            showSlide(prev);
        }

        function startAutoPlay() {
            interval = setInterval(nextSlide, 5000);
        }

        function resetAutoPlay() {
            clearInterval(interval);
            startAutoPlay();
        }

        nextBtn?.addEventListener('click', function () {
            nextSlide();
            resetAutoPlay();
        });

        prevBtn?.addEventListener('click', function () {
            prevSlide();
            resetAutoPlay();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', function () {
                const index = Number(this.dataset.index);
                showSlide(index);
                resetAutoPlay();
            });
        });

        showSlide(0);
        startAutoPlay();
    });
</script>
@endsection