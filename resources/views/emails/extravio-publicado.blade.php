<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte publicado</title>
</head>
<body style="margin:0; padding:0; background:#f9fafb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <div style="background:linear-gradient(135deg, #fb923c, #f97316); padding:28px 32px;">
                <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">
                    Tu reporte fue publicado
                </h1>
                <p style="margin:10px 0 0 0; color:#ffedd5; font-size:15px;">
                    Ya quedó registrado en Huellitas Perdidas.
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0; font-size:16px; color:#374151;">
                    Hola <strong>{{ $usuarioNombre }}</strong>,
                </p>

                <p style="margin:0 0 20px 0; font-size:16px; color:#374151; line-height:1.7;">
                    Tu publicación de mascota extraviada ya fue creada correctamente.  
                    Aquí tienes un resumen de la información registrada:
                </p>

                <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:16px; padding:20px; margin-bottom:24px;">
                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Nombre:</strong> {{ $publicacion->nombre }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Especie:</strong> {{ $especieNombre ?: 'Mascota' }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Zona:</strong> {{ $publicacion->colonia_barrio }}
                    </p>

                    <p style="margin:0 0 10px 0; font-size:15px; color:#9a3412;">
                        <strong>Fecha de extravío:</strong> {{ \Carbon\Carbon::parse($publicacion->fecha_extravio)->format('d/m/Y') }}
                    </p>

                    <p style="margin:0; font-size:15px; color:#9a3412;">
                        <strong>Estado:</strong> {{ $publicacion->estado }}
                    </p>
                </div>

                <div style="margin-bottom:24px;">
                    <a href="{{ $urlPublicacion }}"
                       style="display:inline-block; background:#f97316; color:#ffffff; text-decoration:none; font-weight:bold; padding:14px 22px; border-radius:12px;">
                        Ver publicación
                    </a>
                </div>

                <p style="margin:0 0 12px 0; font-size:15px; color:#4b5563; line-height:1.7;">
                    Te recomendamos compartir el enlace y mantener la información actualizada.
                    Si encuentras a tu mascota, podrás marcar el reporte como resuelto desde tu panel.
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