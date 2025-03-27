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

    public function __construct($nombre, $apellido, $pdfPath)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->pdfPath = $pdfPath;
    }
    
    public function attachments(): array
    {
        $path = storage_path('app/public/' . $this->pdfPath);
        
        if (!file_exists($path)) {
            Log::error("El archivo PDF no existe en: {$path}");
            return [];
        }
    
        return [
            Attachment::fromPath($path)
                ->as('entradas_cine.pdf')
                ->withMime('application/pdf')
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Entradas de Cine - Confirmación de Compra',
        );
    }

    public function content(): Content
{
    return new Content(
        view: 'mail.hella',
        with: [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'pdfPath' => $this->pdfPath, // Pasar la ruta del PDF a la vista
        ]
    );
}


    private function generatePDF()
    {
        $data = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
        ];

        $pdf = Pdf::loadView('pdf.confirmacion', $data);

        // Guardar el PDF temporalmente en storage
        $pdfPath = storage_path('app/public/download.pdf');
        $pdf->save($pdfPath);

        return $pdfPath;
    }
}
