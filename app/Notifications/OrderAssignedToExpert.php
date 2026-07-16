<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Order;

class OrderAssignedToExpert extends Notification
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', \App\Channels\WhatsAppChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'هلا بك خبير 👋 طلب تثمين احترافي جديد',
            'message' => "هلا بك خبير 👋 وصل طلب تثمين احترافي جديد رقم {$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.",
            'user_id' => $this->order->user_id,
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "هلا بك خبير 👋 وصل طلب تثمين احترافي جديد رقم #{$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.";
    }
}