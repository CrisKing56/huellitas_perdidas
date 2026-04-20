<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consejo extends Model
{
    use HasFactory;

    protected $table = 'consejos';
    protected $primaryKey = 'id_consejo';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'autor_organizacion_id',
        'titulo',
        'resumen',
        'categoria_id',
        'especie_id',
        'contenido',
        'estado_publicacion',
        'revisado_por',
        'revisado_en',
        'motivo_rechazo',
    ];

    protected $casts = [
        'revisado_en' => 'datetime',
    ];

    public function imagenes()
    {
        return $this->hasMany(ConsejoImagen::class, 'consejo_id', 'id_consejo')
            ->orderBy('orden', 'asc');
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'autor_organizacion_id', 'id_organizacion');
    }

    public function categoria()
    {
        return $this->belongsTo(ConsejoCategoria::class, 'categoria_id', 'id_categoria');
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'especie_id', 'id_especie');
    }

    public function etiquetas()
    {
        return $this->belongsToMany(
            Etiqueta::class,
            'consejo_etiqueta',
            'consejo_id',
            'etiqueta_id'
        );
    }

    public function reportes()
    {
        return $this->hasMany(ReporteConsejo::class, 'consejo_id', 'id_consejo');
    }
}