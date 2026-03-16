<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefugioDetalle extends Model
{
    protected $table = 'refugio_detalle';
    protected $primaryKey = 'organizacion_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'organizacion_id',
        'tipo_organizacion',
        'anio_fundacion',
        'capacidad_total',
        'animales_actuales',
        'animales_dados_adopcion',
        'anios_operacion',
        'nombre_responsable',
        'cargo_responsable',
        'num_voluntarios',
        'otras_especies',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id', 'id_organizacion');
    }
}