<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Http\Traits\FCMOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    use FCMOperation;

    public function index()
    {
        $users = User::all();
        return view('admin.notifications.index', compact('users'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'recipients' => 'required|string', 
            'channels'   => 'required|array', 
            'title'      => 'nullable|string',
            'message'    => 'required|string',
            'image'      => 'nullable|image|max:5120',
            'file'       => 'nullable|file|max:10240',
        ]);

        $query = User::query();

        if ($request->recipients === 'experts') {
            $query->role('expert');
        } elseif ($request->recipients === 'users') {
            $query->whereDoesntHave('roles', function($q){
                $q->where('name', 'expert');
            });
        } elseif ($request->recipients !== 'all') {
            $ids = explode(',', $request->recipients);
            $query->whereIn('id', $ids);
        }

        $targets = $query->get();
        $whatsappService = app(WhatsAppService::class);
        
        $successCount = 0;
        $failCount = 0;

        $imageUrl = null;
        $fileUrl = null;
        $fileName = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('notifications/images', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('notifications/files', 'public');
            $fileUrl = asset('storage/' . $path);
            $fileName = $request->file('file')->getClientOriginalName();
        }

        foreach ($targets as $user) {
            $sentToUser = false;

            // 1. WhatsApp
            if (in_array('whatsapp', $request->channels) && $user->phone) {
                try {
                    if ($imageUrl) {
                        $res = $whatsappService->sendImage($user->phone, $imageUrl, $request->message);
                    } elseif ($fileUrl) {
                        $res = $whatsappService->sendDocument($user->phone, $fileUrl, $fileName, $request->message);
                    } else {
                        $res = $whatsappService->sendMessage($user->phone, $request->message);
                    }
                    if ($res) $sentToUser = true;
                } catch (\Exception $e) {
                    Log::error("Manual WhatsApp failed for user {$user->id}: " . $e->getMessage());
                }
            }

            // 2. Push Notification
            if (in_array('push', $request->channels)) {
                $token = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
                if ($token) {
                    $res = $this->notifyByFirebase(
                        $request->title ?? 'إشعار من ثمن',
                        $request->message,
                        $token,
                        ['data' => ['type' => 'manual_notification']]
                    );
                    
                    if (isset($res['sent']) && $res['sent'] > 0) {
                        $sentToUser = true;
                    }
                    
                    // الـ dd التي طلبتها للمعاينة السريعة
                    dd([
                        'user_id' => $user->id,
                        'user_name' => $user->first_name,
                        'token_used' => $token,
                        'fcm_response' => $res
                    ]);
                }
            }


            if ($sentToUser) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return back()->with('success', "تم الإرسال بنجاح لـ $successCount مستخدم. (فشل: $failCount)");
    }
}
