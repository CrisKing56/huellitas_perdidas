<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consejo extends Model
{
    use HasFactory;

    // 1. Especificamos el nombre exacto de tu tabla
    protected $table = 'consejos';
    
    // 2. Especificamos tu llave primaria personalizada
    protected $primaryKey = 'id_consejo';

    // 3. Le enseñamos a Laravel que tu base de datos habla español
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // 4. Los campos que permitimos guardar desde el formulario
    protected $fillable = [
        'autor_organizacion_id',
        'titulo',
        'resumen',
        'categoria_id',
        'especie_id',
        'contenido',
        'estado_publicacion'
    ];

    public function imagenes()
    {
        // Relación: Un consejo tiene muchas imágenes
        return $this->hasMany(ConsejoImagen::class, 'consejo_id', 'id_consejo')->orderBy('orden');
    }

    // NUEVA: Relación para saber qué Veterinaria/Refugio lo escribió
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'autor_organizacion_id', 'id_organizacion');
    }

    // NUEVA: Relación para traer el nombre de la categoría (Salud, Higiene, etc.)
    public function categoria()
    {
        // NOTA: Si no tienes el modelo CategoriaConsejo, créalo rápido con: php artisan make:model CategoriaConsejo
        return $this->belongsTo(ConsejoCategoria::class, 'categoria_id', 'id_categoria');
    }
}