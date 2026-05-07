<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Http\Traits\FCMOperation;

class SocialAuthController extends Controller
{
    use FCMOperation;



    public function checkSocialAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:google',
            'social_id' => 'required|string',
            'email' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('social_id', $request->social_id)
            ->where('social_provider', $request->provider)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => true,
                'is_registered' => false,
                'message' => lang(
                    'الحساب غير مسجل',
                    'Account not registered',
                    $request
                )
            ]);
        }

        // ✅ الحساب موجود → اعمل Login
        $token = $user->createToken('API Token')->plainTextToken;

        // Send FCM: Social Login Successful
        $fcmToken = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('مرحباً بك في ثمن', 'Welcome back to Thamn', $request),
                lang('تم تسجيل الدخول بنجاح عبر ' . ucfirst($request->provider), 'Successfully logged in via ' . ucfirst($request->provider), $request),
                [$fcmToken],
                ['data' => ['user_id' => $user->id, 'type' => 'social_login_success']]
            );
        }

        return response()->json([
            'status' => true,
            'message' => lang(
                'تم تسجيل الدخول بنجاح',
                'Login successful',
                $request
            ),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }



    public function registerSocialAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:google',
            'social_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update([
                'social_id' => $request->social_id,
                'social_provider' => $request->provider,
            ]);
        } else {
            $fullName = explode(" ", $request->name);
            $first = $fullName[0] ?? '';
            $last = $fullName[1] ?? '';

            $data = [
                'first_name' => $first,
                'last_name' => $last,
                'email' => $request->email,
                'password' => Hash::make(uniqid()),
                'phone' => $request->phone ?? null,
                'social_id' => $request->social_id,
                'social_provider' => $request->provider,
                'is_verified' => "1",
            ];

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('users', 'public');
            }

            $user = User::create($data);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        // Send FCM: Social Register/Login Successful
        $fcmToken = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('مرحباً بك في ثمن', 'Welcome to Thamn', $request),
                lang('تم تسجيل دخولك بنجاح عبر ' . ucfirst($request->provider), 'Successfully logged in via ' . ucfirst($request->provider), $request),
                [$fcmToken],
                ['data' => ['user_id' => $user->id, 'type' => 'social_register_success']]
            );
        }

        // Send WhatsApp Welcome Message if phone is provided
        if ($user->phone) {
            try {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $message = \App\Services\WhatsAppService::getTemplate('welcome_social', ['name' => $user->first_name]);
                $whatsapp->sendMessage($user->phone, $message);
            } catch (\Exception $e) {
                \Log::error('WhatsApp Welcome Social Failed: ' . $e->getMessage());
            }
        }

        return response()->json([

            'status' => true,
            'message' => 'Social Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }
}
