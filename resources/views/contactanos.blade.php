@extends('layout.app')

@section('content')
    <div class="relative bg-gray-900 pt-24 pb-32 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?q=80&w=2000&auto=format&fit=crop"
                 alt="Contacto Huellitas Perdidas"
                 class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">
                Contáctanos
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-300">
                Estamos aquí para ayudarte. Si tienes dudas, sugerencias o necesitas apoyo, envíanos un mensaje.
            </p>
        </div>
    </div>

    <div class="bg-gray-50 py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18V8H3v8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Correo</h3>
                    <p class="text-gray-600 text-sm mb-3">Escríbenos para dudas, sugerencias o ayuda.</p>
                    <a href="mailto:{{ $contactEmail }}" class="text-orange-500 font-semibold break-all hover:text-orange-600">
                        {{ $contactEmail }}
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bg-green-50 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a2 2 0 011.9 1.37l1.02 3.06a2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006.36 6.36l1.27-1.27a2 2 0 012.11-.45l3.06 1.02A2 2 0 0121 15.72V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">WhatsApp</h3>
                    <p class="text-gray-600 text-sm mb-3">Contáctanos más rápido si necesitas apoyo.</p>
                    <a href="https://wa.me/{{ $contactWhatsapp }}" target="_blank" class="text-green-600 font-semibold hover:text-green-700">
                        Enviar mensaje
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bg-blue-50 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Ubicación</h3>
                    <p class="text-gray-600 text-sm">{{ $contactAddress }}</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bg-purple-50 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Horario</h3>
                    <p class="text-gray-600 text-sm">{{ $contactHours }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                <div class="bg-white rounded-3xl p-8 md:p-10 shadow-sm border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                            <svg class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 14h.01M16 10h.01M9 16h6M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.2-4.2A7.662 7.662 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h2 class="ml-4 text-3xl font-extrabold text-gray-900">Envíanos un mensaje</h2>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contactanos.enviar') }}" method="POST" class="space-y-5">
                        @csrf

                        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
                            <input type="text"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre') }}"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                                   placeholder="Tu nombre completo">
                        </div>

                        <div>
                            <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo electrónico</label>
                            <input type="email"
                                   id="correo"
                                   name="correo"
                                   value="{{ old('correo') }}"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                                   placeholder="tucorreo@ejemplo.com">
                        </div>

                        <div>
                            <label for="asunto" class="block text-sm font-semibold text-gray-700 mb-2">Asunto</label>
                            <input type="text"
                                   id="asunto"
                                   name="asunto"
                                   value="{{ old('asunto') }}"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
                                   placeholder="Escribe el asunto de tu mensaje">
                        </div>

                        <div>
                            <label for="mensaje" class="block text-sm font-semibold text-gray-700 mb-2">Mensaje</label>
                            <textarea id="mensaje"
                                      name="mensaje"
                                      rows="6"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none resize-none"
                                      placeholder="Cuéntanos cómo podemos ayudarte...">{{ old('mensaje') }}</textarea>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm transition">
                            Enviar mensaje
                        </button>
                    </form>
                </div>

                <div class="space-y-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">¿En qué podemos ayudarte?</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            Puedes escribirnos si tienes dudas sobre el uso de la plataforma, problemas con alguna publicación,
                            sugerencias de mejora o si deseas colaborar con nuestra comunidad.
                        </p>

                        <div class="space-y-4">
                            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                                <h4 class="font-bold text-gray-900 mb-2">Soporte general</h4>
                                <p class="text-sm text-gray-600">Ayuda con tu cuenta, publicaciones, errores del sistema o recuperación de acceso.</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                                <h4 class="font-bold text-gray-900 mb-2">Sugerencias y mejoras</h4>
                                <p class="text-sm text-gray-600">Tus comentarios nos ayudan a seguir mejorando Huellitas Perdidas para la comunidad.</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                                <h4 class="font-bold text-gray-900 mb-2">Colaboraciones</h4>
                                <p class="text-sm text-gray-600">Si eres refugio, veterinaria o voluntario y quieres colaborar, contáctanos.</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative rounded-3xl overflow-hidden shadow-lg">
                        <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=1200&auto=format&fit=crop"
                             class="absolute inset-0 w-full h-full object-cover"
                             alt="Mascota feliz">
                        <div class="absolute inset-0 bg-orange-600/85 mix-blend-multiply"></div>

                        <div class="relative p-10 text-white">
                            <h3 class="text-2xl font-extrabold mb-3">¿Perdiste una mascota?</h3>
                            <p class="text-orange-50 mb-6">
                                Si tu caso es urgente, además de contactarnos te recomendamos publicar de inmediato el reporte
                                para que más personas puedan ayudarte a difundirlo.
                            </p>

                            @auth
                                <a href="{{ route('mascotas.create') }}"
                                   class="inline-flex items-center justify-center bg-white text-orange-600 font-bold px-6 py-3 rounded-xl hover:bg-orange-50 transition">
                                    Ir a reportar mascota
                                </a>
                            @endauth

                            @guest
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center bg-white text-orange-600 font-bold px-6 py-3 rounded-xl hover:bg-orange-50 transition">
                                    Ir a reportar mascota
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection