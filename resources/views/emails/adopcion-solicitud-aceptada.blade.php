<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de adopción aceptada</title>
</head>
<body style="margin:0; padding:0; background:#f9fafb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <div style="background:linear-gradient(135deg, #22c55e, #15803d); padding:28px 32px;">
                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">
                    ¡Tu solicitud fue aceptada!
                </h1>
                <p style="margin:10px 0 0 0; color:#dcfce7; font-size:15px;">
                    Ya puedes ponerte en contacto con el responsable de la publicación.
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0; font-size:16px; color:#374151;">
                    Hola <strong>{{ $solicitanteNombre }}</strong>,
                </p>

                <p style="margin:0 0 18px 0; font-size:16px; color:#374151; line-height:1.7;">
                    Tu solicitud para adoptar a <strong>{{ $publicacion->nombre }}</strong> fue aceptada por
                    <strong>{{ $duenoNombre }}</strong>.
                </p>

                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:16px; padding:20px; margin-bottom:24px;">
                    <p style="margin:0 0 10px 0; font-size:15px; color:#166534;">
                        <strong>Mascota:</strong> {{ $publicacion->nombre }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#166534;">
                        <strong>Estado:</strong> {{ str_replace('_', ' ', $publicacion->estado) }}
                    </p>

                    <p style="margin:0; font-size:15px; color:#166534; line-height:1.6;">
                        Ingresa a tu panel para ver los datos de contacto disponibles y continuar el proceso.
                    </p>
                </div>

                <div style="margin-bottom:24px;">
                    <a href="{{ $urlSolicitudes }}"
                       style="display:inline-block; background:#16a34a; color:#ffffff; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px; margin-right:10px;">
                        Ver mis solicitudes
                    </a>

                    <a href="{{ $urlPublicacion }}"
                       style="display:inline-block; background:#ffffff; color:#166534; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px; border:1px solid #bbf7d0;">
                        Ver publicación
                    </a>
                </div>

                <p style="margin:0 0 12px 0; font-size:15px; color:#4b5563; line-height:1.7;">
                    Recuerda mantener una comunicación respetuosa y confirmar todos los detalles antes de concretar la adopción.
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