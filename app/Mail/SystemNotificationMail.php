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

    public function __construct($title, $messageBody, $actionUrl = null)
    {
        $this->title = $title;
        $this->messageBody = $messageBody;
        $this->actionUrl = $actionUrl;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.system_notification');
    }
}
