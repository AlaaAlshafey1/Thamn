<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تطبيق ثمن - تم استرداد المبلغ بنجاح')
            ->line("تم تحويل مبلغ الاسترداد الخاص بالطلب {$this->refund->order_id} إلى حسابك البنكي بنجاح.")
            ->line('شكراً لاستخدامك تطبيق ثمن');
    }

    public function toDatabase($notifiable)
    {
        return [
            'refund_id' => $this->refund->id,
            'order_id' => $this->refund->order_id,
            'title' => 'تم استرداد المبلغ بنجاح',
            'message' => "تم تحويل مبلغ الاسترداد الخاص بالطلب {$this->refund->order_id} إلى حسابك البنكي بنجاح.",
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . "تم تحويل مبلغ الاسترداد الخاص بالطلب {$this->refund->order_id} إلى حسابك البنكي بنجاح.";
    }
}
