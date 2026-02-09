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
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'expert_id' => $this->expert->id,
            'message' => "قام الخبير {$this->expert->first_name} بتقييم الطلب رقم {$this->order->id}.",
            'type' => 'expert_evaluation_completed'
        ];
    }
}
