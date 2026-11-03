<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تطبيق ثمن - طلب استرداد جديد')
            ->line("قام العميل {$this->refund->user->first_name} بتقديم طلب استرداد لمبلغ " . number_format($this->refund->amount, 2) . " ريال للطلب رقم {$this->refund->order_id}")
            ->line('يرجى مراجعة لوحة التحكم لاتخاذ الإجراء المناسب.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'refund_id' => $this->refund->id,
            'order_id' => $this->refund->order_id,
            'title' => 'طلب استرداد جديد',
            'message' => "قام العميل {$this->refund->user->first_name} بتقديم طلب استرداد لمبلغ " . number_format($this->refund->amount, 2) . " ريال للطلب رقم {$this->refund->order_id}",
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "قام العميل {$this->refund->user->first_name} بتقديم طلب استرداد لمبلغ " . number_format($this->refund->amount, 2) . " ريال للطلب رقم {$this->refund->order_id}";
    }
}
