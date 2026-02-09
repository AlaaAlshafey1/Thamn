<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpertValuationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $expert;

    public function __construct($order, $expert)
    {
        $this->order = $order;
        $this->expert = $expert;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم تقييم طلب جديد بواسطة خبير - منصة تثمين',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expert_valuation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
