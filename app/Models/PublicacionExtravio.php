<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionExtravio extends Model
{
    use HasFactory;

    protected $table = 'publicaciones_extravio';
    protected $primaryKey = 'id_publicacion';

    public $timestamps = false;

    protected $fillable = [
        'autor_usuario_id',
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
        'ubicacion_id'
    ];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id', 'id_ubicacion');
    }

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

    public function avistamientos()
    {
        return $this->hasMany(AvistamientoExtravio::class, 'publicacion_id', 'id_publicacion')
            ->orderByDesc('creado_en');
    }
}