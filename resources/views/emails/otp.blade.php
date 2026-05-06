@php
    $locale = app()->getLocale();
    $isRtl = $locale == 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $textAlign = $isRtl ? 'right' : 'left';
    $oppTextAlign = $isRtl ? 'left' : 'right';
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $locale }}" dir="{{ $dir }}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $isRtl ? 'التحقق من تسجيلك' : 'Verify Your Registration' }}</title>
  <!--[if mso]>
  <style type="text/css">
    table { border-collapse:collapse; }
  </style>
  <![endif]-->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style type="text/css">
    body {
        font-family: @if($isRtl) 'Cairo' @else 'Inter' @endif, Arial, sans-serif;
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;direction:{{ $dir }};">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f4f4f4">
  <tr>
    <td align="center" style="padding:20px 10px;">

      <!-- ===== WRAPPER 600px ===== -->
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="max-width:600px;width:100%;border-radius:12px;overflow:hidden;box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

        <!-- TOP BAR -->
        <tr><td bgcolor="#3D3D3D" height="10" style="font-size:0;line-height:0;">&nbsp;</td></tr>

        <!-- LOGO -->
        <tr>
            <td align="center" style="padding:26px 30px 20px;">
                <img src="{{ asset('assets/emails/logo.png') }}" width="100" alt="ثمن" style="display:block;border:0;max-width:100%;">
            </td>
        </tr>

        <!-- DIVIDER -->
        <tr>
            <td style="padding:0 30px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr><td bgcolor="#E8E8E8" height="1" style="font-size:0;line-height:0;">&nbsp;</td></tr>
                </table>
            </td>
        </tr>

        <!-- HERO IMAGE -->
        <tr>
            <td align="center" style="padding:24px 20px 20px;">
                <img src="{{ asset('assets/emails/otp_banner.png') }}" width="540" alt="OTP" style="display:block;border:0;max-width:100%;border-radius:12px;">
            </td>
        </tr>

        <!-- OTP LABEL -->
        <tr>
            <td align="center" style="padding:0 30px 6px;font-size:13px;color:#7c4dff;font-weight:600;text-transform:uppercase;">
                {{ $isRtl ? 'رمز التحقق (OTP)' : 'Verification Code (OTP)' }}
            </td>
        </tr>
        
        <!-- MAIN TITLE -->
        <tr>
            <td align="center" style="padding:0 24px 10px;font-size:24px;font-weight:700;color:#1a1a1a;text-align:center;line-height:1.45;">
                {{ $isRtl ? 'التحقق من تسجيلك في منصة ثمن' : 'Verify your registration in Thamn' }}
            </td>
        </tr>
        
        <!-- DESCRIPTION -->
        <tr>
            <td align="center" style="padding:0 40px 18px;font-size:14px;color:#666666;text-align:center;line-height:1.7;">
                @if($isRtl)
                    @if(isset($userName)) مرحباً <strong>{{ $userName }}</strong>،<br> @endif
                    تلقينا محاولة تسجيل باستخدام الرمز التالي. يرجى إدخاله في نافذة المتصفح أو التطبيق التي بدأت منها عملية التسجيل.
                @else
                    @if(isset($userName)) Hi <strong>{{ $userName }}</strong>,<br> @endif
                    We received a registration attempt with the following code. Please enter it in the browser window or app where you started.
                @endif
            </td>
        </tr>

        <!-- OTP BOX (instead of CTA button for security/clarity) -->
        <tr>
          <td style="padding:0 24px 36px;" align="center">
            <table cellpadding="0" cellspacing="0" border="0" style="width:80%;background-color:#f1f3f9;border-radius:12px;border:1px solid #e1e4e8;">
              <tr>
                <td align="center" style="padding:20px;font-size:32px;font-weight:700;color:#1a232e;letter-spacing:10px;">
                    @php
                        $otp_spaced = implode(' ', str_split($otp));
                    @endphp
                    {{ $otp_spaced }}
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- FOOTER DIVIDER -->
        <tr>
            <td style="padding:0 30px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr><td bgcolor="#E8E8E8" height="1" style="font-size:0;line-height:0;">&nbsp;</td></tr>
                </table>
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="padding:18px 30px 22px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
              <tr>
                <td align="{{ $textAlign }}">
                  <!-- Social Icons -->
                  <table cellpadding="0" cellspacing="0" border="0" align="{{ $textAlign }}">
                    <tr>
                      <td width="30" height="30" bgcolor="#999999" align="center" style="border-radius:50%;">
                        <a href="#" style="color:#ffffff;text-decoration:none;display:block;"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="16" style="vertical-align:middle;filter:invert(1);"></a>
                      </td>
                      <td width="8">&nbsp;</td>
                      <td width="30" height="30" bgcolor="#999999" align="center" style="border-radius:50%;">
                        <a href="#" style="color:#ffffff;text-decoration:none;display:block;"><img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="16" style="vertical-align:middle;filter:invert(1);"></a>
                      </td>
                      <td width="8">&nbsp;</td>
                      <td width="30" height="30" bgcolor="#999999" align="center" style="border-radius:50%;">
                        <a href="#" style="color:#ffffff;text-decoration:none;display:block;"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="16" style="vertical-align:middle;filter:invert(1);"></a>
                      </td>
                    </tr>
                  </table>
                  <div style="margin-top:10px;font-size:11px;color:#aaaaaa;">
                    {{ $isRtl ? 'المملكة العربية السعودية، الجبيل' : 'Kingdom of Saudi Arabia, Al Jubail' }}
                  </div>
                </td>
                <td align="{{ $oppTextAlign }}" valign="bottom" width="80">
                  <img src="{{ asset('assets/emails/logo.png') }}" width="65" alt="Logo" style="display:block;border:0;opacity:0.8;">
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