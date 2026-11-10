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
    public function showForm(Request $request)
    {
        if (Auth::check() && (Auth::user()->hasRole('expert') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('superadmin'))) {
            return redirect()->route('dashboard');
        }

        if ($request->has('retry')) {
            session()->forget(['expert_login_user_id', 'expert_otp_via', 'otp_sent']);
        }

        return view('auth.expert-login');
    }

    /**
     * Generate and send OTP via WhatsApp and Email.
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

        // Find user by phone or email (supports common Saudi phone variations)
        $user = User::where(function ($q) use ($identifier, $isEmail) {
            if ($isEmail) {
                $q->where('email', $identifier);
            } else {
                $cleanPhone = preg_replace('/[^0-9]/', '', $identifier);
                $phoneVariants = array_unique(array_filter([
                    $identifier,
                    $cleanPhone,
                    str_starts_with($cleanPhone, '966') ? '0' . substr($cleanPhone, 3) : null,
                    str_starts_with($cleanPhone, '0') ? '966' . substr($cleanPhone, 1) : null,
                    str_starts_with($cleanPhone, '5') ? '0' . $cleanPhone : null,
                    str_starts_with($cleanPhone, '5') ? '966' . $cleanPhone : null,
                    '+' . $cleanPhone,
                ]));
                $q->whereIn('phone', $phoneVariants);
            }
        })->whereNull('deleted_at')->first();

        if (!$user) {
            $errorMsg = $isEmail
                ? 'البريد الإلكتروني غير مسجل لدينا'
                : 'رقم الجوال أو البريد الإلكتروني غير مسجل لدينا';
            return back()->withErrors(['identifier' => $errorMsg])->withInput();
        }

        // Check expert role
        if (!$user->hasRole('expert') && !$user->hasRole('admin') && !$user->hasRole('super-admin') && !$user->hasRole('superadmin')) {
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

        $emailSent = false;
        $whatsappSent = false;

        // 1. Send via Email (always attempt if user has email)
        if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                Mail::to($user->email)->send(new OTPMail($otp, $fullName ?: 'عزيزنا الخبير'));
                $emailSent = true;
                Log::info("Expert OTP sent via Email to: {$user->email}");
            } catch (\Exception $e) {
                Log::error('Expert OTP Email Failed: ' . $e->getMessage());
            }
        }

        // 2. Send via WhatsApp (always attempt in parallel if user has phone)
        if (!empty($user->phone)) {
            try {
                $whatsapp = app(WhatsAppService::class);
                $whatsappResult = $whatsapp->sendMessage(
                    $user->phone,
                    "رمز تسجيل الدخول الخاص بك في منصة ثمن هو: $otp\nيرجى عدم مشاركته مع أحد."
                );
                if ($whatsappResult) {
                    $whatsappSent = true;
                    Log::info("Expert OTP sent via WhatsApp to: {$user->phone}");
                } else {
                    Log::warning("Expert OTP WhatsApp failed (returned false) for: {$user->phone}");
                }
            } catch (\Exception $e) {
                Log::error('Expert OTP WhatsApp Failed: ' . $e->getMessage());
            }
        }

        // Check if at least one delivery channel succeeded
        if (!$emailSent && !$whatsappSent) {
            return back()->withErrors(['identifier' => 'حدث خطأ أثناء إرسال الرمز، يرجى المحاولة لاحقاً'])->withInput();
        }

        // Construct informative message for the user
        if ($emailSent && $whatsappSent) {
            $successMsg = 'تم إرسال رمز التحقق إلى بريدك الإلكتروني ورقم جوالك على الواتساب.';
        } elseif ($emailSent) {
            $successMsg = 'تم إرسال رمز التحقق إلى بريدك الإلكتروني (' . $user->email . ').';
        } else {
            $successMsg = 'تم إرسال رمز التحقق إلى رقم جوالك على الواتساب.';
        }

        // Store user id in session for the verify step
        session([
            'expert_login_user_id' => $user->id,
            'expert_otp_via'       => ($emailSent && $whatsappSent) ? 'both' : ($emailSent ? 'email' : 'phone')
        ]);

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

        // Login user with remember enabled so they stay logged in
        Auth::login($user, true);

        // Clear session
        $request->session()->forget(['expert_login_user_id', 'expert_otp_via']);

        return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح، مرحباً بك في لوحة التحكم.');
    }

    /**
     * Auto Login for experts via secure signed link.
     */
    public function autoLogin(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('expert.login')->withErrors(['identifier' => 'رابط الدخول غير صالح أو منتهي الصلاحية']);
        }

        if (!$user->hasRole('expert') && !$user->hasRole('admin') && !$user->hasRole('super-admin') && !$user->hasRole('superadmin')) {
            return redirect()->route('expert.login')->withErrors(['identifier' => 'هذا الحساب لا يملك صلاحية الدخول كخبير']);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return redirect()->route('expert.login')->withErrors(['identifier' => 'حسابك غير مفعل بعد، يرجى انتظار موافقة الإدارة']);
        }

        // Persistent login
        Auth::login($user, true);

        $redirectUrl = $request->query('redirect');
        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return redirect()->route('dashboard');
    }
}
