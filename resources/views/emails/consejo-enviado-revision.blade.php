<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consejo enviado a revisión</title>
</head>
<body style="margin:0; padding:0; background:#f9fafb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <div style="background:linear-gradient(135deg, #fb923c, #f97316); padding:28px 32px;">
                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">
                    Consejo enviado a revisión
                </h1>
                <p style="margin:10px 0 0 0; color:#ffedd5; font-size:15px;">
                    Tu publicación quedó registrada correctamente en Huellitas Perdidas.
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0; font-size:16px; color:#374151;">
                    Hola,
                </p>

                <p style="margin:0 0 18px 0; font-size:16px; color:#374151; line-height:1.7;">
                    Tu consejo <strong>{{ $consejo->titulo }}</strong>
                    {{ $tipoAccion === 'actualizado' ? 'fue actualizado y enviado nuevamente a revisión' : 'fue enviado a revisión' }}.
                </p>

                <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:16px; padding:20px; margin-bottom:24px;">
                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Categoría:</strong> {{ $consejo->categoria?->nombre ?? 'General' }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Especie:</strong> {{ $consejo->especie?->nombre ?? 'No especificada' }}
                    </p>

                    <p style="margin:0; font-size:15px; color:#9a3412; line-height:1.6;">
                        <strong>Estado actual:</strong> {{ $consejo->estado_publicacion }}
                    </p>
                </div>

                <div style="margin-bottom:24px;">
                    <a href="{{ $urlMisConsejos }}"
                       style="display:inline-block; background:#f97316; color:#ffffff; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px;">
                        Ver mis consejos
                    </a>
                </div>

                <p style="margin:0 0 12px 0; font-size:15px; color:#4b5563; line-height:1.7;">
                    Recibirás una actualización cuando el administrador revise la publicación.
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