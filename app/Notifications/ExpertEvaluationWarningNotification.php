<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class ExpertEvaluationWarningNotification extends Notification
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
            'title' => 'تنبيه: تأخر في تقييم الطلب ⏰',
            'message' => "مرحباً، مر أكثر من ساعة على استلامك الطلب رقم #{$this->order->id} ولم يتم التقييم بعد. نرجو منك إتمام التقييم في أسرع وقت ممكن حتى لا يتم سحب الطلب منك. شاكرين تعاونك 🙏",
        ];
    }
}
