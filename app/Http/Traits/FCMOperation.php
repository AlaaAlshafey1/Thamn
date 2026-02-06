<?php


namespace App\Http\Traits;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\{ AndroidConfig, ApnsConfig};
trait FCMOperation
{


public function notifyByFirebase($title, $body, $tokens, array $data = [], bool $withNotification = true)
{
    try {
        $messaging = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))
            ->createMessaging();

        $tokens = is_array($tokens) ? $tokens : [$tokens];

        $unreadCount = 0;
        if (isset($data['data']['user_id'])) {
            $unreadCount = \App\Models\Notification::where('read', 'no')
                ->where('user_id', $data['data']['user_id'])
                ->count();

        }

        $android = AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'channel_id' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'notification_count' => $unreadCount,
            ],
        ]);

        $apns = ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'content-available' => 1,
                    'badge' =>$unreadCount,
                ],
            ],
        ]);

        if (isset($data['data']) && is_object($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }

        $message = CloudMessage::new()
            ->withAndroidConfig($android)
            ->withApnsConfig($apns)
            ->withData([
                'payload' => json_encode($data),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'unread_count' => (string)$unreadCount,
            ]);

        if ($withNotification) {
            $message = $message->withNotification(Notification::create($title, $body));
        }

        $report = $messaging->sendMulticast($message, $tokens);

        $errors = [];
        foreach ($report->failures() as $failure) {
            $errors[] = [
                'token' => method_exists($failure->target(), 'value')
                    ? $failure->target()->value()
                    : null,
                'error' => $failure->error()->getMessage(),
            ];
        }

        return [
            'success' => true,
            'sent'    => $report->successes()->count(),
            'failed'  => $report->failures()->count(),
            'errors'  => $errors,
            'unread'  => $unreadCount,
        ];

    } catch (\Throwable $e) {

        return [
            'success' => false,
            'error'   => $e->getMessage(),
        ];
    }
}



}
