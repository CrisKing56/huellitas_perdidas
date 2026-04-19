<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvistamientoExtravio extends Model
{
    use HasFactory;

    protected $table = 'avistamientos_extravio';
    protected $primaryKey = 'id_avistamiento';
    public $timestamps = false;

    protected $fillable = [
        'publicacion_id',
        'usuario_reportante_id',
        'ubicacion_id',
        'nombre_contacto',
        'telefono_contacto',
        'fecha_avistamiento',
        'colonia_barrio',
        'calle_referencias',
        'descripcion',
        'foto_url',
        'estado',
        'creado_en',
        'visto_en',
    ];

    public function publicacion()
    {
        return $this->belongsTo(PublicacionExtravio::class, 'publicacion_id', 'id_publicacion');
    }

    public function reportante()
    {
        return $this->belongsTo(User::class, 'usuario_reportante_id', 'id_usuario');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id', 'id_ubicacion');
    }
}