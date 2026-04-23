<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Adopciones - Huellitas Perdidas</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #334155; margin: 0; }
        .header { width: 100%; margin-bottom: 30px; border-bottom: 3px solid #ea580c; padding-bottom: 15px; }
        .header table { width: 100%; border: none; }
        .title { color: #ea580c; font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 14px; color: #64748b; margin: 5px 0 0 0; }
        .info-meta { text-align: right; font-size: 10px; color: #94a3b8; }
        table { width: 100%; border-collapse: collapse; background-color: #ffffff; }
        th { background-color: #ea580c; color: #ffffff; padding: 10px 8px; text-align: left; font-size: 10px; text-transform: uppercase; border: 1px solid #ea580c; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        tr:nth-child(even) { background-color: #fff7ed; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 9px; font-weight: bold; text-align: center; display: inline-block; }
        
        /* Colores específicos para estados de Adopción */
        .badge-disponible { background-color: #dcfce7; color: #166534; } /* Verde */
        .badge-proceso { background-color: #fef3c7; color: #92400e; }    /* Amarillo */
        .badge-pausada { background-color: #f1f5f9; color: #475569; }    /* Gris */
        .badge-adoptada { background-color: #dbeafe; color: #1e3a8a; }   /* Azul */
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #94a3b8; padding: 10px 0; border-top: 1px solid #e2e8f0; }
        .page-number:before { content: "Página " counter(page); }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="title">Huellitas Perdidas</h1>
                    <p class="subtitle">Reporte Administrativo de Adopciones</p>
                </td>
                <td class="info-meta">
                    <strong>Ubicación:</strong> Ocosingo, Chiapas<br>
                    <strong>Generado el:</strong> {{ \Carbon\Carbon::now()->setTimezone('America/Mexico_City')->format('d/m/Y h:i A') }}<br>
                    <strong>Usuario:</strong> {{ auth()->user()->nombre ?? 'Administrador' }}
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Nombre Mascota</th>
                <th>Especie / Edad</th>
                <th>Organización / Dueño</th>
                <th>Fecha Publicación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($adopciones as $adopcion)
                <tr>
                    <td><strong>#{{ $adopcion->id_publicacion }}</strong></td>
                    <td>{{ $adopcion->nombre }}</td>
                    <td>
                        {{ ucfirst(strtolower($adopcion->especie)) ?? 'N/A' }}<br>
                        <small style="color: #64748b;">Edad: {{ $adopcion->edad ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $adopcion->autor->nombre ?? 'Usuario' }}</td>
                    <td>{{ \Carbon\Carbon::parse($adopcion->created_at)->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $claseEstado = match($adopcion->estado) {
                                'DISPONIBLE' => 'badge-disponible',
                                'EN PROCESO', 'EN_PROCESO' => 'badge-proceso',
                                'PAUSADA' => 'badge-pausada',
                                'ADOPTADA' => 'badge-adoptada',
                                default => 'badge-disponible'
                            };
                        @endphp
                        <span class="badge {{ $claseEstado }}">
                            {{ str_replace('_', ' ', $adopcion->estado) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                        No se encontraron registros de adopciones con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; text-align: left;">Sistema de Gestión - Huellitas Perdidas Ocosingo</td>
                <td style="border: none; text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </div>

</body>
</html>