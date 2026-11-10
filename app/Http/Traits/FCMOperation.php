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

/**
 * Send pure data-only (silent push) FCM message without notification object.
 * Output payload structure:
 * {
 *   "message": {
 *     "token": "DEVICE_FCM_TOKEN",
 *     "data": {
 *       "type": "order_updated",
 *       "order_id": "8",
 *       "refresh": "1"
 *     },
 *     "android": {
 *       "priority": "high"
 *     }
 *   }
 * }
 *
 * @param string|array $tokens
 * @param array $data
 * @return array
 */
public static function sendDataOnly($tokens, array $data): array
{
    try {
        $tokens = is_array($tokens) ? $tokens : [$tokens];
        $tokens = array_values(array_filter($tokens, function ($t) {
            return !empty($t) && !in_array(strtolower($t), [
                'fcm_android_token_123',
                'fcm_ios_token_123',
                'test',
                'null',
                'undefined',
                '123456'
            ]);
        }));

        if (empty($tokens)) {
            return ['success' => false, 'error' => 'No valid tokens'];
        }

        $credentialsPath = storage_path('app/firebase/firebase_credentials.json');
        if (!file_exists($credentialsPath)) {
            \Illuminate\Support\Facades\Log::warning("FCM credentials file not found: {$credentialsPath}");
            return ['success' => false, 'error' => 'Credentials not found'];
        }

        $messaging = (new Factory)
            ->withServiceAccount($credentialsPath)
            ->createMessaging();

        // Android Config: priority high and NO notification block (pure data-only)
        $android = AndroidConfig::fromArray([
            'priority' => 'high',
        ]);

        // Clean string data map
        $fcmData = [];
        foreach ($data as $k => $v) {
            if (is_scalar($v) || is_null($v)) {
                $fcmData[(string) $k] = (string) ($v ?? '');
            }
        }

        if (!isset($fcmData['refresh'])) {
            $fcmData['refresh'] = '1';
        }

        // APNs Config for iOS background wake-up
        $apnsPayload = [
            'aps' => [
                'content-available' => 1,
            ],
        ];
        foreach ($fcmData as $k => $v) {
            $apnsPayload[(string) $k] = $v;
        }

        $apns = ApnsConfig::fromArray([
            'headers' => [
                'apns-priority'  => '5', // 5 for background silent push on iOS
                'apns-push-type' => 'background',
            ],
            'payload' => $apnsPayload,
        ]);

        $message = CloudMessage::new()
            ->withAndroidConfig($android)
            ->withApnsConfig($apns)
            ->withData($fcmData);

        $report = $messaging->sendMulticast($message, $tokens);

        $errors = [];
        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                $errors[] = [
                    'error' => $failure->error()->getMessage(),
                ];
            }
            \Illuminate\Support\Facades\Log::warning('FCM Data-Only Multicast failures: ' . json_encode($errors));
        }

        \Illuminate\Support\Facades\Log::info("FCM Data-Only sent to " . count($tokens) . " tokens", [
            'data'      => $fcmData,
            'successes' => $report->successes()->count(),
            'failures'  => $report->failures()->count(),
        ]);

        return [
            'success' => true,
            'sent'    => $report->successes()->count(),
            'failed'  => $report->failures()->count(),
            'errors'  => $errors,
        ];

    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('FCM sendDataOnly Exception: ' . $e->getMessage(), [
            'data' => $data,
        ]);

        return [
            'success' => false,
            'error'   => $e->getMessage(),
        ];
    }
}

/**
 * Instance wrapper for sendDataOnly.
 */
public function sendDataOnlyFCM($tokens, array $data): array
{
    return self::sendDataOnly($tokens, $data);
}

}