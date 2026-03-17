<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeterinariaDetalle extends Model
{
    protected $table = 'veterinaria_detalle';
    protected $primaryKey = 'organizacion_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'organizacion_id',
        'medico_responsable',
        'cedula_profesional',
        'num_veterinarios',
        'otros_servicios',
    ];

   

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id', 'id_organizacion');
    }
}