<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionExtravio extends Model
{
    use HasFactory;

    // 1. Apuntamos a tu tabla exacta
    protected $table = 'publicaciones_extravio';
    protected $primaryKey = 'id_publicacion';

    public $timestamps = false;

    // 2. Permitimos que estos campos se guarden
    protected $fillable = [
        'autor_usuario_id', // Importante
        'nombre', 
        'especie_id', 
        'raza_id', 
        'otra_raza',
        'color', 
        'tamano', 
        'sexo', 
        'fecha_extravio', 
        'colonia_barrio',
        'calle_referencias', 
        'descripcion', 
        'estado',
        'foto',
    ];

    

    // 3. Relación: Una publicación tiene muchas fotos
    public function fotos()
    {
        return $this->hasMany(ExtravioFoto::class, 'publicacion_id', 'id_publicacion');
    }

    public function fotoPrincipal()
    {
        return $this->hasOne(ExtravioFoto::class, 'publicacion_id', 'id_publicacion')->orderBy('orden', 'asc');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_usuario_id');
    }
}