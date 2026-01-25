<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderEvaluated extends Notification
{
    use Queueable;

    public $order;
    public $type;

    public function __construct($order, $type)
    {
        $this->order = $order;
        $this->type = $type; // ai - expert - thamn - update
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // تقدر تحذف mail لو مش محتاج
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تم تقييم طلبك بنجاح')
            ->line("تم تقييم طلبك رقم #{$this->order->id} بنجاح بواسطة: {$this->type}")
            ->line("السعر النهائي: {$this->order->total_price}")
            ->action('عرض الطلب', url(route('orders.show', $this->order->id)))
            ->line('شكراً لاستخدامك خدمتنا!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'type' => $this->type,
            'total_price' => $this->order->total_price,
            'message' => "تم تقييم طلبك رقم #{$this->order->id} بنجاح"
        ];
    }
}
