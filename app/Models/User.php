<?php

namespace App\Models;

use App\Notifications\VerificarCorreo;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, Notifiable, MustVerifyEmail;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'correo',
        'password_hash',
        'telefono',
        'rol',
        'estado',
        'google_id',
        'facebook_id',
        'auth_provider',
        'google_avatar',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Le decimos a Laravel CÓMO se llama la columna de contraseña
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function getEmailForVerification()
    {
        return $this->correo;
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->correo;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificarCorreo());
    }
}