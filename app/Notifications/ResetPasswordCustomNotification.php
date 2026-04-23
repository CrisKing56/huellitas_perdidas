<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordCustomNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $actionUrl = route('password.reset', [
            'token' => $this->token,
            'correo' => $notifiable->correo,
        ]);

        return (new MailMessage)
            ->subject('Recupera tu contraseña - Huellitas Perdidas')
            ->view('emails.password-reset', [
                'usuario' => $notifiable,
                'actionUrl' => $actionUrl,
                'expireMinutes' => config('auth.passwords.users.expire', 60),
            ]);
    }
}