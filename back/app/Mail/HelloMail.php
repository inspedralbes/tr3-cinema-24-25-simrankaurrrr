<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HelloMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;  // Definir la variable pública

    /**
     * Crear una nueva instancia de mensaje.
     */
    public function __construct($message)
    {
        $this->message = $message;  // Asignar el valor de $message
    }

    /**
     * Obtener el sobre del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mail de Prueba',  // El asunto del correo
        );
    }

    /**
     * Obtener la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        // Asegúrate de que la vista existe, y pasa la variable a la vista
        return new Content(
            view: 'mail.hella',  // Cambié 'hella' por 'hello', asegurándote que la vista existe
            with: [
                'message' => $this->message,  // Pasamos la variable $message a la vista
            ]
        );
    }

    /**
     * Obtener los archivos adjuntos del mensaje.
     */
    public function attachments(): array
    {
        return [];
    }
}
