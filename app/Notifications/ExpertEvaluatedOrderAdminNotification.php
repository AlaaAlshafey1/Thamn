<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpertEvaluatedOrderAdminNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $expert;

    public function __construct($order, $expert)
    {
        $this->order = $order;
        $this->expert = $expert;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('خبير قام بتقييم طلب')
            ->line("قام الخبير {$this->expert->first_name} بتقييم الطلب رقم #{$this->order->id}.")
            ->action('عرض الطلب', url(route('orders.show', $this->order->id)))
            ->line('يرجى الدخول إلى لوحة التحكم واعتماد التقييم النهائي.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'expert_id' => $this->expert->id,
            'title' => 'اكتمل تقييم الخبير',
            'message' => "قام الخبير {$this->expert->first_name} بتقييم الطلب رقم {$this->order->id}.",
            'type' => 'expert_evaluation_completed'
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "منصة ثمن 🔔\n" . "قام الخبير {$this->expert->first_name} بتقييم الطلب رقم {$this->order->id}.";
    }
}