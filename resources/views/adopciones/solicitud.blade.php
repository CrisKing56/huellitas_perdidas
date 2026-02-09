@extends('layout.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto">
        
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-orange-500 font-medium mb-6 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Volver al menú
        </a>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Solicitud de adopción</h1>
        <p class="text-gray-500 mb-8">Completa este formulario para solicitar la adopción responsable de una mascota.</p>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 flex items-center border border-gray-100">
            <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 mr-4">
                <img src="https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?q=80&w=200&auto=format&fit=crop" 
                     alt="Mascota" class="w-full h-full object-cover">
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $mascota->nombre ?? 'Rocky' }}</h2>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">En adopción</span>
                </div>
                <p class="text-gray-500 text-sm">{{ $mascota->otra_raza ?? 'Beagle' }} - {{ $mascota->edad_anios ?? 2 }} años - {{ $mascota->sexo ?? 'Macho' }}</p>
            </div>
        </div>

        <form action="#" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-10">
            @csrf

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold">Datos personales</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre_completo" placeholder="Ej: María González López" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Edad <span class="text-red-500">*</span></label>
                        <input type="number" name="edad" placeholder="Ej: 28" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono <span class="text-red-500">*</span></label>
                        <input type="tel" name="telefono" placeholder="Ej: +52 123 456 789" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="correo" placeholder="Ej: maria@ejemplo.com" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ocupación <span class="text-red-500">*</span></label>
                        <input type="text" name="ocupacion" placeholder="Ej: Maestra" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado civil</label>
                        <select name="estado_civil" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                            <option value="">Selecciona...</option>
                            <option value="SOLTERO">Soltero/a</option>
                            <option value="CASADO">Casado/a</option>
                            <option value="UNION_LIBRE">Unión Libre</option>
                        </select>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold">Información de vivienda</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de vivienda <span class="text-red-500">*</span></label>
                        <select name="tipo_vivienda" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                            <option value="CASA">Casa</option>
                            <option value="DEPARTAMENTO">Departamento</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">¿Es propia o rentada? <span class="text-red-500">*</span></label>
                        <select name="propia_o_rentada" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                            <option value="PROPIA">Propia</option>
                            <option value="RENTADA">Rentada</option>
                            <option value="FAMILIAR">Familiar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">¿Tiene jardín o patio? <span class="text-red-500">*</span></label>
                        <select name="tiene_patio" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Número de integrantes <span class="text-red-500">*</span></label>
                        <input type="text" name="num_integrantes" placeholder="Ej: 3" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-500 mb-2">¿Todos están de acuerdo con la adopción? <span class="text-red-500">*</span></label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="todos_de_acuerdo" value="1" class="text-orange-500 focus:ring-orange-500">
                            <span class="text-gray-700">Sí</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="todos_de_acuerdo" value="0" class="text-orange-500 focus:ring-orange-500">
                            <span class="text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold">Experiencia con mascotas</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">¿Ha tenido mascotas anteriormente? <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tuvo_mascotas" value="1" class="text-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">Sí</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tuvo_mascotas" value="0" class="text-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">No</span>
                            </label>
                        </div>
                        <input type="text" name="tipo_mascotas_antes" placeholder="Si respondió sí, especifique qué tipo de mascota(s)" class="w-full rounded-lg border-gray-200 py-2 px-4 text-sm bg-gray-50/50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">¿Tiene mascotas actualmente? <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tiene_mascotas_actualmente" value="1" class="text-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">Sí</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tiene_mascotas_actualmente" value="0" class="text-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">No</span>
                            </label>
                        </div>
                        <input type="text" name="mascotas_actuales_detalle" placeholder="Si respondió sí, ¿cuántas y de qué tipo?" class="w-full rounded-lg border-gray-200 py-2 px-4 text-sm bg-gray-50/50">
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold">Motivo de adopción</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">¿Por qué desea adoptar a esta mascota? <span class="text-red-500">*</span></label>
                        <textarea name="motivo_adopcion" rows="3" placeholder="Comparte tus razones para adoptar..." class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 p-4 text-gray-700 bg-gray-50/50"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">¿Está dispuesto/a a cubrir los gastos veterinarios? <span class="text-red-500">*</span></label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="cubrir_gastos_vet" value="1" class="text-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">Sí</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="cubrir_gastos_vet" value="0" class="text-orange-500 focus:ring-orange-500">
                                <span class="text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">¿Cuántas horas al día estará solo/a la mascota? <span class="text-red-500">*</span></label>
                        <input type="text" name="horas_sola" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 text-gray-700 bg-gray-50/50">
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-full text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold">Información adicional</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">¿Qué haría si la mascota presenta problemas de comportamiento?</label>
                        <textarea name="que_haria_problemas_comportamiento" rows="2" placeholder="Describe cómo manejarías esta situación..." class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 p-4 text-gray-700 bg-gray-50/50"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Comentarios adicionales</label>
                        <textarea name="comentarios_adicionales" rows="2" placeholder="Cualquier información adicional que desees compartir..." class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 p-4 text-gray-700 bg-gray-50/50"></textarea>
                    </div>
                </div>
            </section>

            <div class="bg-orange-50 border border-orange-100 rounded-xl p-6">
                <h4 class="font-bold text-gray-800 mb-2 text-sm">Compromiso de adopción responsable</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Acepto que toda la información proporcionada es verídica y me comprometo a brindar un hogar responsable, amoroso y permanente a la mascota. Entiendo que el refugio puede hacer seguimiento y que debo notificar cualquier cambio en la situación de la mascota.
                </p>
            </div>

            <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                <a href="{{ url()->previous() }}" class="px-6 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-orange-500 text-white font-medium hover:bg-orange-600 shadow-md transition">
                    Enviar solicitud
                </button>
            </div>

        </form>
    </div>
</div>
@endsection