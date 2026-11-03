<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تطبيق ثمن - لم يتم قبول الطلب')
            ->line("لم يتم قبول الطلب بتاعك من قبل اى خبير وبامكانك استرداد الطلب للطلب رقم {$this->order->id}")
            ->action('عرض الطلب', url(route('orders.show', $this->order->id)))
            ->line('شكراً لاستخدامك تطبيق ثمن');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'لم يتم قبول الطلب',
            'message' => "لم يتم قبول الطلب بتاعك من قبل اى خبير وبامكانك استرداد الطلب للطلب رقم {$this->order->id}",
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "لم يتم قبول الطلب بتاعك من قبل اى خبير وبامكانك استرداد الطلب للطلب رقم {$this->order->id}";
    }
}
