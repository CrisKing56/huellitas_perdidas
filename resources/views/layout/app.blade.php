<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo1.png') }}" type="image/png">
    <title>Huellitas Perdidas </title>
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#1f2937',
                        adoption: '#22c55e',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        summary::-webkit-details-marker {
            display: none;
        }

        .font-navbar-strong {
            font-family: 'Arial Black', Arial, sans-serif;
            font-weight: 900;
            letter-spacing: 0.2px;
        }
    </style>
    
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <nav class="bg-white shadow-sm py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo1.png') }}" alt="Logo Huellitas" class="h-10 w-12">
                <a href="{{ route('inicio') }}">
                    <span class="text-xl font-bold text-gray-800">Huellitas perdidas</span>
                </a>
            </div>

            <div class="hidden md:flex gap-8 text-sm text-gray-700 items-center">
                <a href="{{ route('mascotas.index2') }}" class="hover:text-primary transition font-navbar-strong">
                    Mascotas perdidas
                </a>

                <a href="{{ route('adopciones.index') }}" class="hover:text-primary transition font-navbar-strong">
                    Mascotas en Adopción
                </a>

                <div class="relative group" tabindex="0">
                    <button type="button"
                        class="flex items-center gap-1 hover:text-primary transition focus:outline-none font-navbar-strong">
                        Cuidado Animal
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div class="absolute left-0 top-full pt-2
                                opacity-0 invisible
                                group-hover:opacity-100 group-hover:visible
                                group-focus-within:opacity-100 group-focus-within:visible
                                transition z-50">
                        <div class="w-48 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <a href="{{ route('veterinarias.index') }}"
                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition font-navbar-strong">
                                Veterinarias
                            </a>
                            <a href="{{ route('refugios.index') }}"
                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition font-navbar-strong">
                                Refugios
                            </a>
                            <a href="{{ route('consejos.index') }}"
                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition font-navbar-strong">
                                Consejos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}">
                        <span class="text-orange-500 font-medium text-sm">Iniciar sesión</span>
                    </a>

                    <button class="bg-orange-100 p-2 rounded-full text-primary hover:bg-orange-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>
                @endguest

                @auth
                    @php
                        $usuario = Auth::user();
                        $nombreCompleto = trim($usuario->nombre ?? 'Usuario');
                        $primerNombre = explode(' ', $nombreCompleto)[0] ?? 'Usuario';

                        $partesNombre = preg_split('/\s+/', $nombreCompleto);
                        $iniciales = '';

                        foreach (array_slice($partesNombre, 0, 2) as $parte) {
                            $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1));
                        }

                        $fotoPerfil = $usuario->foto_perfil ?? null;
                        $fotoUrl = null;

                        if ($fotoPerfil) {
                            if (\Illuminate\Support\Str::startsWith($fotoPerfil, ['http://', 'https://'])) {
                                $fotoUrl = $fotoPerfil;
                            } else {
                                $fotoUrl = asset('storage/' . ltrim(str_replace('storage/', '', $fotoPerfil), '/'));
                            }
                        }
                    @endphp

                    <details class="relative">
                        <summary class="list-none cursor-pointer select-none">
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-700 hidden sm:block max-w-[100px] truncate">
                                    {{ $primerNombre }}
                                </span>

                                <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>

                                <div class="h-10 w-10 rounded-full overflow-hidden border border-orange-200 bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">
                                    @if($fotoUrl)
                                        <img src="{{ $fotoUrl }}" alt="Foto de perfil" class="h-full w-full object-cover">
                                    @else
                                        {{ $iniciales ?: 'U' }}
                                    @endif
                                </div>
                            </div>
                        </summary>

                        <div class="absolute right-0 mt-3 w-64 bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden z-50">
                            <div class="px-4 py-4 border-b border-gray-100 bg-gray-50">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $usuario->nombre }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $usuario->correo }}</p>
                            </div>

                            <div class="py-2">
                                <a href="{{ route('inicio') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M5.25 9.75V21h13.5V9.75"></path>
                                    </svg>
                                    Inicio
                                </a>

                                <a href="{{ url('/perfil') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.5-1.632z"></path>
                                    </svg>
                                    Mi perfil
                                </a>

                                <a href="{{ route('extravios.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 6.75h-4.5A2.25 2.25 0 004.5 9v8.25A2.25 2.25 0 006.75 19.5h10.5A2.25 2.25 0 0019.5 17.25V9a2.25 2.25 0 00-2.25-2.25h-4.5M11.25 6.75V4.5h1.5v2.25M11.25 6.75h1.5"></path>
                                    </svg>
                                    Mis reportes
                                </a>

                                <a href="{{ route('adopciones.mis-adopciones') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"></path>
                                    </svg>
                                    Mis adopciones
                                </a>

                                <a href="{{ url('/nosotros') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.5-1.632z"></path>
                                    </svg>
                                    Nosotros
                                </a>

                                <a href="{{ url('/contactanos') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 7.5v9A2.25 2.25 0 0119.5 18.75h-15A2.25 2.25 0 012.25 16.5v-9m19.5 0A2.25 2.25 0 0019.5 5.25h-15A2.25 2.25 0 002.25 7.5m19.5 0l-8.69 5.793a1.125 1.125 0 01-1.245 0L2.25 7.5"></path>
                                    </svg>
                                    Contáctanos
                                </a>
                            </div>

                            <div class="border-t border-gray-100 p-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </details>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-secondary text-gray-300 py-12 mt-auto">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12 text-sm">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="h-8 w-auto">
                    <span class="text-lg font-bold text-white">Huellitas perdidas</span>
                </div>

                <p>Ayudando a reunir mascotas con sus familias desde 2025.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Enlaces rápidos</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('inicio') }}" class="hover:text-primary transition">Inicio</a></li>
                    <li><a href="{{ route('mascotas.index2') }}" class="hover:text-primary transition">Mascotas Perdidas</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Contacto</h4>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2">info@huellitasperdidas.com</li>
                    <li class="flex items-center gap-2">Ocosingo, Chiapas</li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-12 pt-8 border-t border-gray-700 text-xs">
            Huellitas Perdidas © 2025. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>