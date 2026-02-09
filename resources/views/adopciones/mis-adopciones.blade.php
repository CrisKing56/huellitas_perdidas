@extends('layout.app')

@section('content')
<div class="min-h-screen bg-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mis publicaciones</h1>
                <p class="text-gray-500 mt-1">Consulta y administra tus publicaciones realizadas.</p>
            </div>
            
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-orange-400 rounded-xl p-6 flex items-center shadow-sm">
                <div class="bg-white/20 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-sm font-medium opacity-90">Total</p>
                    <p class="text-white text-3xl font-bold">{{ $adopciones->total() }}</p>
                </div>
            </div>

            <div class="bg-orange-400 rounded-xl p-6 flex items-center shadow-sm">
                <div class="bg-white/20 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-sm font-medium opacity-90">Activas</p>
                    <p class="text-white text-3xl font-bold">{{ $adopciones->where('estado', 'DISPONIBLE')->count() }}</p>
                </div>
            </div>

            <div class="bg-orange-400 rounded-xl p-6 flex items-center shadow-sm">
                <div class="bg-white/20 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-sm font-medium opacity-90">Adoptadas</p>
                    <p class="text-white text-3xl font-bold">{{ $adopciones->where('estado', 'ADOPTADA')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('extravios.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                    Mascotas extraviadas
                    <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs">Ir</span>
                </a>

                <a href="#" class="border-orange-500 text-orange-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                    Mascotas en adopción
                    <span class="bg-orange-100 text-orange-600 py-0.5 px-2.5 rounded-full text-xs">{{ $adopciones->total() }}</span>
                </a>
            </nav>
        </div>

        <div class="space-y-4">
            @forelse ($adopciones as $adopcion)
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-6 hover:shadow-md transition-shadow">
                    
                    <div class="w-full sm:w-28 h-28 flex-shrink-0">
                        @if($adopcion->fotoPrincipal)
                            <img src="{{ asset('storage/' . $adopcion->fotoPrincipal->url) }}" class="w-full h-full object-cover rounded-lg">
                        @else
                            <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 w-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $adopcion->nombre }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ ucfirst(strtolower($adopcion->especie_id == 1 ? 'Perro' : 'Gato')) }} en {{ $adopcion->colonia_barrio }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Publicado: {{ \Carbon\Carbon::parse($adopcion->creado_en)->format('d M, Y') }}
                                </p>
                            </div>
                            
                            @php
                                $estados = [
                                    'DISPONIBLE' => 'bg-green-100 text-green-700',
                                    'ADOPTADA' => 'bg-purple-100 text-purple-700',
                                    'PAUSADA' => 'bg-gray-100 text-gray-600',
                                ];
                                $claseBadge = $estados[$adopcion->estado] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="{{ $claseBadge }} px-2.5 py-0.5 rounded text-xs font-bold uppercase">
                                {{ $adopcion->estado }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            <a href="{{ route('adopciones.show', $adopcion->id_publicacion) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-50 text-orange-600 rounded text-sm font-medium hover:bg-orange-100 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Ver
                            </a>

                            <a href="{{ route('adopciones.edit', $adopcion->id_publicacion) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-200 text-gray-600 rounded text-sm font-medium hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar
                            </a>
                            
                            {{-- 
                            <button class="inline-flex items-center px-3 py-1.5 border border-green-200 text-green-600 rounded text-sm font-medium hover:bg-green-50 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Adoptada
                            </button> 
                            --}}

                            <form action="{{ route('adopciones.destroy', $adopcion->id_publicacion) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta publicación?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 text-red-500 rounded text-sm font-medium hover:bg-red-50 transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Sin publicaciones</h3>
                    <p class="text-gray-500 mt-1">Aún no has puesto ninguna mascota en adopción.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $adopciones->links() }}
        </div>

    </div>
</div>
@endsection