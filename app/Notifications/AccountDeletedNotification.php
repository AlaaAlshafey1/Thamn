<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountDeletedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', 'mail', \App\Channels\WhatsAppChannel::class];
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
        return "منصة ثمن 🔔\n" . 'تم حذف حسابك بنجاح ويمكنك استعادته لاحقاً';
    }
}