<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class ExpertEvaluationWithdrawnNotification extends Notification
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
            'title' => 'تم سحب الطلب منك 🚫',
            'message' => "نأسف لإبلاغك بأنه تم سحب الطلب رقم #{$this->order->id} منك وذلك بسبب تأخرك في إتمام التقييم. هذا الإجراء يأتي ضمن سياسة التطبيق لضمان سرعة الخدمة وجودتها. نقدر جهودك ونتطلع لتعاونك في الطلبات القادمة بشكل أسرع 💪",
        ];
    }
}
