<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArbitratorDeclarationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $arbitrator;
    public $declarationUrl;

    public function __construct($arbitrator, string $declarationUrl)
    {
        $this->arbitrator = $arbitrator;
        $this->declarationUrl = $declarationUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'وثيقة الشروط والأحكام وإقرار السرية - تطبيق ثمن',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.arbitrator_declaration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
