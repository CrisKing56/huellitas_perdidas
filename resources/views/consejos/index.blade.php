@extends('layout.app')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Consejos y Cuidados</h1>
                <p class="text-gray-500 mt-2">Aprende los mejores tips para el bienestar de las mascotas.</p>
            </div>

            @auth
                @if(auth()->user()->rol === 'VETERINARIA')
                    <a href="{{ route('consejos.create') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 border border-transparent rounded-lg font-semibold text-white hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Subir consejo
                    </a>
                @endif
            @endauth
        </div>

        @if($consejos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($consejos as $consejo)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                        
                        <div class="h-52 bg-gray-100 relative overflow-hidden group">
                            @if($consejo->imagenes->count() > 0)
                                <img src="{{ asset('storage/' . $consejo->imagenes->first()->url) }}" alt="{{ $consejo->titulo }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 text-xs font-bold bg-white text-orange-600 rounded-full shadow-sm">
                                    {{ $consejo->categoria ? $consejo->categoria->nombre : 'General' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <h2 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                {{ $consejo->titulo }}
                            </h2>
                            <p class="text-gray-600 text-sm flex-1 line-clamp-3 mb-4">
                                {{ $consejo->resumen }}
                            </p>
                            
                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-400 font-medium">
                                    {{ \Carbon\Carbon::parse($consejo->creado_en)->format('d M, Y') }}
                                </span>
                                <a href="{{ route('consejos.show', $consejo->id_consejo) }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600 flex items-center">
                                    Leer más
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $consejos->links() }}
            </div>

        @else
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aún no hay consejos publicados</h3>
                <p class="mt-1 text-gray-500">Sé el primero en compartir tu conocimiento con la comunidad.</p>
                <div class="mt-6">
                    <a href="{{ route('consejos.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-700 rounded-md font-medium hover:bg-orange-200 transition">
                        Subir el primer consejo
                    </a>
                </div>
            </div>
        @endif

    </div>
@endsection
