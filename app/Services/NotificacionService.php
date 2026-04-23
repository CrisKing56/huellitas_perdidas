<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\SolicitudAdopcion;

class NotificacionService
{
    public function crear(
        int $usuarioId,
        string $tipo,
        string $titulo,
        string $mensaje
    ): void {
        if ($usuarioId <= 0) {
            return;
        }

        Notificacion::create([
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'titulo' => trim($titulo),
            'mensaje' => trim($mensaje),
            'leido' => 0,
            'creado_en' => now(),
        ]);
    }

    public function solicitudRecibida(SolicitudAdopcion $solicitud): void
    {
        $solicitud->loadMissing(['publicacion.autor', 'solicitante']);

        $publicacion = $solicitud->publicacion;
        if (!$publicacion) {
            return;
        }

        $duenoId = (int) ($publicacion->autor_usuario_id ?? 0);
        if ($duenoId <= 0) {
            return;
        }

        $nombreMascota = $publicacion->nombre ?? 'la mascota';
        $solicitanteNombre =
            $solicitud->solicitante->nombre
            ?? $solicitud->nombre_completo
            ?? 'Un usuario';

        $this->crear(
            $duenoId,
            'ADOPCION',
            'Nueva solicitud de adopción',
            $solicitanteNombre . ' quiere adoptar a ' . $nombreMascota . '.'
        );
    }

    public function solicitudAceptada(SolicitudAdopcion $solicitud): void
    {
        $solicitud->loadMissing(['publicacion', 'solicitante']);

        $solicitanteId = (int) ($solicitud->solicitante_usuario_id ?? 0);
        if ($solicitanteId <= 0) {
            return;
        }

        $nombreMascota = $solicitud->publicacion->nombre ?? 'la mascota';

        $this->crear(
            $solicitanteId,
            'APROBACION',
            'Solicitud aceptada',
            'Tu solicitud para adoptar a ' . $nombreMascota . ' fue aceptada.'
        );
    }

    public function solicitudRechazada(SolicitudAdopcion $solicitud): void
    {
        $solicitud->loadMissing(['publicacion', 'solicitante']);

        $solicitanteId = (int) ($solicitud->solicitante_usuario_id ?? 0);
        if ($solicitanteId <= 0) {
            return;
        }

        $nombreMascota = $solicitud->publicacion->nombre ?? 'la mascota';

        $this->crear(
            $solicitanteId,
            'APROBACION',
            'Solicitud rechazada',
            'Tu solicitud para adoptar a ' . $nombreMascota . ' fue rechazada.'
        );
    }
}