<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyForExpertsNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail', \App\Channels\WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'title' => 'هلا بك خبير 👋 طلب تثمين احترافي جديد',
            'message' => "هلا بك خبير 👋 وصل طلب تثمين احترافي جديد رقم {$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.",
            'type' => 'new_expert_order'
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "هلا بك خبير 👋 وصل طلب تثمين احترافي جديد رقم {$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.";
    }
}