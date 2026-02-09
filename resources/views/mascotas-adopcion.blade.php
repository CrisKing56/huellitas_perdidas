@extends('layout.app')

@section('title', 'Mascotas en Adopción')

@section('content')
<div class="container mx-auto px-6 py-8">
    
    <h1 class="text-2xl font-bold text-gray-900 mb-6 uppercase">Mascotas en Adopción</h1>

    <div class="flex flex-col lg:flex-row gap-4 mb-4">
        
        <div class="relative flex-grow">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" 
                   class="w-full py-2.5 pl-10 pr-4 text-gray-700 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm placeholder-gray-400" 
                   placeholder="Buscar mascotas...">
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:w-auto">
            <select class="w-full lg:w-40 py-2.5 px-3 bg-white border border-gray-200 rounded-lg text-gray-600 text-sm focus:outline-none focus:border-primary cursor-pointer shadow-sm">
                <option value="">Especie</option>
                <option value="perro">Perro</option>
                <option value="gato">Gato</option>
            </select>

            <select class="w-full lg:w-40 py-2.5 px-3 bg-white border border-gray-200 rounded-lg text-gray-600 text-sm focus:outline-none focus:border-primary cursor-pointer shadow-sm">
                <option value="">Raza</option>
                <option value="labrador">Labrador</option>
                <option value="siames">Siamés</option>
            </select>

            <select class="w-full lg:w-40 py-2.5 px-3 bg-white border border-gray-200 rounded-lg text-gray-600 text-sm focus:outline-none focus:border-primary cursor-pointer shadow-sm">
                <option value="">Edad</option>
                <option value="cachorro">Cachorro</option>
                <option value="adulto">Adulto</option>
            </select>

            <select class="w-full lg:w-40 py-2.5 px-3 bg-white border border-gray-200 rounded-lg text-gray-600 text-sm focus:outline-none focus:border-primary cursor-pointer shadow-sm">
                <option value="">Ubicación</option>
                <option value="madrid">Madrid</option>
                <option value="barcelona">Barcelona</option>
            </select>
        </div>
    </div>

    <div class="flex justify-between items-center mb-8">
        <a href="{{ route ('pub.adopcion')}}" class="text-sm font-medium text-gray-800 hover:text-primary transition flex items-center gap-1">
            Publicar mascota en adopción 
            <span class="text-lg font-bold">+</span>
        </a>
        <span class="text-sm text-gray-500">8 mascotas encontradas</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col">
            <div class="relative h-64">
                <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=500&q=60" class="w-full h-full object-cover">
                <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                    En adopción
                </span>
                <button class="absolute top-3 right-3 bg-white p-2 rounded-full text-gray-500 hover:text-primary transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
            <div class="p-5 flex-grow flex flex-col">
                <h3 class="font-bold text-gray-900 text-lg">Rocky</h3>
                <p class="text-xs text-gray-500 mb-2">Perro</p>
                <p class="text-sm text-gray-600 mb-4 flex-grow">Beagle con mucha energía</p>
                
                <div class="flex items-center gap-1 text-xs text-primary mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Barcelona, España
                </div>
                
                <a href="#" class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-primary transition group mt-auto">
                    ¡Adoptar!
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col">
            <div class="relative h-64 bg-blue-500"> <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=500&q=60" class="w-full h-full object-cover mix-blend-multiply">
                <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                    En adopción
                </span>
                <button class="absolute top-3 right-3 bg-white p-2 rounded-full text-gray-500 hover:text-primary transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
            <div class="p-5 flex-grow flex flex-col">
                <h3 class="font-bold text-gray-900 text-lg">Mimi</h3>
                <p class="text-xs text-gray-500 mb-2">Gato</p>
                <p class="text-sm text-gray-600 mb-4 flex-grow">Gato naranja muy sociable</p>
                
                <div class="flex items-center gap-1 text-xs text-primary mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Madrid, España
                </div>
                
                <a href="#" class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-primary transition group mt-auto">
                    ¡Adoptar!
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col">
            <div class="relative h-64">
                <img src="https://images.unsplash.com/photo-1605568427561-40dd23d2acca?auto=format&fit=crop&w=500&q=60" class="w-full h-full object-cover">
                <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                    En adopción
                </span>
                <button class="absolute top-3 right-3 bg-white p-2 rounded-full text-gray-500 hover:text-primary transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
            <div class="p-5 flex-grow flex flex-col">
                <h3 class="font-bold text-gray-900 text-lg">Zeus</h3>
                <p class="text-xs text-gray-500 mb-2">Perro</p>
                <p class="text-sm text-gray-600 mb-4 flex-grow">Husky siberiano energético</p>
                
                <div class="flex items-center gap-1 text-xs text-primary mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Valencia, España
                </div>
                
                <a href="#" class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-primary transition group mt-auto">
                    ¡Adoptar!
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col">
            <div class="relative h-64">
                <img src="https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=500&q=60" class="w-full h-full object-cover">
                <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded
                    En adopción
                </span>
                <button class="absolute top-3 right-3 bg-white p-2 rounded-full text-gray-500 hover:text-primary transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
            <div class="p-5 flex-grow flex flex-col">
                <h3 class="font-bold text-gray-900 text-lg">Zeus</h3>
                <p class="text-xs text-gray-500 mb-2">Perro</p>
                <p class="text-sm text-gray-600 mb-4 flex-grow">Husky siberiano energético</p>
                
                <div class="flex items-center gap-1 text-xs text-primary mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Valencia, España
                </div>
                
                <a href="#" class="flex justify-between items-center text-sm font-bold text-gray-900 hover:text-primary transition group mt-auto">
                    ¡Adoptar!
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

    

@endsection