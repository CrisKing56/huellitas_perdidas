<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios - Huellitas Perdidas</title>
    <style>
        @page { margin: 1cm; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
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

        .badge-activa { background-color: #dcfce7; color: #166534; }
        .badge-suspendida { background-color: #fef3c7; color: #92400e; }
        .badge-eliminada { background-color: #fee2e2; color: #991b1b; }

        .badge-admin { background-color: #dbeafe; color: #1e3a8a; }
        .badge-usuario { background-color: #f3f4f6; color: #374151; }
        .badge-veterinaria { background-color: #dcfce7; color: #166534; }
        .badge-refugio { background-color: #ede9fe; color: #6d28d9; }

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
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="title">Huellitas Perdidas</h1>
                    <p class="subtitle">Reporte Administrativo de Usuarios</p>
                </td>
                <td class="info-meta">
                    <strong>Ubicación:</strong> Ocosingo, Chiapas<br>
                    <strong>Generado el:</strong> {{ \Carbon\Carbon::now()->setTimezone('America/Mexico_City')->format('d/m/Y h:i A') }}
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Nombre del Usuario</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Rol de Sistema</th>
                <th>Estado de Cuenta</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $user)
                @php
                    $rolClass = match($user->rol) {
                        'ADMIN' => 'badge-admin',
                        'VETERINARIA' => 'badge-veterinaria',
                        'REFUGIO' => 'badge-refugio',
                        default => 'badge-usuario',
                    };

                    $estadoClass = match($user->estado) {
                        'ACTIVA' => 'badge-activa',
                        'SUSPENDIDA' => 'badge-suspendida',
                        default => 'badge-eliminada',
                    };
                @endphp

                <tr>
                    <td><strong>#{{ $user->id_usuario }}</strong></td>
                    <td>{{ $user->nombre ?? 'Sin nombre' }}</td>
                    <td>{{ $user->correo ?? 'Sin correo' }}</td>
                    <td>{{ $user->telefono ?: 'Sin teléfono' }}</td>
                    <td>
                        <span class="badge {{ $rolClass }}">
                            {{ $user->rol ?? 'USUARIO' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $estadoClass }}">
                            {{ $user->estado ?? 'ACTIVA' }}
                        </span>
                    </td>
                    <td>
                        {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') : 'Sin fecha' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                        No se encontraron usuarios con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; text-align: left;">Sistema de Gestión - Huellitas Perdidas</td>
                <td style="border: none; text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </div>
</body>
</html>