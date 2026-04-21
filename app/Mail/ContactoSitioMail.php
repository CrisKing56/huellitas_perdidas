<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoSitioMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $datos;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function build()
    {
        return $this->subject('Nuevo mensaje de contacto: ' . $this->datos['asunto'])
            ->replyTo($this->datos['correo'], $this->datos['nombre'])
            ->view('emails.contacto')
            ->with([
                'datos' => $this->datos,
            ]);
    }
}