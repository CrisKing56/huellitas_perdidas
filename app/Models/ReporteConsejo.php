<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteConsejo extends Model
{
    protected $table = 'reportes_consejo';
    protected $primaryKey = 'id_reporte';
    public $timestamps = false;

    const CREATED_AT = 'creado_en';

    protected $fillable = [
        'consejo_id',
        'usuario_reporta_id',
        'motivo',
        'descripcion',
        'estado',
        'revisado_por',
        'revisado_en',
        'accion_tomada',
        'motivo_resolucion',
        'creado_en',
    ];

    protected $casts = [
        'revisado_en' => 'datetime',
        'creado_en' => 'datetime',
    ];

    public function consejo()
    {
        return $this->belongsTo(Consejo::class, 'consejo_id', 'id_consejo');
    }

    public function usuarioReporta()
    {
        return $this->belongsTo(User::class, 'usuario_reporta_id', 'id_usuario');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por', 'id_usuario');
    }
}