@extends('layout.app')

@section('content')
@php
    $fotoPerfil = $usuario->foto_perfil ?? null;
    $fotoUrl = null;

    if ($fotoPerfil) {
        if (\Illuminate\Support\Str::startsWith($fotoPerfil, ['http://', 'https://'])) {
            $fotoUrl = $fotoPerfil;
        } else {
            $fotoUrl = asset('storage/' . ltrim(str_replace('storage/', '', $fotoPerfil), '/'));
        }
    }

    $iniciales = collect(preg_split('/\s+/', trim($usuario->nombre ?? 'Usuario')))
        ->take(2)
        ->map(fn($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
        ->implode('');

    $totalPublicaciones = $conteoExtravios + $conteoAdopciones;
@endphp

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Mi perfil</h1>
            <p class="text-gray-500">Gestiona tu información, privacidad y actividad.</p>
        </div>

        @if(session('success_profile'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success_profile') }}
            </div>
        @endif

        @if(session('success_photo'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success_photo') }}
            </div>
        @endif

        @if(session('success_settings'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success_settings') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-orange-100 flex items-center justify-center text-orange-600 text-3xl font-bold">
                                @if($fotoUrl)
                                    <img src="{{ $fotoUrl }}" alt="Foto de perfil" class="w-full h-full object-cover">
                                @else
                                    {{ $iniciales ?: 'U' }}
                                @endif
                            </div>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mt-5">{{ $usuario->nombre }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $usuario->correo }}</p>

                        <div class="grid grid-cols-3 gap-3 w-full mt-6">
                            <div class="rounded-2xl bg-orange-50 border border-orange-100 px-3 py-4">
                                <p class="text-xl font-bold text-orange-600">{{ $totalPublicaciones }}</p>
                                <p class="text-xs text-gray-500 mt-1">Publicaciones</p>
                            </div>
                            <div class="rounded-2xl bg-orange-50 border border-orange-100 px-3 py-4">
                                <p class="text-xl font-bold text-orange-600">{{ $conteoComentarios }}</p>
                                <p class="text-xs text-gray-500 mt-1">Comentarios</p>
                            </div>
                            <div class="rounded-2xl bg-orange-50 border border-orange-100 px-3 py-4">
                                <p class="text-xl font-bold text-orange-600">{{ $conteoAdopciones }}</p>
                                <p class="text-xs text-gray-500 mt-1">Adopciones</p>
                            </div>
                        </div>

                        <div class="w-full mt-6 space-y-3 text-left">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Teléfono</p>
                                    <p class="font-medium text-gray-800">{{ $usuario->telefono }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2m-4 0H7a2 2 0 01-2-2v-6a2 2 0 012-2h6m4 0V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2m6 0H9"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">WhatsApp</p>
                                    <p class="font-medium text-gray-800">{{ $usuario->whatsapp ?: 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Ciudad</p>
                                    <p class="font-medium text-gray-800">{{ $usuario->ciudad ?: 'No registrada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-5">Cambiar foto</h3>

                    <form action="{{ route('perfil.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <input type="file" name="foto_perfil" accept="image/*" class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl px-4 py-3 bg-gray-50">
                            @error('foto_perfil')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                            Guardar nueva foto
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Editar información</h3>

                    <form action="{{ route('perfil.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @error('nombre')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Correo</label>
                            <input type="text" value="{{ $usuario->correo }}" disabled
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', $usuario->ciudad) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @error('ciudad')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @error('telefono')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $usuario->whatsapp) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @error('whatsapp')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Mis publicaciones</h3>
                            <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $publicaciones->count() }}</span>
                        </div>

                        <div class="space-y-4 flex-1">
                            @forelse($publicaciones as $pub)
                                @php
                                    $imagenUrl = $pub->imagen ? asset('storage/' . ltrim($pub->imagen, '/')) : null;

                                    $estadoClases = match ($pub->estado) {
                                        'ACTIVA', 'DISPONIBLE' => 'bg-yellow-100 text-yellow-700',
                                        'RESUELTA', 'ADOPTADA' => 'bg-green-100 text-green-700',
                                        'EN_PROCESO' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp

                                <a href="{{ $pub->url }}" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100 group">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                        @if($imagenUrl)
                                            <img src="{{ $imagenUrl }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs">
                                                Sin imagen
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 truncate">{{ $pub->titulo ?: 'Sin título' }}</h4>
                                        <p class="text-xs text-gray-500 mb-1">{{ $pub->tipo }}</p>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $estadoClases }}">
                                            {{ ucfirst(strtolower(str_replace('_', ' ', $pub->estado))) }}
                                        </span>
                                    </div>

                                    <div class="text-gray-300 group-hover:text-orange-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-10 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                    <p class="text-sm text-gray-500">Aún no tienes publicaciones.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('extravios.index') }}" class="text-orange-500 text-sm font-medium hover:underline">Ver mis reportes</a>
                            <span class="text-gray-300">•</span>
                            <a href="{{ route('adopciones.mis-adopciones') }}" class="text-orange-500 text-sm font-medium hover:underline">Ver mis adopciones</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Comentarios recientes</h3>
                            <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $comentarios->count() }}</span>
                        </div>

                        <div class="space-y-4 flex-1">
                            @forelse($comentarios as $com)
                                <a href="{{ $com->url }}" class="flex items-start gap-3 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-orange-50/40 transition">
                                    <div class="text-orange-400 mt-1 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 italic">"{{ \Illuminate\Support\Str::limit($com->texto, 110) }}"</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            En: {{ $com->contexto }} • {{ \Carbon\Carbon::parse($com->fecha)->locale('es')->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-10 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                    <p class="text-sm text-gray-500">Aún no has realizado comentarios.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-6 text-center">
                            <span class="text-gray-400 text-sm">Se muestran los comentarios más recientes</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Configuración del perfil</h3>
                    
                    <form action="{{ route('perfil.settings') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="mostrar_telefono_publico" value="1"
                                    {{ $usuario->mostrar_telefono_publico ? 'checked' : '' }}
                                    class="mt-1 rounded text-orange-500 focus:ring-orange-500 border-gray-300">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Mostrar teléfono públicamente</p>
                                    <p class="text-xs text-gray-500 mt-1">Permitirá que otros usuarios vean tu teléfono en tus publicaciones.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="mostrar_whatsapp_publico" value="1"
                                    {{ $usuario->mostrar_whatsapp_publico ? 'checked' : '' }}
                                    class="mt-1 rounded text-orange-500 focus:ring-orange-500 border-gray-300">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Mostrar WhatsApp públicamente</p>
                                    <p class="text-xs text-gray-500 mt-1">Habilita el contacto directo por WhatsApp cuando publiques.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="ocultar_ubicacion_exacta" value="1"
                                    {{ $usuario->ocultar_ubicacion_exacta ? 'checked' : '' }}
                                    class="mt-1 rounded text-orange-500 focus:ring-orange-500 border-gray-300">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Ocultar ubicación exacta</p>
                                    <p class="text-xs text-gray-500 mt-1">Muestra referencias generales en lugar de una ubicación precisa.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="recibir_notificaciones" value="1"
                                    {{ $usuario->recibir_notificaciones ? 'checked' : '' }}
                                    class="mt-1 rounded text-orange-500 focus:ring-orange-500 border-gray-300">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Recibir notificaciones</p>
                                    <p class="text-xs text-gray-500 mt-1">Te avisa sobre actividad relevante en tus publicaciones.</p>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition">
                                Guardar configuración
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/20 transition inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection