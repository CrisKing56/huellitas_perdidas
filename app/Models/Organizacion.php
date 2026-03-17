<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    protected $table = 'organizaciones';
    protected $primaryKey = 'id_organizacion';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'usuario_dueno_id',
        'nombre',
        'descripcion',
        'telefono',
        'whatsapp',
        'sitio_web',
        'direccion_id',
        'ubicacion_id',
        'estado_revision',
        'revisado_por',
        'revisado_en',
        'motivo_rechazo',
        'puede_reintentar_desde',
        'activo',
        'creado_en',
        'actualizado_en',
    ];

    public function usuarioDueno()
    {
        return $this->belongsTo(User::class, 'usuario_dueno_id', 'id_usuario');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id', 'id_direccion');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id', 'id_ubicacion');
    }

    public function veterinariaDetalle()
    {
        return $this->hasOne(VeterinariaDetalle::class, 'organizacion_id', 'id_organizacion');
    }

    public function refugioDetalle()
    {
        return $this->hasOne(RefugioDetalle::class, 'organizacion_id', 'id_organizacion');
    }

    public function fotos()
    {
        return $this->hasMany(OrganizacionFoto::class, 'organizacion_id', 'id_organizacion');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioAtencion::class, 'organizacion_id', 'id_organizacion');
    }

    public function publicacionesAdopcion()
    {
        return $this->hasMany(PublicacionAdopcion::class, 'autor_organizacion_id', 'id_organizacion');
    }

    public function publicacionesExtravio()
    {
        return $this->hasMany(PublicacionExtravio::class, 'autor_organizacion_id', 'id_organizacion');
    }

    public function consejos()
    {
        return $this->hasMany(Consejo::class, 'autor_organizacion_id', 'id_organizacion');
    }
}