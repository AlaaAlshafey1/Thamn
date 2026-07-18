<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Generate and send OTP via WhatsApp.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20'
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'رقم الجوال غير مسجل لدينا'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['phone' => 'حسابك غير مفعل بعد، يرجى انتظار موافقة الإدارة'])->withInput();
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Save to DB
        DB::table('otps')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'type' => 'login',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Send via WhatsApp
        try {
            $whatsapp = app(WhatsAppService::class);
            $whatsapp->sendMessage($user->phone, "رمز تسجيل الدخول الخاص بك في منصة ثمن هو: $otp\nيرجى عدم مشاركته مع أحد.");
            
            // Store phone in session for the verify step
            session(['expert_login_phone' => $user->phone]);
            
            return back()->with('otp_sent', true)->with('success', 'تم إرسال رمز التحقق إلى رقم جوالك في الواتساب.');
        } catch (\Exception $e) {
            \Log::error('Expert OTP Send Failed: ' . $e->getMessage());
            return back()->withErrors(['phone' => 'حدث خطأ أثناء إرسال الرمز، يرجى المحاولة لاحقاً'])->withInput();
        }
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
            'otp.digits' => 'رمز التحقق يجب أن يتكون من 4 أرقام',
        ]);

        $phone = session('expert_login_phone');
        if (!$phone) {
            return redirect()->route('expert.login')->withErrors(['phone' => 'انتهت الجلسة، يرجى المحاولة من جديد']);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return redirect()->route('expert.login')->withErrors(['phone' => 'حدث خطأ، يرجى المحاولة من جديد']);
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
        $request->session()->forget('expert_login_phone');

        return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح، مرحباً بك في لوحة التحكم.');
    }
}
