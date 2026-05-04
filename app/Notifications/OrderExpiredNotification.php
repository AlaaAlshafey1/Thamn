<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderExpiredNotification extends Notification
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
            'title' => 'لم يتم قبول الطلب',
            'message' => "لم يتم قبول الطلب بتاعك من قبل اى خبير وبامكانك استرداد الطلب للطلب رقم #{$this->order->id}",
        ];
    }
}
