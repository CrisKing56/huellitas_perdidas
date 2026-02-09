<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtravioFoto extends Model
{
    use HasFactory;

    protected $table = 'extravio_fotos';
    protected $primaryKey = 'id_foto';
    public $timestamps = false; // Asumo que esta tabla no tiene created_at

    protected $fillable = ['publicacion_id', 'url', 'orden'];
}