@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Mi perfil</h1>
            <p class="text-gray-500">Gestiona tu información y publicaciones.</p>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm mb-8 border border-gray-100 relative overflow-hidden">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg">
                        <img src="{{ $usuario['foto'] }}" alt="Foto de perfil" class="w-full h-full object-cover">
                    </div>
                    <button class="absolute bottom-2 right-2 bg-orange-500 text-white p-2 rounded-full shadow-md hover:bg-orange-600 transition" title="Cambiar foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                    <p class="text-xs text-orange-500 text-center mt-2 font-medium cursor-pointer hover:underline">Cambiar foto</p>
                </div>

                <div class="flex-1 text-center md:text-left space-y-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $usuario['nombre'] }}</h2>
                    
                    <div class="space-y-2 text-gray-600">
                        <div class="flex items-center justify-center md:justify-start gap-3">
                            <div class="bg-orange-100 p-1.5 rounded-full text-orange-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Correo electrónico</p>
                                <p class="font-medium">{{ $usuario['email'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center md:justify-start gap-3">
                            <div class="bg-orange-100 p-1.5 rounded-full text-orange-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Teléfono / WhatsApp</p>
                                <p class="font-medium">{{ $usuario['telefono'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center md:justify-start gap-3">
                            <div class="bg-orange-100 p-1.5 rounded-full text-orange-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Ubicación</p>
                                <p class="font-medium">{{ $usuario['ubicacion'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition flex items-center gap-2 mx-auto md:mx-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Editar información
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Mis publicaciones</h3>
                    <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ count($publicaciones) }}</span>
                </div>

                <div class="space-y-4 flex-1">
                    @foreach($publicaciones as $pub)
                    <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100 cursor-pointer group">
                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="{{ $pub['imagen'] }}" class="w-full h-full object-cover">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 truncate">{{ $pub['titulo'] }}</h4>
                            <p class="text-xs text-gray-500 mb-1">{{ $pub['tipo'] }}</p>
                            
                            @if($pub['estado'] == 'Activa')
                                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Activa</span>
                            @elseif($pub['estado'] == 'Publicada')
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Publicada</span>
                            @elseif($pub['estado'] == 'Resuelta')
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Resuelta</span>
                            @endif
                        </div>

                        <div class="text-gray-300 group-hover:text-orange-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6 text-center">
                    <a href="#" class="text-orange-500 text-sm font-medium hover:underline">Ver todas mis publicaciones</a>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Comentarios realizados</h3>
                    <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ count($comentarios) }}</span>
                </div>

                <div class="space-y-4 flex-1">
                    @foreach($comentarios as $com)
                    <div class="flex items-start gap-3 p-4 rounded-2xl bg-gray-50 border border-gray-100 relative group">
                        <div class="text-orange-400 mt-1 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 italic">"{{ $com['texto'] }}"</p>
                            <p class="text-xs text-gray-400 mt-1">En: {{ $com['contexto'] }} • {{ $com['fecha'] }}</p>
                        </div>
                        <button class="absolute top-3 right-3 text-gray-300 hover:text-red-500 transition" title="Eliminar comentario">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 text-center">
                    <a href="#" class="text-orange-500 text-sm font-medium hover:underline">Ver todos los comentarios</a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Configuración del perfil</h3>
            
            <div class="space-y-1 divide-y divide-gray-100">
                <a href="#" class="flex items-center gap-4 py-4 hover:bg-gray-50 px-4 rounded-xl transition group">
                    <div class="bg-orange-50 p-2 rounded-full text-orange-500 group-hover:bg-orange-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-900">Cambiar contraseña</h4>
                        <p class="text-xs text-gray-500">Actualiza tu contraseña de acceso</p>
                    </div>
                    <div class="text-gray-300 group-hover:text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>

                <div class="py-4 px-4">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="bg-orange-50 p-2 rounded-full text-orange-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-900">Configurar privacidad</h4>
                            <p class="text-xs text-gray-500">Controla qué información es visible</p>
                        </div>
                    </div>
                    <div class="ml-14 space-y-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-orange-500 focus:ring-orange-500 h-4 w-4 border-gray-300">
                            <span class="text-sm text-gray-600 font-medium">Mostrar número público</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-orange-500 focus:ring-orange-500 h-4 w-4 border-gray-300">
                            <span class="text-sm text-gray-600 font-medium">Mostrar WhatsApp</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" class="rounded text-orange-500 focus:ring-orange-500 h-4 w-4 border-gray-300">
                            <span class="text-sm text-gray-600 font-medium">Ocultar ubicación exacta</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-4 py-4 px-4 hover:bg-gray-50 rounded-xl transition">
                    <div class="bg-orange-50 p-2 rounded-full text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-900">Notificaciones</h4>
                        <p class="text-xs text-gray-500">Recibe alertas sobre tus publicaciones</p>
                    </div>
                    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="toggle" id="toggle" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 right-5"/>
                        <label for="toggle" class="toggle-label block overflow-hidden h-5 rounded-full bg-orange-500 cursor-pointer"></label>
                    </div>
                </div>

                <a href="#" class="flex items-center gap-4 py-4 px-4 hover:bg-red-50 rounded-xl transition group">
                    <div class="bg-red-50 p-2 rounded-full text-red-500 group-hover:bg-red-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-red-500">Eliminar mi cuenta</h4>
                        <p class="text-xs text-red-400 opacity-70">Esta acción es permanente</p>
                    </div>
                    <div class="text-red-300 group-hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center mb-12">
             <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 transition flex items-center gap-2 mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar sesión
                </button>
             </form>
        </div>

    </div>
</div>

<style>
    /* Estilos extra para el toggle switch de notificaciones */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #f97316; /* orange-500 */
    }
    .toggle-checkbox {
        right: 0;
        transition: all 0.3s;
        border-color: #e5e7eb; /* gray-200 */
    }
    .toggle-label {
        width: 2.5rem;
    }
</style>
@endsection