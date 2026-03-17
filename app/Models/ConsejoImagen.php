<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsejoImagen extends Model
{
    use HasFactory;

    protected $table = 'consejo_imagen';
    protected $primaryKey = 'id_foto';
    
    public $timestamps = false; 

    protected $fillable = [
        'consejo_id',
        'url',
        'orden'
    ];
}