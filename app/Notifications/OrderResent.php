<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderResent extends Notification
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
            'title' => 'تم إعادة إرسال الطلب',
            'message' => "تم إعادة إرسال طلبك: طلب رقم #{$this->order->id}"
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "منصة ثمن 🔔\n" . "تم إعادة إرسال طلبك: طلب رقم #{$this->order->id}";
    }
}