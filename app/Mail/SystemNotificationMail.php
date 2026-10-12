<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $messageBody;
    public $actionUrl;
    public $actionLabel;

    public function __construct($title, $messageBody, $actionUrl = null, $actionLabel = null)
    {
        $this->title = $title;
        $this->messageBody = $messageBody;
        $this->actionUrl = $actionUrl;
        $this->actionLabel = $actionLabel;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.system_notification');
    }
}
