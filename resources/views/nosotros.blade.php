@extends('layout.app')

@section('content')
    <div class="relative bg-gray-900 pt-24 pb-32 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=2069&auto=format&fit=crop" alt="Mascotas felices" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">
                Sobre Huellitas Perdidas
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-300">
                Una plataforma dedicada a reunir mascotas con sus familias y promover la adopción responsable.
            </p>
        </div>
    </div>

    <div class="bg-gray-50 py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center mb-20 bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-100">
                <div>
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                            <svg class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h2 class="ml-4 text-3xl font-extrabold text-gray-900">Quiénes somos</h2>
                    </div>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Somos una comunidad comprometida con el bienestar animal. Nuestro equipo está formado por amantes de los animales, voluntarios y profesionales dedicados a hacer la diferencia en la vida de las mascotas perdidas y en situación de adopción.
                    </p>
                </div>
                <div class="mt-10 lg:mt-0">
                    <img src="https://images.unsplash.com/photo-1528301721190-186c3bd85418?q=80&w=1000&auto=format&fit=crop" alt="Voluntarios con perros" class="rounded-2xl shadow-lg object-cover h-80 w-full">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
                
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Qué hacemos</h3>
                    <p class="text-gray-600">Facilitamos el reencuentro de mascotas perdidas con sus dueños mediante un sistema de publicación y búsqueda eficiente. También promovemos la adopción responsable conectando a mascotas en busca de hogar con familias amorosas.</p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Por qué lo hacemos</h3>
                    <p class="text-gray-600">Creemos que cada mascota merece un hogar amoroso y una segunda oportunidad. Sabemos el dolor que causa perder una mascota y la alegría de encontrarla, por eso trabajamos para facilitar esos reencuentros.</p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Cómo lo hacemos</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-orange-500 mr-2">•</span> Plataforma digital accesible para publicar y buscar mascotas
                        </li>
                        <li class="flex items-start">
                            <span class="text-orange-500 mr-2">•</span> Red de veterinarias y refugios colaboradores
                        </li>
                        <li class="flex items-start">
                            <span class="text-orange-500 mr-2">•</span> Comunidad activa de usuarios comprometidos
                        </li>
                        <li class="flex items-start">
                            <span class="text-orange-500 mr-2">•</span> Educación y consejos sobre cuidado responsable
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Dónde nos ubicamos</h3>
                    <p class="text-gray-600 mb-6">Actualmente operamos principalmente en Ocosingo, Chiapas, pero nuestra plataforma está disponible para cualquier persona que necesite ayuda para encontrar o dar en adopción una mascota en México.</p>
                    
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <span class="block font-semibold text-gray-900 text-sm">Oficina principal</span>
                        <span class="text-gray-500 text-sm">Ocosingo, Chiapas, México</span>
                    </div>
                </div>

            </div>

            <div class="relative rounded-3xl overflow-hidden shadow-lg">
                <img src="https://images.unsplash.com/photo-1544568100-847a948585b9?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Perro rescatado">
                <div class="absolute inset-0 bg-orange-600/90 mix-blend-multiply"></div>
                
                <div class="relative p-10 md:p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    
                    <h2 class="text-3xl font-extrabold text-white mb-4">Cómo ayudar a la causa</h2>
                    <p class="text-orange-100 text-lg mb-10 max-w-2xl mx-auto">Existen muchas formas de contribuir a nuestra misión de ayudar a las mascotas.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-colors">
                            <h3 class="text-white font-bold text-lg mb-2">Comparte publicaciones</h3>
                            <p class="text-orange-50 text-sm">Ayuda a difundir las publicaciones de mascotas perdidas en tus redes sociales.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-colors">
                            <h3 class="text-white font-bold text-lg mb-2">Adopta responsablemente</h3>
                            <p class="text-orange-50 text-sm">Considera dar un hogar a una mascota en adopción antes de comprar.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-colors">
                            <h3 class="text-white font-bold text-lg mb-2">Voluntariado</h3>
                            <p class="text-orange-50 text-sm">Únete como voluntario en los refugios asociados a nuestra red.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection