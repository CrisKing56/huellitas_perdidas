<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionAdopcion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones_adopcion'; 
    protected $primaryKey = 'id_publicacion'; 

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';


    protected $fillable = [
        'autor_usuario_id',
        // 'autor_organizacion_id', 
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
        'estado'                
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_usuario_id', 'id_usuario'); 
    }

    public function fotos()
    {
        return $this->hasMany(AdopcionFoto::class, 'publicacion_id');
    }
    
    public function fotoPrincipal()
    {
        return $this->hasOne(AdopcionFoto::class, 'publicacion_id')->oldest('orden');
    }
}