<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\AccountDeletedNotification;
use App\Mail\WelcomeMail;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Http\Traits\FCMOperation;

class AuthController extends Controller
{
    use FCMOperation;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:255|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'image' => 'nullable|image|max:2048',
            'fcm_token' => 'nullable|string',
            'device_type' => 'nullable|string|in:ios,android',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'fcm_token', 'device_type']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $user = User::create($data);

        // generate OTP
        $otp = rand(1000, 9999);

        \DB::table('otps')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send OTP via Email & WhatsApp
        try {
            \Log::debug("Sending OTP Email/WhatsApp to: " . $user->email . " / " . $user->phone . " with OTP: " . $otp);
            Mail::to($user->email)->send(new OTPMail($otp, $user->first_name . ' ' . $user->last_name));
            
            $whatsapp = app(\App\Services\WhatsAppService::class);
            $whatsapp->sendMessage($user->phone, "كود تفعيل حسابك في ثمن هو: $otp . لا تشاركه مع أحد يا غالي.");
            
            \Log::debug("OTP sent successfully.");
        } catch (\Exception $e) {
            \Log::error('OTP Delivery Failed (Register): ' . $e->getMessage());
        }

        // هنا تبعته SMS لو عندك خدمة

        return response()->json([
            'status' => true,
            'message' => transMsg('account_created_please_verify', $request),
            'data' => [
                'user_id' => $user->id,
                'otp' => $otp,
            ]
        ]);
    }

    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string'
        ], [
            'phone.required' => lang('رقم الهاتف مطلوب', 'Phone is required', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('phone', $request->phone)
            ->where('is_verified', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => true,
                'exists' => false,
                'message' => lang('الرقم غير مسجل', 'Phone not registered', $request),
            ]);
        }

        return response()->json([
            'status' => true,
            'exists' => true,
            'is_verified' => (bool) $user->is_verified,
            'message' => $user->is_verified
                ? lang('الرقم مسجل ومفعل', 'Phone exists and verified', $request)
                : lang('الرقم مسجل لكنه غير مفعل', 'Phone exists but not verified', $request),
        ]);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'fcm_token' => 'nullable|string',
            'device_type' => 'nullable|string|in:ios,android',
        ], [
            'phone.required' => lang(' رقم الهاتف مطلوب', ' phone is required', $request),
            'password.required' => lang('كلمة المرور مطلوبة', 'Password is required', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('phone', $request->phone)
            ->where('is_verified', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => lang('بيانات الدخول غير صحيحة', 'Invalid login credentials', $request)
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'status' => false,
                'message' => transMsg('account_not_verified', $request)
            ], 403);
        }


        $token = $user->createToken('API Token')->plainTextToken;

        // Update FCM Token if provided
        if ($request->fcm_token) {
            $user->update([
                'fcm_token' => $request->fcm_token,
                'device_type' => $request->device_type ?? $user->device_type
            ]);
        }

        // Send FCM: Login Successful
        $fcmToken = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('مرحباً بعودتك', 'Welcome back', $request),
                lang('تم تسجيل الدخول بنجاح إلى تثمين', 'Successfully logged into Thamn', $request),
                [$fcmToken],
                ['data' => ['user_id' => $user->id, 'type' => 'login_success']]
            );
        }

        // Send Welcome Mail
        try {
            Mail::to($user->email)->send(new WelcomeMail($user, lang('مرحباً بك في ثمن', 'Welcome to Thamn', $request)));
        } catch (\Exception $e) {
            \Log::error('Welcome Mail Failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => lang('تم تسجيل الدخول بنجاح', 'Login successful', $request),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => lang('تم تسجيل الخروج بنجاح', 'Logout successful', $request)
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $otp = \DB::table('otps')
            ->where('user_id', $request->user_id)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otp) {
            return response()->json([
                'status' => false,
                'message' => transMsg('otp_invalid_or_expired', $request)
            ], 400);
        }

        // mark otp used
        \DB::table('otps')->where('id', $otp->id)->update(['is_used' => true]);

        User::where('id', $request->user_id)->update(['is_verified' => true]);

        $user = User::find($request->user_id);

        // Send FCM Notification for Register/Verify
        $fcmToken = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('مرحباً بك في ثمن', 'Welcome to Thamn', $request),
                lang('تم تفعيل حسابك بنجاح، استكشف عالم التثمين الآن', 'Account verified successfully, explore the world of evaluation now', $request),
                [$fcmToken],
                ['data' => ['user_id' => $user->id, 'type' => 'register_success']]
            );
        }

        // Send Welcome Mail
        try {
            Mail::to($user->email)->send(new WelcomeMail($user, lang('مرحباً بك في ثمن', 'Welcome to Thamn', $request)));
        } catch (\Exception $e) {
            \Log::error('Welcome Mail Failed (Register): ' . $e->getMessage());
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => transMsg('account_verified_successfully', $request),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $otp = rand(1000, 9999);

        \DB::table('otps')->insert([
            'user_id' => $request->user_id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::find($request->user_id);

        // Send OTP via Email & WhatsApp
        try {
            Mail::to($user->email)->send(new OTPMail($otp, $user->first_name . ' ' . $user->last_name));
            
            $whatsapp = app(\App\Services\WhatsAppService::class);
            $whatsapp->sendMessage($user->phone, "كود تفعيل حسابك الجديد في ثمن هو: $otp");
        } catch (\Exception $e) {
            \Log::error('OTP Delivery Failed (Resend): ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => transMsg('otp_sent_successfully', $request),
            'otp' => $otp
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => transMsg('profile_fetched_successfully', $request),
            'data' => new UserResource($request->user())
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if ($request->filled('first_name'))
            $user->first_name = $request->first_name;
        if ($request->filled('last_name'))
            $user->last_name = $request->last_name;
        if ($request->filled('email'))
            $user->email = $request->email;
        if ($request->filled('phone'))
            $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->image && \Storage::disk('public')->exists($user->image)) {
                \Storage::disk('public')->delete($user->image);
            }

            $user->image = $request->file('image')->store('users', 'public');
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => transMsg('profile_updated_successfully', $request),
            'data' => new UserResource($user)
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ], [
            'phone.required' => lang('رقم الهاتف مطلوب', 'Phone is required', $request),
            'phone.exists' => lang('رقم الهاتف غير مسجل', 'Phone not registered', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        // generate OTP
        $otp = rand(1000, 9999);

        \DB::table('otps')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'type' => 'reset_password', // مهم
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        return response()->json([
            'status' => true,
            'message' => lang(
                'تم إرسال رمز إعادة تعيين كلمة المرور',
                'Reset password code sent',
                $request
            ),
            'data' => [
                'otp' => $otp,
                'user_id' => $user->id
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => lang('كلمة المرور الحالية مطلوبة', 'Current password is required', $request),
            'new_password.required' => lang('كلمة المرور الجديدة مطلوبة', 'New password is required', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => lang(
                    'كلمة المرور الحالية غير صحيحة',
                    'Current password is incorrect',
                    $request
                )
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // إلغاء كل التوكنات
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => lang(
                'تم تغيير كلمة المرور بنجاح',
                'Password changed successfully',
                $request
            )
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // حذف التوكنات
        $user->tokens()->delete();

        // Hard Delete (Permanent)
        $user->forceDelete();

        // Notification (database) - Removed as user record is permanently deleted

        return response()->json([
            'status' => true,
            'message' => lang(
                'تم حذف الحساب نهائياً بنجاح',
                'Account permanently deleted successfully',
                $request
            )
        ]);
    }



}
