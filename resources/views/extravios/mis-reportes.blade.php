@extends('layout.app')

@section('content')


    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Mis publicaciones</h1>
                <p class="text-gray-500">Consulta y administra tus publicaciones realizadas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-orange-400 rounded-xl p-6 text-white flex items-center shadow-lg">
                    <div class="bg-white/20 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium opacity-80">Total</p>
                        <p class="text-2xl font-bold">{{ $mascotas->total() }}</p>
                    </div>
                </div>
                <div class="bg-orange-400/90 rounded-xl p-6 text-white flex items-center shadow-lg">
                    <div class="bg-white/20 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium opacity-80">Activas</p>
                        <p class="text-2xl font-bold">{{ $mascotas->where('estado', 'ACTIVA')->count() }}</p>
                    </div>
                </div>
                <div class="bg-orange-400/80 rounded-xl p-6 text-white flex items-center shadow-lg">
                    <div class="bg-white/20 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium opacity-80">Resueltas</p>
                        <p class="text-2xl font-bold">{{ $mascotas->where('estado', 'RESUELTA')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="#" class="border-orange-500 text-orange-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Mascotas extraviadas
                        <span class="bg-orange-100 text-orange-600 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $mascotas->total() }}</span>
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Mascotas en adopción
                    </a>
                </nav>
            </div>

            <div class="space-y-4">
                @forelse ($mascotas as $pub)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-start md:items-center gap-6 hover:shadow-md transition">
                        
                        <div class="w-full md:w-32 h-32 flex-shrink-0">
                            @if($pub->fotoPrincipal)
                                <img src="{{ asset('storage/' . $pub->fotoPrincipal->url) }}" class="w-full h-full object-cover rounded-lg" alt="{{ $pub->nombre }}">
                            @else
                                <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $pub->nombre }}</h3>
                                    <p class="text-sm text-gray-500 mb-1">Extraviada en {{ $pub->colonia_barrio }}</p>
                                    <p class="text-xs text-gray-400">
                                        Publicado: {{ $pub->created_at ? \Carbon\Carbon::parse($pub->created_at)->format('d M Y') : 'Fecha no disponible' }}
                                    </p>
                                </div>
                                
                                @php
                                    $colores = [
                                        'ACTIVA' => 'bg-green-100 text-green-800',
                                        'REVISION' => 'bg-yellow-100 text-yellow-800',
                                        'RESUELTA' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $claseColor = $colores[$pub->estado] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="{{ $claseColor }} px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ $pub->estado }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route ('extravios.show' , $pub->id_publicacion)}}" class="text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-md text-sm font-medium transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Ver
                                </a>
                                <a href="{{ route('extravios.edit', $pub->id_publicacion) }}" class="text-gray-600 border border-gray-200 hover:bg-gray-50 px-3 py-1.5 rounded-md text-sm font-medium transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Editar
                                </a>
                                <button class="text-green-600 border border-green-200 hover:bg-green-50 px-3 py-1.5 rounded-md text-sm font-medium transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Resolver
                                </button>
                                
                                <form action="{{ route('extravios.destroy', $pub->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este reporte?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-md text-sm font-medium transition flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes reportes</h3>
                        <p class="mt-1 text-sm text-gray-500">Empieza reportando una mascota extraviada.</p>
                        <div class="mt-6">
                            <a href="{{ route('extravios.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                Crear reporte
                            </a>
                        </div>
                    </div>
                @endforelse
                
                <div class="mt-4">
                    {{ $mascotas->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection