<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrador - Huellitas Perdidas')</title>
    <link rel="icon" href="{{ asset('img/logo1.png') }}" type="image/png">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#1f2937',
                        sidebar: '#243447',
                        panelbg: '#f6f7fb',
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(15, 23, 42, 0.08)',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: Inter, system-ui, sans-serif; }
        .admin-scroll::-webkit-scrollbar { width: 8px; }
        .admin-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.45);
            border-radius: 999px;
        }
    </style>
</head>
<body class="bg-panelbg text-gray-800">
<div class="min-h-screen flex">

    {{-- Overlay móvil --}}
    <div id="sidebarOverlay"
         class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden"></div>

    {{-- Sidebar --}}
    <aside id="adminSidebar"
           class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-sidebar text-gray-300 flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl lg:shadow-none">

        <div class="h-20 px-6 flex items-center justify-between border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="h-9 w-9 object-contain">
                <div>
                    <span class="block text-white font-bold text-lg leading-none">Huellitas</span>
                    <span class="text-xs text-gray-400">Panel administrativo</span>
                </div>
            </a>

            <button id="closeSidebar"
                    class="lg:hidden text-gray-300 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-1 admin-scroll overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
               {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-lg shadow-orange-500/20' : 'hover:bg-white/10 text-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.usuarios.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
               {{ request()->routeIs('admin.usuarios.*') ? 'bg-primary text-white shadow-lg shadow-orange-500/20' : 'hover:bg-white/10 text-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5V10H2v10h5m10 0v-2a4 4 0 10-8 0v2m8 0H7m10-10V6a4 4 0 10-8 0v4m8 0H7"/>
                </svg>
                <span>Usuarios</span>
            </a>

            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition font-medium hover:bg-white/10 text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2a4 4 0 014-4h6M3 7h18M5 7l1.5 11h11L19 7"/>
                </svg>
                <span>Publicaciones (Extraviados)</span>
            </button>

            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition font-medium hover:bg-white/10 text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 12h4l3 8 4-16 3 8h2"/>
                </svg>
                <span>Publicaciones (Adopciones)</span>
            </button>

            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition font-medium hover:bg-white/10 text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.2-3.2A7.773 7.773 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Comentarios</span>
            </button>

            <a href="{{ route('admin.veterinarias.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
               {{ request()->routeIs('admin.veterinarias.*') ? 'bg-primary text-white shadow-lg shadow-orange-500/20' : 'hover:bg-white/10 text-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a4 4 0 00-5.656 0M9 10h.01M15 10h.01M12 14h.01M8 6h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                </svg>
                <span>Veterinarias</span>
            </a>

            <a href="{{ route('admin.refugios.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
               {{ request()->routeIs('admin.refugios.*') ? 'bg-primary text-white shadow-lg shadow-orange-500/20' : 'hover:bg-white/10 text-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10l9-7 9 7v9a2 2 0 01-2 2h-4v-6H9v6H5a2 2 0 01-2-2v-9z"/>
                </svg>
                <span>Refugios</span>
            </a>

            <a href="{{ route('admin.reportes.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium
               {{ request()->routeIs('admin.reportes.*') ? 'bg-primary text-white shadow-lg shadow-orange-500/20' : 'hover:bg-white/10 text-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-6h13M9 5v6h13M5 5h.01M5 12h.01M5 19h.01"/>
                </svg>
                <span>Reportes</span>
            </a>

            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition font-medium hover:bg-white/10 text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 20h9M12 4h9M4 9h16M4 15h16"/>
                </svg>
                <span>Consejos</span>
            </button>

            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition font-medium hover:bg-white/10 text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Configuración del sitio</span>
            </button>
        </nav>

        <div class="p-4 border-t border-white/10 text-xs text-gray-400">
            Huellitas Perdidas © {{ date('Y') }}
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <button id="openSidebar"
                            class="lg:hidden inline-flex items-center justify-center h-11 w-11 rounded-xl border border-gray-200 bg-white text-gray-700 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="h-7 w-7 object-contain">
                            <span class="font-semibold text-gray-900 truncate">Huellitas perdidas</span>
                            <span class="hidden sm:inline text-gray-300">|</span>
                            <span class="hidden sm:inline text-sm text-gray-500 truncate">
                                @yield('top_title', 'Panel Administrador')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">
                    <div class="hidden sm:flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold text-gray-800 leading-none">{{ Auth::user()->nombre }}</p>
                            <p class="text-xs text-gray-400 mt-1">Administrador</p>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 text-sm font-medium text-red-500 hover:text-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9l3 3m0 0l-3 3m3-3H21"/>
                            </svg>
                            <span class="hidden sm:inline">Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Contenido --}}
        <div class="flex-1 flex flex-col min-h-0">
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto admin-scroll">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="bg-white border-t border-gray-200 px-4 sm:px-6 lg:px-8 py-4 text-sm text-gray-500">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>Panel Administrador · Huellitas Perdidas © {{ date('Y') }}</p>
                    <p class="text-xs text-gray-400">Gestión de usuarios, refugios, veterinarias y reportes</p>
                </div>
            </footer>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    openBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            overlay.classList.add('hidden');
            sidebar.classList.remove('-translate-x-full');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>
</body>
</html>