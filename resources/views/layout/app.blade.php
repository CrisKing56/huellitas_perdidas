<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo1.png') }}" type="image/png">
    <title>Huellitas Perdidas</title>

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
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(15, 23, 42, 0.08)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        .navbar-main-link {
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .navbar-dropdown-link {
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        .mobile-panel-scroll {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen text-gray-800">

    @php
        $esUsuarioNoVerificado = false;
    @endphp

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

            $fotoPerfil = $usuario->foto_perfil ?? ($usuario->google_avatar ?? null);
            $fotoUrl = null;

            if ($fotoPerfil) {
                if (\Illuminate\Support\Str::startsWith($fotoPerfil, ['http://', 'https://'])) {
                    $fotoUrl = $fotoPerfil;
                } else {
                    $fotoUrl = asset('storage/' . ltrim(str_replace('storage/', '', $fotoPerfil), '/'));
                }
            }

            $esUsuarioNoVerificado =
                (($usuario->auth_provider ?? 'LOCAL') === 'LOCAL') &&
                is_null($usuario->email_verified_at);
        @endphp
    @endauth

    @if($esUsuarioNoVerificado)
        <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="h-20 flex items-center justify-between gap-4">

                    <div class="flex items-center gap-3 min-w-0">
                        <a href="{{ route('verification.notice') }}" class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('img/logo1.png') }}" alt="Logo Huellitas" class="h-11 w-11 object-contain">
                            <div class="min-w-0">
                                <span class="block text-xl sm:text-2xl font-bold text-gray-900 leading-none truncate">
                                    Huellitas perdidas
                                </span>
                                <span class="block text-xs text-orange-500 font-medium mt-1">
                                    Verificación pendiente
                                </span>
                            </div>
                        </a>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden md:flex items-center gap-3 px-4 py-2 rounded-xl bg-orange-50 border border-orange-100 text-sm text-orange-600 font-medium">
                            Debes verificar tu correo
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-100 transition">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @else
        <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="h-20 flex items-center justify-between gap-4">

                    <div class="flex items-center gap-3 min-w-0">
                        <a href="{{ route('inicio') }}" class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('img/logo1.png') }}" alt="Logo Huellitas" class="h-11 w-11 object-contain">
                            <div class="min-w-0">
                                <span class="block text-xl sm:text-2xl font-bold text-gray-900 leading-none truncate">
                                    Huellitas perdidas
                                </span>
                            </div>
                        </a>
                    </div>

                    <div class="hidden lg:flex items-center gap-8 text-sm text-gray-700">
                        <a href="{{ route('mascotas.index2') }}"
                        class="navbar-main-link hover:text-primary transition">
                            Mascotas perdidas
                        </a>

                        <a href="{{ route('adopciones.index') }}"
                        class="navbar-main-link hover:text-primary transition">
                            Mascotas en Adopción
                        </a>

                        <div class="relative group" tabindex="0">
                            <button type="button"
                                class="flex items-center gap-2 navbar-main-link hover:text-primary transition focus:outline-none">
                                Cuidado Animal
                                <svg class="w-4 h-4 mt-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div class="absolute left-0 top-full pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition z-50">
                                <div class="w-56 bg-white border border-gray-200 rounded-2xl shadow-soft overflow-hidden">
                                    <a href="{{ route('veterinarias.index') }}"
                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                        Veterinarias
                                    </a>
                                    <a href="{{ route('refugios.index') }}"
                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                        Refugios
                                    </a>
                                    <a href="{{ route('consejos.index') }}"
                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                        Consejos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-center gap-3">
                        @guest
                            <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-orange-200 bg-orange-50 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-orange-100 transition">
                                Iniciar sesión
                            </a>
                        @endguest

                        @auth
                            <details class="relative">
                                <summary class="list-none cursor-pointer select-none">
                                    <div class="flex items-center gap-3 rounded-2xl px-3 py-2 hover:bg-gray-50 transition">
                                        <span class="text-sm text-gray-700 max-w-[110px] truncate font-medium">
                                            {{ $primerNombre }}
                                        </span>

                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                                <div class="absolute right-0 mt-3 w-72 bg-white border border-gray-200 rounded-2xl shadow-soft overflow-hidden z-50">
                                    <div class="px-4 py-4 border-b border-gray-100 bg-gray-50">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $usuario->nombre }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $usuario->correo }}</p>
                                    </div>

                                    <div class="py-2">
                                        <a href="{{ route('inicio') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M5.25 9.75V21h13.5V9.75"></path>
                                            </svg>
                                            Inicio
                                        </a>

                                        <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.5-1.632z"></path>
                                            </svg>
                                            Mi perfil
                                        </a>

                                        <a href="{{ route('extravios.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 6.75h-4.5A2.25 2.25 0 004.5 9v8.25A2.25 2.25 0 006.75 19.5h10.5A2.25 2.25 0 0019.5 17.25V9a2.25 2.25 0 00-2.25-2.25h-4.5M11.25 6.75V4.5h1.5v2.25M11.25 6.75h1.5"></path>
                                            </svg>
                                            Mis reportes
                                        </a>

                                        <a href="{{ route('adopciones.mis-adopciones') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"></path>
                                            </svg>
                                            Mis adopciones
                                        </a>

                                        <a href="{{ url('/nosotros') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.5-1.632z"></path>
                                            </svg>
                                            Nosotros
                                        </a>

                                        <a href="{{ url('/contactanos') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-primary transition navbar-dropdown-link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 7.5v9A2.25 2.25 0 0119.5 18.75h-15A2.25 2.25 0 012.25 16.5v-9m19.5 0A2.25 2.25 0 0019.5 5.25h-15A2.25 2.25 0 002.25 7.5m19.5 0l-8.69 5.793a1.125 1.125 0 01-1.245 0L2.25 7.5"></path>
                                            </svg>
                                            Contáctanos
                                        </a>
                                    </div>

                                    <div class="border-t border-gray-100 p-2">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 rounded-xl transition navbar-dropdown-link">
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

                    <div class="lg:hidden flex items-center gap-2">
                        @guest
                            <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-orange-200 bg-orange-50 px-3 py-2 text-sm font-semibold text-primary hover:bg-orange-100 transition">
                                Entrar
                            </a>
                        @endguest

                        <details class="relative">
                            <summary class="list-none cursor-pointer select-none">
                                <div class="h-11 w-11 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-700 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"></path>
                                    </svg>
                                </div>
                            </summary>

                            <div class="absolute right-0 mt-3 w-[90vw] max-w-sm bg-white border border-gray-200 rounded-2xl shadow-soft overflow-hidden z-50 mobile-panel-scroll">
                                <div class="px-4 py-4 border-b border-gray-100 bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        @auth
                                            <div class="h-11 w-11 rounded-full overflow-hidden border border-orange-200 bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">
                                                @if($fotoUrl)
                                                    <img src="{{ $fotoUrl }}" alt="Foto de perfil" class="h-full w-full object-cover">
                                                @else
                                                    {{ $iniciales ?: 'U' }}
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $usuario->nombre }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $usuario->correo }}</p>
                                            </div>
                                        @else
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">Menú principal</p>
                                                <p class="text-xs text-gray-500">Navega por Huellitas Perdidas</p>
                                            </div>
                                        @endauth
                                    </div>
                                </div>

                                <div class="p-3 space-y-2">
                                    <a href="{{ route('inicio') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                        Inicio
                                    </a>

                                    <a href="{{ route('mascotas.index2') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                        Mascotas perdidas
                                    </a>

                                    <a href="{{ route('adopciones.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                        Mascotas en Adopción
                                    </a>

                                    <details class="group rounded-xl border border-gray-100 bg-gray-50">
                                        <summary class="flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 cursor-pointer">
                                            <span>Cuidado Animal</span>
                                            <svg class="w-4 h-4 text-gray-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </summary>

                                        <div class="px-2 pb-2 space-y-1">
                                            <a href="{{ route('veterinarias.index') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-white hover:text-primary transition">
                                                Veterinarias
                                            </a>
                                            <a href="{{ route('refugios.index') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-white hover:text-primary transition">
                                                Refugios
                                            </a>
                                            <a href="{{ route('consejos.index') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-white hover:text-primary transition">
                                                Consejos
                                            </a>
                                        </div>
                                    </details>

                                    <a href="{{ url('/nosotros') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                        Nosotros
                                    </a>

                                    <a href="{{ url('/contactanos') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                        Contáctanos
                                    </a>

                                    @auth
                                        <div class="border-t border-gray-100 my-2"></div>

                                        <a href="{{ route('perfil') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                            Mi perfil
                                        </a>

                                        <a href="{{ route('extravios.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                            Mis reportes
                                        </a>

                                        <a href="{{ route('adopciones.mis-adopciones') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-primary transition">
                                            Mis adopciones
                                        </a>

                                        <form action="{{ route('logout') }}" method="POST" class="pt-2">
                                            @csrf
                                            <button type="submit" class="w-full rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-500 hover:bg-red-100 transition">
                                                Cerrar sesión
                                            </button>
                                        </form>
                                    @endauth
                                </div>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main class="flex-grow">
        @yield('content')
    </main>

    @if(!$esUsuarioNoVerificado)
        <footer class="bg-secondary text-gray-300 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-10">

                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                            <div>
                                <span class="block text-lg font-bold text-white">Huellitas perdidas</span>
                                <span class="text-xs text-gray-400">Mascotas, adopción y ayuda</span>
                            </div>
                        </div>

                        <p class="text-sm leading-6 text-gray-400">
                            Conectamos personas con mascotas perdidas, en adopción y con servicios de apoyo animal para facilitar reencuentros y nuevos hogares.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-white font-bold mb-4">Explorar</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="{{ route('inicio') }}" class="hover:text-primary transition">Inicio</a></li>
                            <li><a href="{{ route('mascotas.index2') }}" class="hover:text-primary transition">Mascotas perdidas</a></li>
                            <li><a href="{{ route('adopciones.index') }}" class="hover:text-primary transition">Mascotas en adopción</a></li>
                            <li><a href="{{ route('consejos.index') }}" class="hover:text-primary transition">Consejos</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-white font-bold mb-4">Cuidado animal</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="{{ route('veterinarias.index') }}" class="hover:text-primary transition">Veterinarias</a></li>
                            <li><a href="{{ route('refugios.index') }}" class="hover:text-primary transition">Refugios</a></li>
                            <li><a href="{{ url('/nosotros') }}" class="hover:text-primary transition">Nosotros</a></li>
                            <li><a href="{{ url('/contactanos') }}" class="hover:text-primary transition">Contáctanos</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-white font-bold mb-4">Cuenta y contacto</h4>
                        <ul class="space-y-3 text-sm">
                            @guest
                                <li><a href="{{ route('login') }}" class="hover:text-primary transition">Iniciar sesión</a></li>
                            @endguest

                            @auth
                                <li><a href="{{ route('perfil') }}" class="hover:text-primary transition">Mi perfil</a></li>
                                <li><a href="{{ route('extravios.index') }}" class="hover:text-primary transition">Mis reportes</a></li>
                                <li><a href="{{ route('adopciones.mis-adopciones') }}" class="hover:text-primary transition">Mis adopciones</a></li>
                            @endauth

                            <li class="text-gray-400">info@huellitasperdidas.com</li>
                            <li class="text-gray-400">Ocosingo, Chiapas</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-6 border-t border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-400">
                    <p>Huellitas Perdidas © 2025. Todos los derechos reservados.</p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('inicio') }}" class="hover:text-primary transition">Inicio</a>
                        <a href="{{ route('mascotas.index2') }}" class="hover:text-primary transition">Extraviadas</a>
                        <a href="{{ route('adopciones.index') }}" class="hover:text-primary transition">Adopciones</a>
                        <a href="{{ route('consejos.index') }}" class="hover:text-primary transition">Consejos</a>
                    </div>
                </div>
            </div>
        </footer>
    @endif
</body>
</html>