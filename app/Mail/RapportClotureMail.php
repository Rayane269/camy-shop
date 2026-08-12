<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class RapportClotureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $donnees;
    public $pdfPath;

    public function __construct($donnees, $pdfPath)
    {
        $this->donnees = $donnees;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Clôture de Caisse - ' . date('d/m/Y') . ' - Ray-Multitech',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cloture',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('cloture_' . date('Y-m-d') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}