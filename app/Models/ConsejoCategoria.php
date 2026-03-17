<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsejoCategoria extends Model
{
    protected $table = 'categorias_consejo'; 
    protected $primaryKey = 'id_categoria'; 
    public $timestamps = false;
}
