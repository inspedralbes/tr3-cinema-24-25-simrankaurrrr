<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class HelloMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $apellido;
    private $pdfPath;

    // Constructor
    public function __construct($nombre, $apellido = null)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;

        // Generar el PDF
        $this->pdfPath = $this->generatePDF();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Pago',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.hella',
            with: [
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('confirmacion.pdf')
                ->withMime('application/pdf')
        ];
    }

    private function generatePDF()
    {
        $data = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
        ];

        $pdf = Pdf::loadView('pdf.confirmacion', $data);

        // Guardar el PDF temporalmente en storage
        $pdfPath = storage_path('app/public/confirmacion.pdf');
        $pdf->save($pdfPath);

        return $pdfPath;
    }
}
