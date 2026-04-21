<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f9fafb; padding:30px; color:#111827;">
    <div style="max-width:700px; margin:0 auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden;">
        <div style="background:#f97316; color:#ffffff; padding:24px 30px;">
            <h1 style="margin:0; font-size:24px;">Nuevo mensaje desde Huellitas Perdidas</h1>
        </div>

        <div style="padding:30px;">
            <p style="margin-top:0;"><strong>Nombre:</strong> {{ $datos['nombre'] }}</p>
            <p><strong>Correo:</strong> {{ $datos['correo'] }}</p>
            <p><strong>Asunto:</strong> {{ $datos['asunto'] }}</p>

            <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

            <p style="margin-bottom:10px;"><strong>Mensaje:</strong></p>
            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:18px; white-space:pre-line;">
                {{ $datos['mensaje'] }}
            </div>
        </div>
    </div>
</body>
</html>