<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderAcceptedByExpertNotification extends Notification
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
            'title' => 'جاري العمل على طلبك',
            'message' => "جارى العمل على الطلب بتاعكم وسوف يتم الرد ف حد اقصى 24 ساعة للطلب رقم #{$this->order->id}",
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "جارى العمل على الطلب بتاعكم وسوف يتم الرد ف حد اقصى 24 ساعة للطلب رقم #{$this->order->id}";
    }
}