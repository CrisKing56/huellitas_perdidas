<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizacionFoto extends Model
{
    // 1. Apuntamos a la tabla correcta en tu base de datos
    protected $table = 'organizacion_fotos';
    
    // 2. Apagamos los timestamps en inglés de Laravel
    public $timestamps = false; 

    // 3. Declaramos los campos que se van a usar
    protected $fillable = [
        'organizacion_id',
        'url',
        'orden',
        'creado_en'
    ];
}