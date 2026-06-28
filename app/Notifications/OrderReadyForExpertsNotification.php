<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyForExpertsNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail', \App\Channels\WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'title' => 'طلب تقييم جديد',
            'message' => "طلب تقييم جديد متاح الآن (رقم {$this->order->id}) لمن يرغب في البدء.",
            'type' => 'new_expert_order'
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "منصة ثمن 🔔\n" . "طلب تقييم جديد متاح الآن (رقم {$this->order->id}) لمن يرغب في البدء.";
    }
}