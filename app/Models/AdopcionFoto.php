<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdopcionFoto extends Model
{
    use HasFactory;

    
    protected $table = 'adopcion_fotos';
    protected $primaryKey = 'id_foto';
    public $timestamps = false; 
    
    protected $fillable = [
        'publicacion_id', 
        'url',
        'orden'
    ];

    public function publicacion()
    {
        return $this->belongsTo(PublicacionAdopcion::class, 'publicacion_id', 'id_publicacion');
    }
}