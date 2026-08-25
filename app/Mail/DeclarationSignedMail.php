<?php

namespace App\Mail;

use App\Models\ArbitratorDeclaration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DeclarationSignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ArbitratorDeclaration $declaration;
    public string $downloadUrl;

    public function __construct(ArbitratorDeclaration $declaration, string $downloadUrl)
    {
        $this->declaration = $declaration;
        $this->downloadUrl = $downloadUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'نسختك من الاتفاقية القانونية للتعاون - تطبيق ثمن',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.declaration_signed',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->declaration->pdf_path && Storage::disk('public')->exists($this->declaration->pdf_path)) {
            $fullPath = Storage::disk('public')->path($this->declaration->pdf_path);

            if (file_exists($fullPath)) {
                $attachments[] = Attachment::fromPath($fullPath)
                    ->as('الاتفاقية_القانونية_' . $this->declaration->full_name . '.pdf')
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
