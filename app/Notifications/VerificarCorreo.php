<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerificarCorreo extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu correo en Huellitas Perdidas')
            ->greeting('Hola ' . ($notifiable->nombre ?? 'usuario') . ',')
            ->line('Gracias por registrarte en Huellitas Perdidas.')
            ->line('Para activar tu cuenta y confirmar que tu correo es válido, verifica tu dirección de correo electrónico.')
            ->action('Verificar correo', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si tú no creaste esta cuenta, puedes ignorar este mensaje.');
    }
}