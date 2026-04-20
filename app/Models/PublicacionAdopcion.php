<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionAdopcion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones_adopcion';
    protected $primaryKey = 'id_publicacion';

    protected $fillable = [
        'autor_usuario_id',
        'autor_organizacion_id',
        'nombre',
        'especie_id',
        'raza_id',
        'otra_raza',
        'edad_anios',
        'sexo',
        'tamano',
        'color_predominante',
        'descripcion',
        'vacunas_aplicadas',
        'esterilizado',
        'condicion_salud',
        'descripcion_salud',
        'requisitos',
        'colonia_barrio',
        'calle_referencias',
        'latitud',
        'longitud',
        'estado',
    ];

    protected $casts = [
        'esterilizado' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_usuario_id', 'id_usuario');
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'especie_id', 'id_especie');
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class, 'raza_id', 'id_raza');
    }

    public function fotos()
    {
        return $this->hasMany(AdopcionFoto::class, 'publicacion_id', 'id_publicacion')
            ->orderBy('orden', 'asc');
    }

    public function fotoPrincipal()
    {
        return $this->hasOne(AdopcionFoto::class, 'publicacion_id', 'id_publicacion')
            ->orderBy('orden', 'asc');
    }

    public function solicitudesAdopcion()
    {
        return $this->hasMany(SolicitudAdopcion::class, 'publicacion_id', 'id_publicacion')
            ->orderBy('created_at', 'desc');
    }
}