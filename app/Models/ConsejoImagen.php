<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsejoImagen extends Model
{
    use HasFactory;

    protected $table = 'consejo_imagen';
    protected $primaryKey = 'id_imagen';

    public $timestamps = false;

    protected $fillable = [
        'consejo_id',
        'url',
        'orden',
    ];

    public function consejo()
    {
        return $this->belongsTo(Consejo::class, 'consejo_id', 'id_consejo');
    }
}