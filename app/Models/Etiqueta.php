<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';
    protected $primaryKey = 'id_etiqueta';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    public function consejos()
    {
        return $this->belongsToMany(
            Consejo::class,
            'consejo_etiqueta',
            'etiqueta_id',
            'consejo_id'
        );
    }
}