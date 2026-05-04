<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RefundRequest;

class NewRefundRequestNotification extends Notification
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
            'title' => 'طلب استرداد جديد',
            'message' => "قام العميل {$this->refund->user->first_name} بتقديم طلب استرداد لمبلغ " . number_format($this->refund->amount, 2) . " ريال للطلب رقم #{$this->refund->order_id}",
        ];
    }
}
