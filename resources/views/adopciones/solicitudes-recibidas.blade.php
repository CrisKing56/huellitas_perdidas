@extends('layout.app')

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        <div class="mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center rounded-full bg-green-50 text-green-600 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-green-100">
                            Panel personal
                        </span>

                        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                            Solicitudes recibidas
                        </h1>

                        <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                            Aquí puedes revisar y responder las solicitudes enviadas a tus mascotas en adopción.
                        </p>

                        @if(isset($publicacionFiltro) && $publicacionFiltro)
                            <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-blue-50 text-blue-700 px-4 py-2 text-sm font-semibold border border-blue-100">
                                Filtrando por: {{ $publicacionFiltro->nombre }}
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if(isset($publicacionFiltro) && $publicacionFiltro)
                            <a href="{{ route('adopciones.solicitudes.recibidas') }}"
                               class="inline-flex items-center justify-center px-5 py-3 bg-blue-50 text-blue-700 font-medium rounded-2xl hover:bg-blue-100 transition">
                                Ver todas las solicitudes
                            </a>
                        @endif

                        <a href="{{ route('adopciones.mis-adopciones') }}"
                           class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 font-medium rounded-2xl hover:bg-gray-200 transition">
                            Volver a mis adopciones
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6">
            @forelse($solicitudes as $solicitud)
                @php
                    $mascota = $solicitud->publicacion;

                    $clasesEstado = [
                        'ENVIADA' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'ACEPTADA' => 'bg-green-100 text-green-700 border-green-200',
                        'RECHAZADA' => 'bg-red-100 text-red-700 border-red-200',
                        'CANCELADA' => 'bg-gray-100 text-gray-700 border-gray-200',
                    ];

                    $claseEstado = $clasesEstado[$solicitud->estado] ?? 'bg-gray-100 text-gray-700 border-gray-200';

                    $telefonoSolicitante = $solicitud->solicitante->telefono ?? 'No disponible';
                    $correoSolicitante = $solicitud->solicitante->correo ?? $solicitud->solicitante->email ?? 'No disponible';

                    $telefonoLimpio = $solicitud->solicitante && ($solicitud->solicitante->telefono ?? $solicitud->solicitante->whatsapp)
                        ? preg_replace('/\D+/', '', ($solicitud->solicitante->whatsapp ?? $solicitud->solicitante->telefono))
                        : null;

                    if ($telefonoLimpio && strlen($telefonoLimpio) === 10) {
                        $telefonoLimpio = '52' . $telefonoLimpio;
                    }

                    $whatsappSolicitante = $telefonoLimpio ? 'https://wa.me/' . $telefonoLimpio . '?text=' . urlencode('Hola, te contacto porque tu solicitud para adoptar a ' . ($mascota->nombre ?? 'la mascota') . ' fue aceptada.') : null;
                @endphp

                <article class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col lg:flex-row gap-6">

                            <div class="w-full lg:w-44 shrink-0">
                                <div class="w-full h-52 lg:h-44 rounded-2xl overflow-hidden border border-gray-100 bg-gray-100">
                                    @if($mascota && $mascota->fotoPrincipal)
                                        <img src="{{ asset('storage/' . $mascota->fotoPrincipal->url) }}"
                                             class="w-full h-full object-cover"
                                             alt="{{ $mascota->nombre }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <span class="text-sm">Sin imagen</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-3 mb-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide border {{ $claseEstado }}">
                                                {{ $solicitud->estado }}
                                            </span>

                                            <span class="text-sm text-gray-400">
                                                Recibida {{ \Carbon\Carbon::parse($solicitud->created_at)->locale('es')->diffForHumans() }}
                                            </span>
                                        </div>

                                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">
                                            {{ $solicitud->nombre_completo }}
                                        </h2>

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                            <span>{{ $solicitud->edad }} años</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span>{{ str_replace('_', ' ', $solicitud->estado_civil) }}</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span>{{ str_replace('_', ' ', $solicitud->tipo_vivienda) }}</span>
                                        </div>

                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
                                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                                <span class="font-semibold text-gray-800">Patio:</span>
                                                {{ $solicitud->tiene_patio ? 'Sí' : 'No' }}
                                            </div>

                                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                                <span class="font-semibold text-gray-800">Todos están de acuerdo:</span>
                                                {{ $solicitud->todos_de_acuerdo ? 'Sí' : 'No' }}
                                            </div>
                                        </div>

                                        <div class="mt-4 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-4">
                                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2">Motivo de adopción</p>
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $solicitud->motivo_adopcion }}</p>
                                        </div>

                                        @if($mascota)
                                            <p class="mt-4 text-sm text-gray-500">
                                                Mascota solicitada:
                                                <a href="{{ route('adopciones.show', $mascota->id_publicacion) }}" class="font-semibold text-green-600 hover:text-green-700">
                                                    {{ $mascota->nombre }}
                                                </a>
                                            </p>
                                        @endif

                                        @if($solicitud->estado === 'ACEPTADA')
                                            <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 p-4">
                                                <p class="text-sm font-semibold text-green-800 mb-3">
                                                    Esta solicitud fue aceptada. Ya puedes ponerte en contacto con el solicitante.
                                                </p>

                                                <div class="space-y-2 text-sm text-gray-700">
                                                    <p><span class="font-semibold">Teléfono:</span> {{ $telefonoSolicitante }}</p>
                                                    <p><span class="font-semibold">Correo:</span> {{ $correoSolicitante }}</p>
                                                </div>

                                                <div class="mt-4 flex flex-wrap gap-3">
                                                    @if($whatsappSolicitante)
                                                        <a href="{{ $whatsappSolicitante }}"
                                                           target="_blank"
                                                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-[#25D366] text-white font-semibold text-sm hover:bg-[#20bd5a] transition">
                                                            Escribir por WhatsApp
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($solicitud->estado === 'RECHAZADA')
                                            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                                                <p class="text-sm text-red-700">
                                                    Esta solicitud ya fue rechazada.
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($solicitud->estado === 'ENVIADA')
                                        <div class="shrink-0 flex flex-col gap-3">
                                            <form action="{{ route('adopciones.solicitudes.updateEstado', $solicitud->id_solicitud) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="ACEPTADA">
                                                @if(isset($publicacionFiltro) && $publicacionFiltro)
                                                    <input type="hidden" name="publicacion" value="{{ $publicacionFiltro->id_publicacion }}">
                                                @endif
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-green-600 text-white font-semibold text-sm hover:bg-green-700 transition">
                                                    Aceptar
                                                </button>
                                            </form>

                                            <form action="{{ route('adopciones.solicitudes.updateEstado', $solicitud->id_solicitud) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="RECHAZADA">
                                                @if(isset($publicacionFiltro) && $publicacionFiltro)
                                                    <input type="hidden" name="publicacion" value="{{ $publicacionFiltro->id_publicacion }}">
                                                @endif
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-red-50 text-red-600 border border-red-100 font-semibold text-sm hover:bg-red-100 transition">
                                                    Rechazar
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-14 bg-white rounded-3xl shadow-sm border border-dashed border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">No tienes solicitudes recibidas todavía</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        Cuando alguien solicite adoptar una de tus mascotas, aparecerá aquí.
                    </p>
                </div>
            @endforelse
        </div>

        @if($solicitudes->hasPages())
            <div class="mt-8">
                {{ $solicitudes->links() }}
            </div>
        @endif

    </div>
</div>
@endsection