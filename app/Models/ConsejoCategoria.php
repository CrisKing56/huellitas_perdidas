<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsejoCategoria extends Model
{
    protected $table = 'categorias_consejo';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function consejos()
    {
        return $this->hasMany(Consejo::class, 'categoria_id', 'id_categoria');
    }
}