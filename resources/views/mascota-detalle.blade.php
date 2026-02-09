@extends('layout.app')

@section('title', 'Detalle de Mascota - Max')

@section('content')
<div class="container mx-auto px-6 py-8">

    <div class="mb-6">
        <span class="bg-red-100 text-red-500 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">Perdida</span>
        <h1 class="text-4xl font-bold text-gray-900 mt-2">Max</h1>
        <p class="text-gray-500 text-lg">Perro - Golden Retriever</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="relative w-full h-96 bg-gray-200 rounded-2xl overflow-hidden group">
                <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Max" class="w-full h-full object-cover">
                
                <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 p-2 rounded-full hover:bg-white transition shadow-sm">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 p-2 rounded-full hover:bg-white transition shadow-sm">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <div class="w-2.5 h-2.5 bg-white rounded-full shadow"></div>
                    <div class="w-2.5 h-2.5 bg-white/50 rounded-full shadow"></div>
                    <div class="w-2.5 h-2.5 bg-white/50 rounded-full shadow"></div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Información de la Mascota</h3>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Fecha de Extravío</p>
                            <p class="text-gray-900 font-medium">28 de Noviembre, 2025</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Último Avistamiento</p>
                            <p class="text-gray-900 font-medium">Parque del Retiro, Madrid</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Descripción</p>
                            <p class="text-gray-700 text-sm leading-relaxed mt-1">
                                Max es un Golden Retriever macho de 3 años. Es muy amigable y responde a su nombre. Llevaba un collar azul marino al momento de extraviarse. Es muy sociable con personas y otros perros. Puede estar asustado, pero no es agresivo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Ubicación del Último Avistamiento</h3>
                
                <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex items-center justify-center bg-[url('https://maps.googleapis.com/maps/api/staticmap?center=40.416775,-3.703790&zoom=14&size=600x300&style=feature:all|element:labels|visibility:off&key=YOUR_API_KEY_HERE')] bg-cover bg-center">
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <div class="bg-primary/20 p-4 rounded-full animate-pulse">
                            <div class="bg-white p-2 rounded-full shadow-lg">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute top-4 left-4 bg-white p-3 rounded-lg shadow-md flex items-center gap-3 pr-6">
                         <div class="bg-primary rounded-full p-2 text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                         </div>
                         <div>
                             <p class="text-xs font-bold text-gray-800">Último avistamiento</p>
                             <p class="text-xs text-gray-500">Parque del Retiro, Madrid</p>
                         </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="#" class="text-primary text-sm font-medium hover:underline flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        Ver en mapa completo
                    </a>
                    <p class="text-xs text-gray-400 mt-2">Mueve el mapa para explorar la zona del avistamiento</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Comentarios</h3>
                
                <div class="mb-8">
                    <textarea class="w-full border border-gray-200 rounded-lg p-4 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none resize-none h-24" placeholder="Escribe un comentario si tienes información sobre esta mascota..."></textarea>
                    <div class="flex justify-end mt-2">
                        <button class="bg-primary text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-orange-600 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Comentar
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                        <div class="flex gap-3 mb-2">
                            <div class="w-8 h-8 rounded-full bg-orange-400 text-white flex items-center justify-center text-xs font-bold">M</div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">María López <span class="text-xs text-gray-400 font-normal ml-2">hace 2 horas</span></p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700 ml-11">Creo que vi a este perrito cerca del parque ayer por la tarde. Intenté acercarme pero salió corriendo.</p>
                        <button class="ml-11 mt-2 text-xs text-primary font-medium hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg> Responder
                        </button>

                        <div class="ml-11 mt-3 pl-4 border-l-2 border-gray-200">
                            <div class="flex gap-3 mb-1">
                                <div class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">J</div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Juan García <span class="text-xs text-gray-400 font-normal ml-2">hace 1 hora</span></p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 ml-9">¡Gracias María! ¿A qué hora más o menos? Voy a revisar esa zona.</p>
                        </div>
                    </div>

                    <div class="border border-gray-100 rounded-lg p-4 bg-white">
                        <div class="flex gap-3 mb-2">
                            <div class="w-8 h-8 rounded-full bg-orange-400 text-white flex items-center justify-center text-xs font-bold">C</div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Carlos Rodríguez <span class="text-xs text-gray-400 font-normal ml-2">hace 5 horas</span></p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700 ml-11">Compartido en mi grupo de vecinos. Esperemos que aparezca pronto.</p>
                         <button class="ml-11 mt-2 text-xs text-primary font-medium hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg> Responder
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Información de Contacto</h3>
                
                <div class="flex items-center gap-4 mb-6 p-3 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg">C</div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Dueño</p>
                        <p class="font-bold text-gray-900">Carlos Martínez</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-6">
                     <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                     </div>
                     <div>
                         <p class="text-xs text-gray-400">Teléfono</p>
                         <p class="text-sm font-semibold text-gray-800">+34 612 345 678</p>
                     </div>
                </div>

                <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow transition flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.897.003-6.171 5.02-11.192 11.196-11.192 3.029.003 5.86 1.187 7.968 3.326 2.148 2.151 3.308 5.015 3.298 8.019-.023 6.16-5.068 11.168-11.246 11.168-.96 0-1.977-.145-2.999-.467L.057 24zm2.233-4.232l.54.321c2.119 1.259 4.384 1.332 5.166 1.325.292-.003.52-.027.653-.041l.36-.039c3.344-.366 5.923-3.084 6.273-6.613l.014-.146c.009-.092.015-.179.015-.275.02-2.316-.922-4.494-2.656-6.131-1.696-1.602-3.926-2.486-6.279-2.488-5.023 0-9.109 4.087-9.112 9.111-.002 1.839.544 3.593 1.58 5.111l.325.48-1.085 3.961 4.206-1.125z"></path></svg>
                    Contactar por WhatsApp
                </button>
                
                <hr class="my-6 border-gray-100">

                <p class="text-xs text-gray-500 mb-2">Compartir o descargar</p>
                <div class="grid grid-cols-2 gap-3">
                    <button class="flex items-center justify-center gap-2 py-2 px-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-600 text-sm font-medium transition border border-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        PDF
                    </button>
                    <button class="flex items-center justify-center gap-2 py-2 px-4 bg-orange-50 hover:bg-orange-100 rounded-lg text-primary text-sm font-medium transition border border-orange-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        Compartir
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection