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
            $unreadCount = \Illuminate\Support\Facades\DB::table('notifications')
                ->whereNull('read_at')
                ->where('notifiable_id', $data['data']['user_id'])
                ->count();
        }

        $androidConfig = [
            'priority' => 'high',
        ];

        if ($withNotification) {
            $androidConfig['notification'] = [
                'sound'                   => 'default',
                'channel_id'              => 'default',
                'click_action'            => 'FLUTTER_NOTIFICATION_CLICK',
                'notification_count'      => $unreadCount,
                'notification_priority'   => 'PRIORITY_HIGH',
                'default_sound'           => true,
                'default_vibrate_timings' => true,
            ];
        }

        $android = AndroidConfig::fromArray($androidConfig);

        // إعدادات APNs الخاصة بـ iOS لتشغيل وتحديث الخلفية
        $apsPayload = [
            'content-available' => 1,
            'badge'             => $unreadCount,
        ];
        if ($withNotification) {
            $apsPayload['sound'] = 'default';
        }

        $apnsHeaders = [
            'apns-priority'  => $withNotification ? '10' : '5',
            'apns-push-type' => $withNotification ? 'alert' : 'background',
        ];

        // بناء حزمة الـ Data وضمان وجود refresh: "1" مع تحويل كافة القيم لنصوص
        $fcmData = [
            'refresh'      => '1',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'unread_count' => (string) $unreadCount,
        ];

        if (isset($data['data']) && is_array($data['data'])) {
            $data['data']['refresh'] = '1';
            foreach ($data['data'] as $k => $v) {
                if (is_scalar($v) || is_null($v)) {
                    $fcmData[(string) $k] = (string) ($v ?? '');
                }
            }
        } elseif (!empty($data) && is_array($data)) {
            $data['refresh'] = '1';
            foreach ($data as $k => $v) {
                if (is_scalar($v) || is_null($v)) {
                    $fcmData[(string) $k] = (string) ($v ?? '');
                }
            }
        }

        $fcmData['refresh'] = '1';
        $fcmData['payload'] = json_encode($data);

        // تمرير البيانات المخصصة في APNs أيضاً لسرعة التقاطها في خلفية iOS
        $apnsPayloadData = [
            'aps'     => $apsPayload,
            'refresh' => '1',
        ];
        foreach ($fcmData as $k => $v) {
            if ($k !== 'payload') {
                $apnsPayloadData[$k] = $v;
            }
        }

        $apns = ApnsConfig::fromArray([
            'headers' => $apnsHeaders,
            'payload' => $apnsPayloadData,
        ]);

        $message = CloudMessage::new()
            ->withAndroidConfig($android)
            ->withApnsConfig($apns)
            ->withData($fcmData);

        if ($withNotification) {
            $message = $message->withNotification(Notification::create($title, $body));
        }

        $report = $messaging->sendMulticast($message, $tokens);

        $errors = [];
        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                $errors[] = [
                    'error' => $failure->error()->getMessage(),
                ];
            }
            \Illuminate\Support\Facades\Log::warning('FCM Multicast failures: ' . json_encode($errors));
        }

        return [
            'success' => true,
            'sent'    => $report->successes()->count(),
            'failed'  => $report->failures()->count(),
            'errors'  => $errors,
            'unread'  => $unreadCount,
        ];

    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('FCM notifyByFirebase Exception: ' . $e->getMessage(), [
            'exception' => $e
        ]);

        return [
            'success' => false,
            'error'   => $e->getMessage(),
        ];
    }
}



}