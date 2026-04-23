<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recupera tu contraseña</title>
</head>
<body style="margin:0; padding:0; background:#f9fafb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <div style="background:linear-gradient(135deg, #f97316, #ea580c); padding:28px 32px; text-align:center;">
                <img src="{{ asset('img/logo1.png') }}"
                     alt="Huellitas Perdidas"
                     style="height:64px; width:auto; margin-bottom:14px;">
                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">
                    Recuperación de contraseña
                </h1>
                <p style="margin:10px 0 0 0; color:#ffedd5; font-size:15px;">
                    Recibimos una solicitud para restablecer tu acceso.
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0; font-size:16px; color:#374151;">
                    Hola <strong>{{ $usuario->nombre }}</strong>,
                </p>

                <p style="margin:0 0 18px 0; font-size:16px; color:#374151; line-height:1.7;">
                    Da clic en el siguiente botón para crear una nueva contraseña para tu cuenta de
                    <strong>Huellitas Perdidas</strong>.
                </p>

                <div style="background:#fff7ed; border:1px solid #fdba74; border-radius:16px; padding:20px; margin-bottom:24px;">
                    <p style="margin:0; font-size:15px; color:#9a3412; line-height:1.7;">
                        Por seguridad, este enlace expirará en <strong>{{ $expireMinutes }} minutos</strong>.
                        Si tú no solicitaste este cambio, puedes ignorar este correo.
                    </p>
                </div>

                <div style="margin-bottom:24px; text-align:center;">
                    <a href="{{ $actionUrl }}"
                       style="display:inline-block; background:#f97316; color:#ffffff; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px;">
                        Restablecer contraseña
                    </a>
                </div>

                <p style="margin:0 0 12px 0; font-size:15px; color:#4b5563; line-height:1.7;">
                    Si el botón no funciona, copia y pega este enlace en tu navegador:
                </p>

                <p style="margin:0 0 18px 0; font-size:14px; color:#ea580c; word-break:break-all;">
                    {{ $actionUrl }}
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