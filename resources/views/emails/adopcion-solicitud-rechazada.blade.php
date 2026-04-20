<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización de tu solicitud</title>
</head>
<body style="margin:0; padding:0; background:#f9fafb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <div style="background:linear-gradient(135deg, #ef4444, #dc2626); padding:28px 32px;">
                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">
                    Actualización de tu solicitud
                </h1>
                <p style="margin:10px 0 0 0; color:#fee2e2; font-size:15px;">
                    Ya hay una respuesta para tu solicitud de adopción.
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0; font-size:16px; color:#374151;">
                    Hola <strong>{{ $solicitanteNombre }}</strong>,
                </p>

                <p style="margin:0 0 18px 0; font-size:16px; color:#374151; line-height:1.7;">
                    Lamentablemente, en esta ocasión tu solicitud para adoptar a
                    <strong>{{ $publicacion->nombre }}</strong> no fue seleccionada.
                </p>

                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:16px; padding:20px; margin-bottom:24px;">
                    <p style="margin:0 0 10px 0; font-size:15px; color:#991b1b;">
                        <strong>Mascota:</strong> {{ $publicacion->nombre }}
                    </p>

                    <p style="margin:0; font-size:15px; color:#991b1b; line-height:1.6;">
                        Puedes revisar tus solicitudes desde tu panel y continuar explorando otras mascotas en adopción.
                    </p>
                </div>

                <div style="margin-bottom:24px;">
                    <a href="{{ $urlSolicitudes }}"
                       style="display:inline-block; background:#dc2626; color:#ffffff; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px; margin-right:10px;">
                        Ver mis solicitudes
                    </a>

                    <a href="{{ $urlPublicacion }}"
                       style="display:inline-block; background:#ffffff; color:#991b1b; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px; border:1px solid #fecaca;">
                        Ver publicación
                    </a>
                </div>

                <p style="margin:0 0 12px 0; font-size:15px; color:#4b5563; line-height:1.7;">
                    Gracias por tu interés en adoptar de forma responsable.
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