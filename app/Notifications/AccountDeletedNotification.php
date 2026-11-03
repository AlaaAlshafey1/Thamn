<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountDeletedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تطبيق ثمن - تم حذف الحساب')
            ->line('تم حذف حسابك بنجاح ويمكنك استعادته لاحقاً')
            ->line('شكراً لاستخدامك تطبيق ثمن');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'تم حذف الحساب',
            'message' => 'تم حذف حسابك بنجاح ويمكنك استعادته لاحقاً',
            'title_ar' => 'تم حذف الحساب',
            'title_en' => 'Account Deleted',
            'message_ar' => 'تم حذف حسابك بنجاح ويمكنك استعادته لاحقاً',
            'message_en' => 'Your account has been deleted successfully',
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return "تطبيق ثمن 🔔\n" . 'تم حذف حسابك بنجاح ويمكنك استعادته لاحقاً';
    }
}
