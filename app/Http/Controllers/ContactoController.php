<?php

namespace App\Http\Controllers;

use App\Mail\ContactoSitioMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactoController extends Controller
{
    public function index()
    {
        return view('contactanos', [
            'contactEmail'   => config('services.contact.email'),
            'contactWhatsapp'=> config('services.contact.whatsapp'),
            'contactAddress' => config('services.contact.address'),
            'contactHours'   => config('services.contact.hours'),
        ]);
    }

    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:120'],
            'correo'   => ['required', 'email', 'max:120'],
            'asunto'   => ['required', 'string', 'max:150'],
            'mensaje'  => ['required', 'string', 'min:10', 'max:2000'],
            'website'  => ['nullable', 'max:0'], // honeypot anti-spam
        ], [
            'nombre.required'  => 'Por favor escribe tu nombre.',
            'correo.required'  => 'Por favor escribe tu correo.',
            'correo.email'     => 'Escribe un correo válido.',
            'asunto.required'  => 'Por favor escribe un asunto.',
            'mensaje.required' => 'Por favor escribe tu mensaje.',
            'mensaje.min'      => 'El mensaje debe tener al menos 10 caracteres.',
        ]);

        try {
            Mail::to(config('services.contact.email'))
                ->send(new ContactoSitioMail($validated));

            return back()->with('success', 'Tu mensaje fue enviado correctamente. Te responderemos lo antes posible.');
        } catch (\Throwable $e) {
            Log::error('Error al enviar formulario de contacto', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo enviar el mensaje en este momento. Intenta nuevamente más tarde.');
        }
    }
}