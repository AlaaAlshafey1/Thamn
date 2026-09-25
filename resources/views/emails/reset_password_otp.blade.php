@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'إعادة تعيين كلمة المرور – ثمن' : 'Reset Password – Thamn';
@endphp
@php $banner = 'reset_password.png'; @endphp
@php ob_start(); @endphp

  {{-- OTP Label --}}
  <p style="margin:0 0 8px;font-size:12px;font-weight:700;color:#C1953E;letter-spacing:3px;text-transform:uppercase;">OTP</p>

  {{-- Title --}}
  <h1 class="h1" style="margin:0 0 12px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'طلبت إعادة تعيين كلمة المرور' : 'Password Reset Request' }}
  </h1>

  {{-- Description --}}
  <p style="margin:0 0 28px;font-size:14px;color:#666;line-height:1.85;">
    @if($isRtl)
      @if(isset($userName))<strong style="color:#1A1A1A;">{{ $userName }}</strong>، @endif
      تلقينا طلباً لإعادة تعيين كلمة المرور. أدخل الرمز التالي لإتمام العملية.
    @else
      @if(isset($userName))Hi <strong style="color:#1A1A1A;">{{ $userName }}</strong>,<br>@endif
      We received a password reset request. Enter the code below to proceed.
    @endif
  </p>

  {{-- OTP Box --}}
  <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 10px;">
    <tr>
      <td style="background:#F5F5F5;border-radius:12px;border:1px solid #E5E5E5;
                 padding:16px 36px;font-size:32px;font-weight:900;color:#1A1A1A;
                 letter-spacing:14px;font-family:'Courier New',monospace;">
        {{ $otp }}
      </td>
    </tr>
  </table>

  <p style="margin:0 0 24px;font-size:12px;color:#999;">
    ⏱&nbsp;{{ $isRtl ? 'الرمز صالح لمدة 5 دقائق' : 'Code expires in 5 minutes' }}
  </p>

  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#FFF8EC;border-radius:10px;border:1px solid #F5DFA0;">
    <tr>
      <td style="padding:13px 18px;font-size:12px;color:#7A5B00;text-align:center;line-height:1.7;">
        ⚠️&nbsp;{{ $isRtl ? 'لا تشارك هذا الرمز. فريق ثمن لن يطلبه منك أبداً.' : "Never share this code. Thamn's team will never ask for it." }}
      </td>
    </tr>
  </table>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))