<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Order;

class OrderThamnPriceCalculated extends Notification
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
            'message' => "تم تثمين ثمن منتجك: Order #{$this->order->id} → السعر النهائي: {$this->order->thamn_price} SAR",
            'thamn_price' => $this->order->thamn_price,
        ];
    }
}
