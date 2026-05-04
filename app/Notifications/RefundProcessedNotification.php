<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RefundRequest;

class RefundProcessedNotification extends Notification
{
    use Queueable;

    protected RefundRequest $refund;

    public function __construct(RefundRequest $refund)
    {
        $this->refund = $refund;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'refund_id' => $this->refund->id,
            'order_id' => $this->refund->order_id,
            'title' => 'تم استرداد المبلغ بنجاح',
            'message' => "تم تحويل مبلغ الاسترداد الخاص بالطلب #{$this->refund->order_id} إلى حسابك البنكي بنجاح.",
        ];
    }
}
