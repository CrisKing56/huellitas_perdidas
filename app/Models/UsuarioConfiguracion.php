<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioConfiguracion extends Model
{
    protected $table = 'usuario_configuracion';
    protected $primaryKey = 'id_config';
    public $timestamps = false;

    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'usuario_id',
        'recibir_notificaciones',
        'recibir_correos',
        'mostrar_telefono_publico',
        'mostrar_whatsapp_publico',
        'ocultar_ubicacion_exacta',
        'actualizado_en',
    ];

    protected $casts = [
        'recibir_notificaciones' => 'boolean',
        'recibir_correos' => 'boolean',
        'mostrar_telefono_publico' => 'boolean',
        'mostrar_whatsapp_publico' => 'boolean',
        'ocultar_ubicacion_exacta' => 'boolean',
    ];
}