<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ExpertDeclarationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'وثيقة الشروط والأحكام وإقرار السرية للمحكمين المستقلين - تطبيق ثمن',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expert_declaration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
