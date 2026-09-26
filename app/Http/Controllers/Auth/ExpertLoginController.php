<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\WhatsAppService;

class ExpertLoginController extends Controller
{
    /**
     * Show the expert OTP login form.
     */
    public function showForm()
    {
        return view('auth.expert-login');
    }

    /**
     * Generate and send OTP via WhatsApp or Email based on identifier.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:100'
        ], [
            'identifier.required' => 'رقم الجوال أو البريد الإلكتروني مطلوب',
        ]);

        $identifier = trim($request->identifier);
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

        // Find user by phone or email
        $user = User::where(function ($q) use ($identifier, $isEmail) {
            if ($isEmail) {
                $q->where('email', $identifier);
            } else {
                $q->where('phone', $identifier);
            }
        })->whereNull('deleted_at')->first();

        if (!$user) {
            $errorKey = $isEmail ? 'identifier' : 'identifier';
            $errorMsg = $isEmail
                ? 'البريد الإلكتروني غير مسجل لدينا'
                : 'رقم الجوال غير مسجل لدينا';
            return back()->withErrors(['identifier' => $errorMsg])->withInput();
        }

        // Check expert role
        if (!$user->hasRole('expert') && !$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            return back()->withErrors(['identifier' => 'هذا الحساب لا يملك صلاحية الدخول كخبير'])->withInput();
        }

        if (isset($user->is_active) && !$user->is_active) {
            return back()->withErrors(['identifier' => 'حسابك غير مفعل بعد، يرجى انتظار موافقة الإدارة'])->withInput();
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Remove old OTPs for this user
        DB::table('otps')->where('user_id', $user->id)->where('type', 'login')->delete();

        // Save to DB
        DB::table('otps')->insert([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'type'       => 'login',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sent = false;

        if ($isEmail) {
            // Send via Email
            try {
                Mail::to($user->email)->send(new OTPMail($otp, $user->first_name . ' ' . $user->last_name));
                $sent = true;
                $successMsg = 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.';
            } catch (\Exception $e) {
                Log::error('Expert OTP Email Failed: ' . $e->getMessage());
            }
        } else {
            // Send via WhatsApp
            try {
                $whatsapp = app(WhatsAppService::class);
                $whatsapp->sendMessage($user->phone, "رمز تسجيل الدخول الخاص بك في منصة ثمن هو: $otp\nيرجى عدم مشاركته مع أحد.");
                $sent = true;
                $successMsg = 'تم إرسال رمز التحقق إلى رقم جوالك على الواتساب.';
            } catch (\Exception $e) {
                Log::error('Expert OTP WhatsApp Failed: ' . $e->getMessage());
            }
        }

        if (!$sent) {
            return back()->withErrors(['identifier' => 'حدث خطأ أثناء إرسال الرمز، يرجى المحاولة لاحقاً'])->withInput();
        }

        // Store user id in session for the verify step
        session(['expert_login_user_id' => $user->id, 'expert_otp_via' => $isEmail ? 'email' : 'phone']);

        return back()
            ->with('otp_sent', true)
            ->with('success', $successMsg);
    }

    /**
     * Verify OTP and Login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4'
        ], [
            'otp.required' => 'رمز التحقق مطلوب',
            'otp.digits'   => 'رمز التحقق يجب أن يتكون من 4 أرقام',
        ]);

        $userId = session('expert_login_user_id');
        if (!$userId) {
            return redirect()->route('expert.login')->withErrors(['identifier' => 'انتهت الجلسة، يرجى المحاولة من جديد']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('expert.login')->withErrors(['identifier' => 'حدث خطأ، يرجى المحاولة من جديد']);
        }

        // Verify OTP
        $otpRecord = DB::table('otps')
            ->where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('type', 'login')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->with('otp_sent', true)->withErrors(['otp' => 'رمز التحقق غير صحيح أو منتهي الصلاحية'])->withInput();
        }

        // Mark OTP as used
        DB::table('otps')->where('id', $otpRecord->id)->update(['is_used' => true]);

        // Login user
        Auth::login($user);

        // Clear session
        $request->session()->forget(['expert_login_user_id', 'expert_otp_via']);

        return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح، مرحباً بك في لوحة التحكم.');
    }
}
