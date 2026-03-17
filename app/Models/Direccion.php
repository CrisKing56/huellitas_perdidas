<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    
    protected $primaryKey = 'id_direccion';
    
    public $timestamps = false; 

    protected $fillable = [
        'calle_numero',
        'colonia',
        'codigo_postal',
        'ciudad',
        'estado'
    ];
}