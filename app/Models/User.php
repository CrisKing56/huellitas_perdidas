<?php

namespace App\Models;

use App\Notifications\ResetPasswordCustomNotification;
use App\Notifications\VerificarCorreo;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract, CanResetPasswordContract
{
    use HasFactory, Notifiable, MustVerifyEmail, CanResetPassword;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';

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

    /**
     * Laravel usa este valor para autenticar la contraseña.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Le indicamos a Laravel cómo se llama la columna de contraseña.
     */
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    /**
     * Le indicamos a Laravel que el campo identificador para login es "correo".
     */
    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    /**
     * Correo usado para verificación de email.
     */
    public function getEmailForVerification()
    {
        return $this->correo;
    }

    /**
     * Correo usado por las notificaciones por mail.
     */
    public function routeNotificationForMail($notification = null)
    {
        return $this->correo;
    }

    /**
     * Notificación personalizada de verificación de correo.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificarCorreo());
    }

    /**
     * Correo usado para el flujo de recuperación de contraseña.
     */
    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    /**
     * Notificación personalizada para restablecer contraseña.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustomNotification($token));
    }
}