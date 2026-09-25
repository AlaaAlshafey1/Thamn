@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'تهانينا! تم قبولك كخبير في ثمن' : 'Congratulations! You are now an Expert on Thamn';
@endphp
@php $banner = 'expert_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'تم قبولك كخبير في ثمن 🏆' : 'You are now a Thamn Expert! 🏆' }}
  </h1>

  <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.85;">
    @if($isRtl)
      مبروك <strong style="color:#1A1A1A;">{{ $user->first_name ?? '' }}</strong>!<br>
      تم تسجيلك بنجاح كخبير في منصة ثمن.<br><br>
      <strong>بيانات الدخول الخاصة بك:</strong>
    @else
      Congratulations <strong style="color:#1A1A1A;">{{ $user->first_name ?? '' }}</strong>!<br>
      You've been successfully registered as an Expert on Thamn.<br><br>
      <strong>Your login credentials:</strong>
    @endif
  </p>

  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#F9F9F9;border-radius:12px;border:1px solid #E5E5E5;margin-bottom:24px;">
    <tr>
      <td style="padding:16px 20px;">
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          📧 {{ $isRtl ? 'البريد الإلكتروني' : 'Email' }}: <strong style="color:#1A1A1A;">{{ $user->email ?? '' }}</strong>
        </p>
        <p style="margin:0;font-size:13px;color:#555;">
          🔑 {{ $isRtl ? 'كلمة المرور' : 'Password' }}: <strong style="color:#1A1A1A;">{{ $password }}</strong>
        </p>
      </td>
    </tr>
  </table>

  <a href="https://thmmn.net/expert/login"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'تسجيل الدخول الآن' : 'Login Now' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))