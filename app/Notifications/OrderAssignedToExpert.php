<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تطبيق ثمن - طلب تثمين احترافي جديد')
            ->line("هلا بك خبير ( التثمين ) 👋 وصل طلب تثمين احترافي جديد رقم {$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن.")
            ->action('عرض الطلب', url(route('orders.show', $this->order->id)))
            ->line('نرجو منك الدخول وتقييم الطلب في أسرع وقت.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'هلا بك خبير ( التثمين ) 👋 طلب تثمين احترافي جديد',
            'message' => "هلا بك خبير ( التثمين ) 👋 وصل طلب تثمين احترافي جديد رقم {$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.",
            'user_id' => $this->order->user_id,
            'type' => 'new_expert_order',
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "هلا بك خبير ( التثمين ) 👋 وصل طلب تثمين احترافي جديد رقم {$this->order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.";
    }
}
