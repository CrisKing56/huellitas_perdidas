<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raza extends Model
{
    protected $table = 'razas';
    protected $primaryKey = 'id_raza';
    public $timestamps = false;

    protected $fillable = [
        'especie_id',
        'nombre',
        'activo',
    ];

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'especie_id', 'id_especie');
    }

    public function publicacionesAdopcion()
    {
        return $this->hasMany(PublicacionAdopcion::class, 'raza_id', 'id_raza');
    }
}