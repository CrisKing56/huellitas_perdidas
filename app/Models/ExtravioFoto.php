<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtravioFoto extends Model
{
    use HasFactory;

    protected $table = 'extravio_fotos';
    protected $primaryKey = 'id_foto';
    public $timestamps = false; 

    protected $fillable = ['publicacion_id', 'url', 'orden'];
}