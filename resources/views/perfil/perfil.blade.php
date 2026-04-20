@extends('layout.app')

@section('content')
@php
    $iniciales = collect(preg_split('/\s+/', trim($usuario->nombre ?? 'Usuario')))
        ->filter()
        ->take(2)
        ->map(fn($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
        ->implode('');

    $esVeterinaria = $tipoPerfil === 'VETERINARIA';
    $esRefugio = $tipoPerfil === 'REFUGIO';
    $esInstitucional = $esVeterinaria || $esRefugio;

    if ($esVeterinaria) {
        $tema = [
            'gradiente' => 'from-sky-600 to-cyan-500',
            'soft' => 'bg-sky-50 border-sky-100 text-sky-700',
            'solid' => 'bg-sky-600 hover:bg-sky-700',
            'text' => 'text-sky-700',
            'ring' => 'focus:ring-sky-500',
            'border' => 'focus:border-sky-500',
            'avatar' => 'bg-sky-100 text-sky-700',
            'badge' => 'VETERINARIA',
        ];
    } elseif ($esRefugio) {
        $tema = [
            'gradiente' => 'from-emerald-600 to-green-500',
            'soft' => 'bg-emerald-50 border-emerald-100 text-emerald-700',
            'solid' => 'bg-emerald-600 hover:bg-emerald-700',
            'text' => 'text-emerald-700',
            'ring' => 'focus:ring-emerald-500',
            'border' => 'focus:border-emerald-500',
            'avatar' => 'bg-emerald-100 text-emerald-700',
            'badge' => 'REFUGIO',
        ];
    } else {
        $tema = [
            'gradiente' => 'from-orange-500 to-amber-500',
            'soft' => 'bg-orange-50 border-orange-100 text-orange-700',
            'solid' => 'bg-orange-500 hover:bg-orange-600',
            'text' => 'text-orange-700',
            'ring' => 'focus:ring-orange-500',
            'border' => 'focus:border-orange-500',
            'avatar' => 'bg-orange-100 text-orange-700',
            'badge' => 'USUARIO',
        ];
    }

    $totalPublicaciones = $conteoExtravios + $conteoAdopciones;

    $estadoPublicacionClase = function ($estado) {
        return match ($estado) {
            'ACTIVA', 'DISPONIBLE', 'APROBADO' => 'bg-green-100 text-green-700',
            'EN_PROCESO', 'PENDIENTE', 'ENVIADA' => 'bg-yellow-100 text-yellow-700',
            'RESUELTA', 'ADOPTADA', 'ACEPTADA' => 'bg-blue-100 text-blue-700',
            'RECHAZADA', 'ELIMINADA', 'PAUSADA' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-600',
        };
    };
@endphp

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 rounded-3xl bg-gradient-to-r {{ $tema['gradiente'] }} p-8 text-white shadow-lg">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold tracking-wide uppercase">
                        Perfil de cuenta
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold mt-4">{{ $usuario->nombre }}</h1>
                    <p class="text-white/90 mt-2">{{ $usuario->correo }}</p>

                    @if($esInstitucional && $organizacion)
                        <div class="mt-4 flex flex-wrap gap-3">
                            <span class="inline-flex items-center rounded-full bg-white/15 px-4 py-2 text-sm font-semibold">
                                {{ data_get($organizacion, 'nombre') ?: 'Organización' }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-white/15 px-4 py-2 text-sm font-semibold">
                                Estado: {{ data_get($organizacion, 'estado_revision') ?: 'Sin estado' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <div class="h-24 w-24 rounded-full bg-white/15 border-4 border-white/30 flex items-center justify-center text-3xl font-bold">
                        {{ $iniciales ?: 'U' }}
                    </div>
                </div>
            </div>
        </div>

        @if(session('success_profile'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success_profile') }}
            </div>
        @endif

        @if(session('success_settings'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success_settings') }}
            </div>
        @endif

        @if(session('error_profile'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error_profile') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-28 h-28 rounded-full {{ $tema['avatar'] }} flex items-center justify-center text-3xl font-bold shadow-sm">
                            {{ $iniciales ?: 'U' }}
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mt-5">{{ $usuario->nombre }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $usuario->correo }}</p>

                        <div class="mt-4">
                            <span class="inline-flex items-center rounded-full px-4 py-2 text-xs font-bold {{ $tema['soft'] }}">
                                {{ $tema['badge'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 w-full mt-6">
                            <div class="rounded-2xl {{ $tema['soft'] }} px-3 py-4 border">
                                <p class="text-xl font-bold">{{ $totalPublicaciones }}</p>
                                <p class="text-xs mt-1">Publicaciones</p>
                            </div>

                            <div class="rounded-2xl {{ $tema['soft'] }} px-3 py-4 border">
                                <p class="text-xl font-bold">{{ $conteoComentarios }}</p>
                                <p class="text-xs mt-1">Comentarios</p>
                            </div>

                            @if($esInstitucional)
                                <div class="rounded-2xl {{ $tema['soft'] }} px-3 py-4 border">
                                    <p class="text-xl font-bold">{{ $conteoConsejos }}</p>
                                    <p class="text-xs mt-1">Consejos</p>
                                </div>

                                <div class="rounded-2xl {{ $tema['soft'] }} px-3 py-4 border">
                                    <p class="text-xl font-bold">{{ $conteoSolicitudesRecibidas }}</p>
                                    <p class="text-xs mt-1">Solicitudes recibidas</p>
                                </div>
                            @else
                                <div class="rounded-2xl {{ $tema['soft'] }} px-3 py-4 border">
                                    <p class="text-xl font-bold">{{ $conteoAdopciones }}</p>
                                    <p class="text-xs mt-1">Adopciones</p>
                                </div>

                                <div class="rounded-2xl {{ $tema['soft'] }} px-3 py-4 border">
                                    <p class="text-xl font-bold">{{ $conteoSolicitudesEnviadas }}</p>
                                    <p class="text-xs mt-1">Solicitudes enviadas</p>
                                </div>
                            @endif
                        </div>

                        <div class="w-full mt-6 space-y-3 text-left">
                            <div class="flex items-center gap-3">
                                <div class="{{ $tema['soft'] }} p-2 rounded-full border">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Teléfono</p>
                                    <p class="font-medium text-gray-800">{{ $usuario->telefono ?? 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="{{ $tema['soft'] }} p-2 rounded-full border">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2m-4 0H7a2 2 0 01-2-2v-6a2 2 0 012-2h6m4 0V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2m6 0H9"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">WhatsApp</p>
                                    <p class="font-medium text-gray-800">{{ $usuario->whatsapp ?? 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="{{ $tema['soft'] }} p-2 rounded-full border">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Ciudad</p>
                                    <p class="font-medium text-gray-800">{{ $usuario->ciudad ?? 'No registrada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-5">Privacidad actual</h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Mostrar teléfono</span>
                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $configuracion->mostrar_telefono_publico ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $configuracion->mostrar_telefono_publico ? 'Activo' : 'Oculto' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Mostrar WhatsApp</span>
                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $configuracion->mostrar_whatsapp_publico ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $configuracion->mostrar_whatsapp_publico ? 'Activo' : 'Oculto' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Ubicación exacta</span>
                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $configuracion->ocultar_ubicacion_exacta ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ $configuracion->ocultar_ubicacion_exacta ? 'Oculta' : 'Visible' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Notificaciones</span>
                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $configuracion->recibir_notificaciones ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $configuracion->recibir_notificaciones ? 'Activadas' : 'Desactivadas' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Correos</span>
                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $configuracion->recibir_correos ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $configuracion->recibir_correos ? 'Activados' : 'Desactivados' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($esInstitucional && $organizacion)
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 mb-5">Información institucional</h3>

                        <div class="space-y-3 text-sm">
                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">Organización</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ data_get($organizacion, 'nombre') ?: 'No registrada' }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">Tipo</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ data_get($organizacion, 'tipo') ?: 'No disponible' }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">Estado de revisión</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ data_get($organizacion, 'estado_revision') ?: 'No disponible' }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">Teléfono institucional</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ data_get($organizacion, 'telefono') ?: 'No registrado' }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">WhatsApp institucional</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ data_get($organizacion, 'whatsapp') ?: 'No registrado' }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">Sitio web</p>
                                <p class="text-gray-800 font-semibold mt-1 break-all">{{ data_get($organizacion, 'sitio_web') ?: 'No registrado' }}</p>
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                <p class="text-gray-400 uppercase text-xs font-bold">Ubicación</p>
                                <p class="text-gray-800 font-semibold mt-1">
                                    {{ data_get($organizacion, 'calle_numero') ?: 'Sin calle' }},
                                    {{ data_get($organizacion, 'colonia') ?: 'Sin colonia' }},
                                    {{ data_get($organizacion, 'ciudad_direccion') ?: 'Sin ciudad' }},
                                    {{ data_get($organizacion, 'estado_direccion') ?: 'Sin estado' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            @if($panelOrganizacionUrl)
                                <a href="{{ $panelOrganizacionUrl }}" class="inline-flex items-center justify-center px-4 py-3 rounded-xl text-white font-semibold {{ $tema['solid'] }} transition">
                                    Ir a mi panel
                                </a>
                            @endif

                            @if($perfilPublicoUrl)
                                <a href="{{ $perfilPublicoUrl }}" class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">
                                    Ver perfil público
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Editar información personal</h3>

                    <form action="{{ route('perfil.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 {{ $tema['ring'] }} {{ $tema['border'] }}">
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
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 {{ $tema['ring'] }} {{ $tema['border'] }}">
                            @error('ciudad')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 {{ $tema['ring'] }} {{ $tema['border'] }}">
                            @error('telefono')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $usuario->whatsapp) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 {{ $tema['ring'] }} {{ $tema['border'] }}">
                            @error('whatsapp')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm transition {{ $tema['solid'] }}">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">{{ $esInstitucional ? 'Actividad reciente' : 'Mis publicaciones' }}</h3>
                            <span class="text-white text-xs font-bold px-2 py-1 rounded-full {{ $tema['solid'] }}">
                                {{ $publicaciones->count() }}
                            </span>
                        </div>

                        <div class="space-y-4 flex-1">
                            @forelse($publicaciones as $pub)
                                @php
                                    $imagenUrl = $pub->imagen ? asset('storage/' . ltrim($pub->imagen, '/')) : null;
                                @endphp

                                <a href="{{ $pub->url }}" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100 group">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                        @if($imagenUrl)
                                            <img src="{{ $imagenUrl }}" class="w-full h-full object-cover" alt="">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs">
                                                Sin imagen
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 truncate">{{ $pub->titulo ?: 'Sin título' }}</h4>
                                        <p class="text-xs text-gray-500 mb-1">{{ $pub->tipo }}</p>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $estadoPublicacionClase($pub->estado) }}">
                                            {{ ucfirst(strtolower(str_replace('_', ' ', $pub->estado))) }}
                                        </span>
                                    </div>

                                    <div class="text-gray-300 transition {{ $tema['text'] }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-10 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                    <p class="text-sm text-gray-500">Aún no hay actividad para mostrar.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('extravios.index') }}" class="text-sm font-medium {{ $tema['text'] }} hover:underline">Ver mis reportes</a>
                            <span class="text-gray-300">•</span>
                            <a href="{{ route('adopciones.mis-adopciones') }}" class="text-sm font-medium {{ $tema['text'] }} hover:underline">Ver mis adopciones</a>
                            @if($esInstitucional && $panelOrganizacionUrl)
                                <span class="text-gray-300">•</span>
                                <a href="{{ $panelOrganizacionUrl }}" class="text-sm font-medium {{ $tema['text'] }} hover:underline">Ir al panel institucional</a>
                            @endif
                        </div>
                    </div>

                    @if($esInstitucional)
                        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800">Solicitudes recibidas</h3>
                                <span class="text-white text-xs font-bold px-2 py-1 rounded-full {{ $tema['solid'] }}">
                                    {{ $solicitudesRecibidas->count() }}
                                </span>
                            </div>

                            <div class="space-y-4 flex-1">
                                @forelse($solicitudesRecibidas as $solicitud)
                                    <a href="{{ $solicitud->url }}" class="flex items-start gap-3 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white transition">
                                        <div class="{{ $tema['soft'] }} mt-1 flex-shrink-0 p-2 rounded-full border">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 017 17h10a4 4 0 011.879.468M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">
                                                {{ $solicitud->nombre_completo }} solicitó adoptar a {{ $solicitud->mascota }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($solicitud->fecha)->locale('es')->diffForHumans() }}
                                            </p>
                                            <span class="inline-flex mt-2 text-[10px] font-bold px-2 py-1 rounded-full {{ $estadoPublicacionClase($solicitud->estado) }}">
                                                {{ ucfirst(strtolower($solicitud->estado)) }}
                                            </span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-10 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                        <p class="text-sm text-gray-500">Aún no hay solicitudes recibidas.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-6 text-center">
                                <span class="text-gray-400 text-sm">Se muestran las solicitudes más recientes</span>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800">Comentarios recientes</h3>
                                <span class="text-white text-xs font-bold px-2 py-1 rounded-full {{ $tema['solid'] }}">
                                    {{ $comentarios->count() }}
                                </span>
                            </div>

                            <div class="space-y-4 flex-1">
                                @forelse($comentarios as $com)
                                    <a href="{{ $com->url }}" class="flex items-start gap-3 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white transition">
                                        <div class="{{ $tema['soft'] }} mt-1 flex-shrink-0 p-2 rounded-full border">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
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
                    @endif
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Configuración del perfil</h3>

                    <form action="{{ route('perfil.settings') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="mostrar_telefono_publico" value="1"
                                    {{ $configuracion->mostrar_telefono_publico ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 {{ $tema['text'] }}">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Mostrar teléfono públicamente</p>
                                    <p class="text-xs text-gray-500 mt-1">Permitirá que otros usuarios vean tu teléfono donde aplique.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="mostrar_whatsapp_publico" value="1"
                                    {{ $configuracion->mostrar_whatsapp_publico ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 {{ $tema['text'] }}">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Mostrar WhatsApp públicamente</p>
                                    <p class="text-xs text-gray-500 mt-1">Habilita el contacto directo por WhatsApp cuando corresponda.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="ocultar_ubicacion_exacta" value="1"
                                    {{ $configuracion->ocultar_ubicacion_exacta ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 {{ $tema['text'] }}">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Ocultar ubicación exacta</p>
                                    <p class="text-xs text-gray-500 mt-1">Muestra referencias generales en lugar de una ubicación precisa.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="recibir_notificaciones" value="1"
                                    {{ $configuracion->recibir_notificaciones ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 {{ $tema['text'] }}">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Recibir notificaciones</p>
                                    <p class="text-xs text-gray-500 mt-1">Te avisa sobre actividad importante en la plataforma.</p>
                                </div>
                            </label>

                            <label class="md:col-span-2 flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="recibir_correos" value="1"
                                    {{ $configuracion->recibir_correos ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 {{ $tema['text'] }}">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Recibir correos electrónicos</p>
                                    <p class="text-xs text-gray-500 mt-1">Recibe avisos por correo cuando haya cambios importantes en tus publicaciones, solicitudes o revisiones.</p>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="text-white font-bold py-3 px-8 rounded-xl shadow-sm transition {{ $tema['solid'] }}">
                                Guardar configuración
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-white font-bold py-3 px-8 rounded-xl shadow-lg transition inline-flex items-center gap-2 {{ $tema['solid'] }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection