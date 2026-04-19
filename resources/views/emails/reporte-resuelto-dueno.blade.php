<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización del reporte sobre tu publicación</title>
</head>
<body style="margin:0; padding:0; background:#f9fafb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <div style="background:linear-gradient(135deg, #fb923c, #f97316); padding:28px 32px;">
                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">
                    Revisión del reporte finalizada
                </h1>
                <p style="margin:10px 0 0 0; color:#ffedd5; font-size:15px;">
                    Ya se actualizó el estado del reporte relacionado con tu publicación.
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0; font-size:16px; color:#374151;">
                    Hola <strong>{{ $reporte->dueno_nombre }}</strong>,
                </p>

                <p style="margin:0 0 18px 0; font-size:16px; color:#374151; line-height:1.7;">
                    La revisión administrativa del reporte sobre tu publicación de
                    <strong>{{ $reporte->mascota_nombre ?? 'tu mascota' }}</strong> ha concluido.
                </p>

                <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:16px; padding:20px; margin-bottom:24px;">
                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Resultado:</strong> {{ $estadoBonito }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Mascota:</strong> {{ $reporte->mascota_nombre ?? 'No disponible' }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Zona:</strong> {{ $reporte->colonia_barrio ?? 'No disponible' }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Motivo original:</strong> {{ $reporte->motivo_nombre ?? 'No disponible' }}
                    </p>

                    <p style="margin:0; font-size:15px; color:#9a3412; line-height:1.6;">
                        <strong>Nota administrativa:</strong><br>
                        {{ $reporte->nota_resolucion ?: 'No se agregó una nota adicional.' }}
                    </p>
                </div>

                <p style="margin:0 0 18px 0; font-size:15px; color:#4b5563; line-height:1.7;">
                    Estado final del reporte: <strong>{{ $estadoBonito }}</strong>.
                    Este correo solo te informa sobre el cierre del proceso administrativo del reporte.
                </p>

                <p style="margin:24px 0 0 0; font-size:15px; color:#4b5563;">
                    Saludos,<br>
                    <strong>Equipo de Huellitas Perdidas</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>