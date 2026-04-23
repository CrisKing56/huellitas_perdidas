<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mascotas Extraviadas - Huellitas Perdidas</title>
    <style>
        @page { margin: 1cm; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #334155;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 3px solid #ea580c;
            padding-bottom: 15px;
        }

        .header table {
            width: 100%;
            border: none;
        }

        .title {
            color: #ea580c;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 5px 0 0 0;
        }

        .info-meta {
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }

        th {
            background-color: #ea580c;
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #ea580c;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #fff7ed;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
        }

        .badge-activa { background-color: #fef3c7; color: #92400e; }
        .badge-resuelta { background-color: #dcfce7; color: #166534; }
        .badge-eliminada { background-color: #fee2e2; color: #991b1b; }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        .small {
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="title">Huellitas Perdidas</h1>
                    <p class="subtitle">Reporte Administrativo de Mascotas Extraviadas</p>
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
                <th style="width: 55px;">ID</th>
                <th>Nombre Mascota</th>
                <th>Datos</th>
                <th>Zona / Barrio</th>
                <th>Fecha Extravío</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mascotas as $mascota)
                @php
                    $claseEstado = match($mascota->estado) {
                        'ACTIVA' => 'badge-activa',
                        'RESUELTA', 'ENCONTRADA' => 'badge-resuelta',
                        'ELIMINADA', 'OCULTA' => 'badge-eliminada',
                        default => 'badge-activa'
                    };

                    $zona = $mascota->colonia_barrio ?: ($mascota->calle_referencias ?: 'No especificada');
                    $fechaExtravio = $mascota->fecha_extravio
                        ? \Carbon\Carbon::parse($mascota->fecha_extravio)->format('d/m/Y')
                        : 'No especificada';
                @endphp

                <tr>
                    <td><strong>#{{ $mascota->id_publicacion }}</strong></td>
                    <td>
                        <strong>{{ $mascota->nombre ?? 'Sin nombre' }}</strong><br>
                        <span class="small">
                            {{ \Illuminate\Support\Str::limit($mascota->descripcion ?? 'Sin descripción', 70) }}
                        </span>
                    </td>
                    <td>
                        <strong>Sexo:</strong> {{ $mascota->sexo ?? 'No especificado' }}<br>
                        <strong>Tamaño:</strong> {{ $mascota->tamano ?? 'No especificado' }}<br>
                        <strong>Color:</strong> {{ $mascota->color ?? 'No especificado' }}
                    </td>
                    <td>{{ $zona }}</td>
                    <td>{{ $fechaExtravio }}</td>
                    <td>
                        <span class="badge {{ $claseEstado }}">
                            {{ $mascota->estado ?? 'ACTIVA' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                        No se encontraron registros que coincidan con los filtros aplicados.
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