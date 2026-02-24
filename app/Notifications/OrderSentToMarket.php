<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderSentToMarket extends Notification
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'تم إرسال الطلب للسوق',
            'message' => "تم إرسال منتجك للسوق: طلب رقم #{$this->order->id}، السعر: {$this->order->thamn_price} ريال سعودي"
        ];
    }
}
