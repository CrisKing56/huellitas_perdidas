<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    // 1. Apuntar a tu tabla real
    protected $table = 'ubicaciones'; 
    
    // 2. Especificar tu llave primaria
    protected $primaryKey = 'id_ubicacion'; 
    
    // 3. Apagar los timestamps de Laravel (MySQL hará el 'creado_en')
    public $timestamps = false; 

    // 4. Solo estos campos se pueden llenar desde el formulario
    protected $fillable = [
        'latitud', 
        'longitud', 
        'precision_metros'
    ];
}