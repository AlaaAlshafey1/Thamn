@php
    $locale = app()->getLocale();
    $isRtl = $locale == 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $textAlign = $isRtl ? 'right' : 'left';
    $oppTextAlign = $isRtl ? 'left' : 'right';

    // Fetch social media from settings
    $contact = \App\Models\Contact::first();
    $socials = $contact ? ($contact->social_media ?? []) : [];
    
    $fbUrl = '#';
    $ytUrl = '#';
    $igUrl = '#';
    
    foreach($socials as $social) {
        $name = strtolower($social['name'] ?? '');
        if(str_contains($name, 'facebook')) $fbUrl = $social['url'] ?? '#';
        if(str_contains($name, 'youtube')) $ytUrl = $social['url'] ?? '#';
        if(str_contains($name, 'instagram')) $igUrl = $social['url'] ?? '#';
    }
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $locale }}" dir="{{ $dir }}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $isRtl ? 'إعادة تعيين كلمة المرور' : 'Reset Your Password' }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style type="text/css">
    body {
        font-family: @if($isRtl) 'Cairo' @else 'Inter' @endif, Arial, sans-serif;
    }
    .otp-char {
        display: inline-block;
        width: 50px;
        height: 60px;
        line-height: 60px;
        background: #ffffff;
        border: 2px solid #ff9800;
        border-radius: 8px;
        margin: 0 5px;
        font-size: 28px;
        font-weight: bold;
        color: #333;
        text-align: center;
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f9f9f9;direction:{{ $dir }};">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f9f9f9">
  <tr>
    <td align="center" style="padding:40px 10px;">

      <!-- ===== WRAPPER 600px ===== -->
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="max-width:600px;width:100%;border-radius:16px;overflow:hidden;box-shadow: 0 10px 30px rgba(0,0,0,0.08);">

        <!-- HEADER IMAGE -->
        <tr>
            <td align="center" bgcolor="#FFF8F0" style="padding:40px 20px;">
                <img src="{{ asset('assets/emails/reset_password.png') }}" width="280" alt="Security" style="display:block;border:0;max-width:100%;">
            </td>
        </tr>

        <!-- CONTENT AREA -->
        <tr>
            <td style="padding:40px 40px 20px;">
                <h1 style="margin:0 0 15px;font-size:24px;font-weight:700;color:#1a1a1a;text-align:center;">
                    {{ $isRtl ? 'طلب إعادة تعيين كلمة المرور' : 'Password Reset Request' }}
                </h1>
                <p style="margin:0;font-size:16px;color:#666666;text-align:center;line-height:1.6;">
                    @if($isRtl)
                        مرحباً <strong>{{ $userName }}</strong>،<br>
                        تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في ثمن. استخدم الرمز التالي لإتمام العملية:
                    @else
                        Hi <strong>{{ $userName }}</strong>,<br>
                        We received a request to reset your password for your Thamn account. Use the following code to complete the process:
                    @endif
                </p>
            </td>
        </tr>

        <!-- OTP BOX -->
        <tr>
          <td style="padding:20px 40px 40px;" align="center">
            <div style="background-color:#FFF8F0;padding:30px;border-radius:12px;display:inline-block;border:1px dashed #ff9800;">
                @php
                    $otpChars = str_split($otp);
                @endphp
                @foreach($otpChars as $char)
                    <span class="otp-char">{{ $char }}</span>
                @endforeach
            </div>
            <p style="margin:20px 0 0;font-size:13px;color:#999;text-align:center;">
                {{ $isRtl ? 'هذا الرمز صالح لمدة 5 دقائق فقط' : 'This code is valid for 5 minutes only' }}
            </p>
          </td>
        </tr>

        <!-- WARNING -->
        <tr>
            <td style="padding:0 40px 30px;">
                <div style="background-color:#fef2f2;border-radius:8px;padding:15px;border:1px solid #fee2e2;">
                    <p style="margin:0;font-size:13px;color:#991b1b;text-align:center;">
                        {{ $isRtl ? 'إذا لم تطلب أنت هذا التغيير، يرجى تجاهل هذا البريد الإلكتروني.' : 'If you did not request this change, please ignore this email.' }}
                    </p>
                </div>
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td bgcolor="#1a232e" style="padding:30px 40px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
              <tr>
                <td align="{{ $textAlign }}">
                  <img src="{{ asset('assets/emails/logo.png') }}" width="80" alt="Logo" style="display:block;border:0;filter: brightness(0) invert(1);">
                  <div style="margin-top:15px;font-size:12px;color:#999;">
                    {{ $isRtl ? 'منصة ثمن - خيارك الأول للتقييم العقاري' : 'Thamn Platform - Your first choice for real estate evaluation' }}
                  </div>
                </td>
                <td align="{{ $oppTextAlign }}" valign="middle">
                    <table cellpadding="0" cellspacing="0" border="0" align="{{ $oppTextAlign }}">
                        <tr>
                          <td style="padding:0 5px;"><a href="{{ $fbUrl }}"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="20" style="filter: brightness(0) invert(1);"></a></td>
                          <td style="padding:0 5px;"><a href="{{ $ytUrl }}"><img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="20" style="filter: brightness(0) invert(1);"></a></td>
                          <td style="padding:0 5px;"><a href="{{ $igUrl }}"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="20" style="filter: brightness(0) invert(1);"></a></td>
                        </tr>
                    </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
      <!-- ===== END WRAPPER ===== -->

    </td>
  </tr>
</table>

</body>
</html>
