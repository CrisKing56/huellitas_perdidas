@extends('layout.app')

@section('content')

    <header class="relative w-full h-[500px] overflow-hidden">
        <img src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&w=1950&q=80" class="absolute w-full h-full object-cover">
        <div class="absolute inset-0 hero-overlay flex flex-col items-center justify-center text-center px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">Reporta o busca una mascota fácilmente.</h1>
            <p class="text-white text-lg mb-8">¡Conectando familias con mascotas perdidas!</p>
            <button class="bg-primary hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition transform hover:scale-105">
                <a href="{{ route ('reportar.mascota')}}">Reportar mascota</a>
            </button>
        </div>
    </header>

    <section class="container mx-auto px-6 py-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Mascotas perdidas recientemente</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-pet-card 
                name="Bella" 
                type="Perro"
                description="Perro pequeño con collar rojo"
                location="Barrio Lindavista, Ocosingo"
                image="https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=500&q=60"
                status="Perdida"
                tagColor="bg-primary"
                btnText="Reportar avistamiento"
            />

            <x-pet-card 
                name="Máximo" 
                type="Gato"
                description="Gato anaranjado muy amigable"
                location="Centro, Ocosingo"
                image="https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=500&q=60"
                status="Perdida"
                tagColor="bg-primary"
                btnText="Reportar avistamiento"
            />

            <x-pet-card 
                name="Rorro" 
                type="Gatisimo"
                description="Gato rey en celo"
                location="En cualquier esquina de Ocosingo"
                image="https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=500&q=60"
                status="Super perdida"
                tagColor="bg-primary"
                btnText="Reportar avistamiento"
            />
            
            </div>
    </section>

    <section class="bg-white py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Mascotas que necesitan un hogar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <x-pet-card 
                    name="Rocky" 
                    type="Perro"
                    description="Beagle con manchas marrones"
                    location="¡Buscando un hogar!"
                    image="https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?auto=format&fit=crop&w=500&q=60"
                    status="En adopción"
                    tagColor="bg-adoption"
                    btnText="¡Adoptar!"
                />

                <x-pet-card 
                    name="Mimi" 
                    type="Gato"
                    description="Gato naranja muy sociable"
                    location="¡Amistoso y juguetón!"
                    image="https://images.unsplash.com/photo-1529778873920-4da4926a7071?auto=format&fit=crop&w=500&q=60"
                    status="En adopción"
                    tagColor="bg-adoption"
                    btnText="¡Adoptar!"
                />

            </div>
        </div>
    </section>

@endsection