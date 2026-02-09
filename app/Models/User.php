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

    // campos se pueden llenar 
    protected $fillable = [
        'nombre',
        'correo',
        'password_hash', // Ojo aquí
        'telefono',
        'rol',
        // agrega aquí el resto de campos que quieras llenar desde formularios...
    ];

    // 4. Ocultar el hash de la contraseña y tokens
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // 5. IMPORTANTE: Decirle a Laravel cuál es la contraseña
    // Laravel busca por defecto la columna 'password', nosotros usaremos 'password_hash'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // 6. Casts (opcional, para tipos de datos)
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_hash' => 'hashed', // Para que lo encripte automáticamente
    ];

    
}