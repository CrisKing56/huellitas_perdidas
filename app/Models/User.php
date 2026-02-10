<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'password_hash',
        'telefono',
        'rol',
        'estado',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Laravel usará password_hash como contraseña
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
