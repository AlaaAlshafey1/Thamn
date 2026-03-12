<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\FCMOperation;

class NotificationController extends Controller
{
    use FCMOperation;
    /**
     * Get Notifications
     * GET /notifications
     */
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $notifications = $request->user()->notifications()->paginate($request->get('per_page', 20));

        $data = $notifications->getCollection()->map(function ($notification) use ($lang) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->data['title'] ?? ($notification->data['title_' . $lang] ?? ''),
                'message' => $notification->data['message'] ?? ($notification->data['message_' . $lang] ?? ''),
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم استرجاع التنبيهات بنجاح' : 'Notifications fetched successfully',
            'data' => $data,
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]
        ]);
    }

    /**
     * Mark Notification as Read
     * POST /notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark All as Read
     * POST /notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Test Firebase Notification
     * POST /notifications/test
     */
    public function testNotification(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'title' => 'nullable|string',
            'body' => 'nullable|string',
        ]);

        $title = $request->title ?? 'Test Notification';
        $body = $request->body ?? 'This is a test notification from Thamn system';

        $response = $this->notifyByFirebase(
            $title,
            $body,
            [$request->fcm_token],
            ['data' => ['type' => 'test_notification']]
        );

        return response()->json([
            'status' => true,
            'message' => 'Notification test executed',
            'firebase_response' => $response
        ]);
    }
}
