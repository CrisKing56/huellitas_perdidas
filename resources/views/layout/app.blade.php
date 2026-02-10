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
                        primary: '#f97316',   // Tu Naranja
                        secondary: '#1f2937', // Tu Gris Oscuro
                        adoption: '#22c55e',  // Tu Verde
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
            <div class="hidden md:flex gap-8 text-sm font-medium text-gray-600">
                <a href="{{ route('mascotas.index2') }}" class="hover:text-primary transition">Mascotas perdidas</a>
                <a href="{{ route('adopciones.index') }}" class="hover:text-primary transition">Mascotas en Adopción</a>
                <div class="relative group cursor-pointer">
                    <span class="flex items-center gap-1 hover:text-primary transition">
                        Cuidado Animal <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @guest
                <a href="{{ route ('registro.usuario')}}">
                    <span class="text-orange-500 font-medium text-sm">Registrarse</span>
                </a>
                <button class="bg-orange-100 p-2 rounded-full text-primary hover:bg-orange-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </button>

                @endguest

                @auth 
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col text-right hidden sm:block">
                            <span class="text-sm font-bold text-gray-700">
                              Hola {{ Auth::user()->nombre }} </span>
                        </div>

                        <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold border border-orange-200">
                            {{ substr(Auth::user()->nombre, 0, 1) }}
                        </div>

                    <form action="{{ route('logout') }}" method="POST" class="ml-2">
                        @csrf <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Cerrar Sesión">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </form>
                </div>



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
                    <li><a href="{{ route ('inicio')}}" class="hover:text-primary transition">Inicio</a></li>
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