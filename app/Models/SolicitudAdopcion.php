<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudAdopcion extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_adopcion';
    protected $primaryKey = 'id_solicitud';

    protected $fillable = [
        'publicacion_id',
        'solicitante_usuario_id',
        'nombre_completo',
        'edad',
        'estado_civil',
        'tipo_vivienda',
        'tiene_patio',
        'todos_de_acuerdo',
        'motivo_adopcion',
        'estado',
    ];

    protected $casts = [
        'tiene_patio' => 'boolean',
        'todos_de_acuerdo' => 'boolean',
    ];

    public function publicacion()
    {
        return $this->belongsTo(PublicacionAdopcion::class, 'publicacion_id', 'id_publicacion');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_usuario_id', 'id_usuario');
    }
}