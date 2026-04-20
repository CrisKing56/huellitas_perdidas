@extends('layout.app')

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">

        <div class="mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 px-3 py-1 text-xs font-bold tracking-wide uppercase border border-blue-100">
                            Panel personal
                        </span>

                        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                            Mis solicitudes enviadas
                        </h1>

                        <p class="text-gray-500 mt-3 text-base md:text-lg leading-relaxed">
                            Aquí puedes consultar el estado de las solicitudes de adopción que has enviado.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('adopciones.index') }}"
                           class="inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 font-medium rounded-2xl hover:bg-gray-200 transition">
                            Volver al catálogo
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

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                {{ session('error') }}
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

                    $especieTexto = $mascota?->especie->nombre ?? match ((int) ($mascota->especie_id ?? 0)) {
                        1 => 'Perro',
                        2 => 'Gato',
                        default => 'Mascota',
                    };

                    $telefonoCrudo = $mascota?->autor->whatsapp ?? $mascota?->autor->telefono ?? null;
                    $telefonoLimpio = $telefonoCrudo ? preg_replace('/\D+/', '', $telefonoCrudo) : null;

                    if ($telefonoLimpio && strlen($telefonoLimpio) === 10) {
                        $telefonoLimpio = '52' . $telefonoLimpio;
                    }

                    $whatsappUrl = $telefonoLimpio ? 'https://wa.me/' . $telefonoLimpio . '?text=' . urlencode('Hola, mi solicitud para adoptar a ' . ($mascota->nombre ?? 'la mascota') . ' fue aceptada.') : null;
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
                                                Enviada {{ \Carbon\Carbon::parse($solicitud->created_at)->locale('es')->diffForHumans() }}
                                            </span>
                                        </div>

                                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">
                                            {{ $mascota->nombre ?? 'Mascota no disponible' }}
                                        </h2>

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                            <span>{{ $especieTexto }}</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span>{{ $solicitud->nombre_completo }}</span>
                                        </div>

                                        <p class="mt-4 text-sm text-gray-600 leading-relaxed max-w-3xl">
                                            {{ \Illuminate\Support\Str::limit($solicitud->motivo_adopcion, 180) }}
                                        </p>

                                        @if($solicitud->estado === 'ACEPTADA' && $mascota && $mascota->autor)
                                            <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 p-4">
                                                <p class="text-sm font-semibold text-green-800 mb-3">
                                                    Tu solicitud fue aceptada. Ya puedes comunicarte con el responsable.
                                                </p>

                                                <div class="space-y-2 text-sm text-gray-700">
                                                    <p><span class="font-semibold">Responsable:</span> {{ $mascota->autor->nombre ?? $mascota->autor->name ?? 'Usuario' }}</p>
                                                    <p><span class="font-semibold">Teléfono:</span> {{ $mascota->autor->telefono ?? $mascota->autor->whatsapp ?? 'No disponible' }}</p>
                                                    <p><span class="font-semibold">Correo:</span> {{ $mascota->autor->correo ?? $mascota->autor->email ?? 'No disponible' }}</p>
                                                </div>

                                                <div class="mt-4 flex flex-wrap gap-3">
                                                    @if($whatsappUrl)
                                                        <a href="{{ $whatsappUrl }}"
                                                           target="_blank"
                                                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-[#25D366] text-white font-semibold text-sm hover:bg-[#20bd5a] transition">
                                                            Escribir por WhatsApp
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('adopciones.show', $mascota->id_publicacion) }}"
                                                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white text-green-700 border border-green-200 font-semibold text-sm hover:bg-green-100 transition">
                                                        Ver publicación
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif($solicitud->estado === 'RECHAZADA')
                                            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                                                <p class="text-sm text-red-700">
                                                    Lamentablemente en esta ocasión no fuiste seleccionado para esta adopción.
                                                </p>
                                            </div>
                                        @elseif($solicitud->estado === 'ENVIADA')
                                            <div class="mt-5 rounded-2xl border border-yellow-200 bg-yellow-50 p-4">
                                                <p class="text-sm text-yellow-800">
                                                    Tu solicitud sigue en revisión. El contacto del responsable se habilitará solo si eres aceptado.
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($mascota)
                                        <div class="shrink-0">
                                            <a href="{{ route('adopciones.show', $mascota->id_publicacion) }}"
                                               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 font-semibold text-sm hover:bg-blue-100 transition">
                                                Ver publicación
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-14 bg-white rounded-3xl shadow-sm border border-dashed border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">No has enviado solicitudes todavía</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        Cuando envíes una solicitud de adopción, aparecerá aquí con su estado.
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