<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تطبيق ثمن - تم إرسال الطلب للسوق')
            ->line("تم إرسال منتجك للسوق: طلب رقم {$this->order->id}، السعر: {$this->order->thamn_price} ريال سعودي")
            ->action('عرض الطلب', url(route('orders.show', $this->order->id)))
            ->line('شكراً لاستخدامك تطبيق ثمن');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'تم إرسال الطلب للسوق',
            'message' => "تم إرسال منتجك للسوق: طلب رقم {$this->order->id}، السعر: {$this->order->thamn_price} ريال سعودي"
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "تم إرسال منتجك للسوق: طلب رقم {$this->order->id}، السعر: {$this->order->thamn_price} ريال سعودي";
    }
}
