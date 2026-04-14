@extends('layout.app')

@section('content')

    <header class="relative w-full h-[500px] overflow-hidden">
        <img src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&w=1950&q=80" class="absolute w-full h-full object-cover">
        <div class="absolute inset-0 hero-overlay flex flex-col items-center justify-center text-center px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">Reporta o busca una mascota fácilmente.</h1>
            <p class="text-white text-lg mb-8">¡Conectando familias con mascotas perdidas!</p>
            <button class="bg-primary hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition transform hover:scale-105">
                @guest
                    <a href="{{ route('login')}}">
                        Reportar mascota perdida
                    </a>

                @endguest

                @auth
                <a href="{{ route('mascotas.create')}}">
                    Reportar mascota perdida
                </a>
                @endauth
                
            </button>
        </div>
    </header>

    <section class="container mx-auto px-6 py-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Mascotas perdidas recientemente</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($mascotasRecientes as $mascota)
                <a href="{{ route('extravios.show', $mascota->id_publicacion) }}">
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">
                        
                        <div class="h-64 relative overflow-hidden bg-gray-100">
                            @if($mascota->fotoPrincipal)
                                <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}"
                                     class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                    <span class="text-sm">Sin imagen</span>
                                </div>
                            @endif

                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold shadow-sm uppercase tracking-wide bg-red-100 text-red-600">
                                Perdida
                            </span>
                        </div>

                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-base mb-1">{{ $mascota->nombre }}</h3>
                            
                            <p class="text-sm text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                                {{ $mascota->descripcion }}
                            </p>
                            
                            <div class="mt-auto flex items-center text-gray-500 text-xs font-medium mb-4">
                                <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $mascota->colonia_barrio }}
                            </div>

                            <span class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-orange-600 transition group mt-auto pt-4 border-t border-gray-50">
                                Reportar avistamiento
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-12">
                    <p class="text-gray-500 text-lg">No hay mascotas perdidas recientemente.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-white py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Mascotas que necesitan un hogar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($adopcionesRecientes as $adopcion)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col group h-full">
                        <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}">
                            <div class="relative h-64 overflow-hidden">
                                @if($adopcion->fotoPrincipal)
                                    <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
                                <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                    En adopción
                                </span>
                            </div>
                        </a>

                        <div class="p-5 flex-grow flex flex-col">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-bold text-gray-900 text-lg truncate pr-2">{{ $adopcion->nombre }}</h3>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $adopcion->edad_anios ?? 'N/D' }} años</span>
                            </div>
                            
                            <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">
                                {{ $adopcion->especie_id == 1 ? 'Perro' : 'Gato' }}
                                @if($adopcion->otra_raza)
                                    • {{ \Illuminate\Support\Str::limit($adopcion->otra_raza, 15) }}
                                @endif
                            </p>
                            
                            <p class="text-sm text-gray-600 mb-4 flex-grow line-clamp-2">
                                {{ $adopcion->descripcion }}
                            </p>
                            
                            <div class="flex items-center gap-1 text-xs text-gray-500 mb-6">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">{{ $adopcion->colonia_barrio }}</span>
                            </div>
                            
                            <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}" class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-green-600 transition group mt-auto pt-4 border-t border-gray-50">
                                ¡Adoptar!
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-12">
                        <p class="text-gray-500 text-lg">No hay mascotas en adopción disponibles.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

@endsection