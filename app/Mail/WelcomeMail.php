<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $title;

    public function __construct(User $user, $title = 'مرحباً بك في ثمن')
    {
        $this->user = $user;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.welcome');
    }
}
