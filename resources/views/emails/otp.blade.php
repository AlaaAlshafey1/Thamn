@php
  $locale = app()->getLocale();
  $isRtl = $locale == 'ar';
  $dir = $isRtl ? 'rtl' : 'ltr';
  $textAlign = $isRtl ? 'right' : 'left';
  $oppTextAlign = $isRtl ? 'left' : 'right';
  $contact = \App\Models\Contact::first();
  $socials = $contact ? ($contact->social_media ?? []) : [];
  $fbUrl = $ytUrl = $igUrl = '#';
  foreach ($socials as $social) {
    $name = strtolower($social['name'] ?? '');
    if (str_contains($name, 'facebook'))
      $fbUrl = $social['url'] ?? '#';
    if (str_contains($name, 'youtube'))
      $ytUrl = $social['url'] ?? '#';
    if (str_contains($name, 'instagram'))
      $igUrl = $social['url'] ?? '#';
  }
@endphp
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $locale }}" dir="{{ $dir }}">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $isRtl ? 'التحقق من تسجيلك في ثمن' : 'Verify Your Registration – Thamn' }}</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <style type="text/css">
    body,
    table,
    td,
    a {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
      border-collapse: collapse
    }

    img {
      -ms-interpolation-mode: bicubic;
      border: 0;
      height: auto;
      outline: none;
      text-decoration: none
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #eef0f6;
      font-family: Arial, sans-serif;
    }

    @media only screen and (max-width:640px) {
      .wrap {
        padding: 16px 8px !important
      }

      .card {
        border-radius: 14px !important
      }

      .hdr {
        padding: 28px 16px !important
      }

      .body-pad {
        padding: 24px 16px 28px !important
      }

      .h1 {
        font-size: 18px !important;
        line-height: 1.35 !important
      }

      .desc {
        font-size: 13px !important
      }

      .otp-td {
        width: 40px !important;
        height: 48px !important;
        font-size: 20px !important;
        border-radius: 8px !important;
        padding: 0 2px !important
      }

      .otp-gap {
        width: 5px !important
      }

      .ftr {
        padding: 20px 16px !important
      }

      .ftr-logo {
        width: 46px !important
      }
    }
  </style>
</head>

<body style="margin:0;padding:0;background-color:#eef0f6;">

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef0f6">
    <tr>
      <td class="wrap" align="center" style="padding:36px 16px;">

        <table role="presentation" class="card" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;
                    box-shadow:0 16px 50px rgba(76,29,149,.14);">

          <!-- HEADER -->
          <tr>
            <td class="hdr" align="center"
              style="background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 55%,#8b5cf6 100%);padding:38px 28px 34px;">
              <img class="ftr-logo" src="{{ asset('assets/emails/logo_white.png') }}" width="78" alt="ثمن"
                style="display:block;margin:0 auto 20px;border:0;">
              <table cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                  <td style="width:70px;height:70px;background:rgba(255,255,255,.18);border-radius:50%;
                           text-align:center;vertical-align:middle;font-size:30px;line-height:70px;">🔐</td>
                </tr>
              </table>
              <p style="margin:16px 0 0;font-size:11px;font-weight:600;color:rgba(255,255,255,.85);
                      letter-spacing:2px;text-transform:uppercase;">
                {{ $isRtl ? 'رمز التحقق — OTP' : 'Verification Code — OTP' }}
              </p>
            </td>
          </tr>

          <!-- BRIDGE -->
          <tr>
            <td height="18" style="background:linear-gradient(180deg,#6d28d9 0%,#6d28d9 49%,#ffffff 50%);
                     font-size:0;line-height:0;">&nbsp;</td>
          </tr>

          <!-- BODY -->
          <tr>
            <td class="body-pad" style="padding:10px 44px 36px;text-align:center;">

              <h1 class="h1" style="margin:0 0 12px;font-size:21px;font-weight:700;color:#1e1b4b;
                                   line-height:1.4;text-align:center;">
                {{ $isRtl ? 'التحقق من تسجيلك في تطبيق ثمن' : 'Verify Your Registration on Thamn' }}
              </h1>

              <p class="desc" style="margin:0 0 30px;font-size:14px;color:#64748b;line-height:1.85;
                                    text-align:center;word-break:break-word;">
                @if($isRtl)
                  @if(isset($userName)) مرحباً <strong style="color:#3730a3;">{{ $userName }}</strong>،<br>@endif
                  تلقينا محاولة تسجيل باستخدام الرمز أدناه.<br>أدخله في التطبيق لإتمام عملية التسجيل.
                @else
                  @if(isset($userName)) Hi <strong style="color:#3730a3;">{{ $userName }}</strong>,<br>@endif
                  We received a registration attempt using the code below.<br>Enter it in the app to complete your
                  registration.
                @endif
              </p>

              <!-- OTP -->
              <table cellpadding="0" cellspacing="0" border="0" align="center"
                style="margin:0 auto 8px;background:#f5f3ff;border-radius:16px;border:1px solid #ddd6fe;padding:18px 22px;">
                <tr>
                  @php $digits = str_split($otp); @endphp
                  @foreach($digits as $d)
                    <td class="otp-td" style="width:54px;height:62px;background:#ffffff;border:2px solid #7c3aed;
                               border-radius:12px;font-size:28px;font-weight:800;color:#4c1d95;
                               text-align:center;vertical-align:middle;padding:0 6px;">{{ $d }}</td>
                    @if(!$loop->last)
                    <td class="otp-gap" width="10">&nbsp;</td>@endif
                  @endforeach
                </tr>
              </table>

              <p style="margin:0 0 26px;font-size:12px;color:#94a3b8;text-align:center;">
                ⏱&nbsp;{{ $isRtl ? 'صالح لمدة 5 دقائق فقط' : 'Valid for 5 minutes only' }}
              </p>

              <table cellpadding="0" cellspacing="0" border="0" width="100%"
                style="background:#fef9ee;border-radius:10px;border:1px solid #fde68a;">
                <tr>
                  <td style="padding:14px 18px;font-size:12px;color:#78350f;text-align:center;line-height:1.7;">
                    ⚠️&nbsp;{{ $isRtl
  ? 'لا تشارك هذا الرمز مع أي شخص. فريق ثمن لن يطلبه منك أبداً.'
  : "Never share this code. Thamn team will never ask for it." }}
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td class="ftr" style="background:#1e1b4b;padding:24px 44px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
                <tr>
                  <td align="{{ $textAlign }}" valign="middle">
                    <img class="ftr-logo" src="{{ asset('assets/emails/logo_white.png') }}" width="56" alt="ثمن"
                      style="display:block;border:0;opacity:.9;">
                    <p style="margin:7px 0 0;font-size:11px;color:#6366f1;">
                      {{ $isRtl ? 'المملكة العربية السعودية، الجبيل' : 'Saudi Arabia, Al Jubail' }}
                    </p>
                  </td>
                  <td align="{{ $oppTextAlign }}" valign="middle">
                    <table cellpadding="0" cellspacing="0" border="0" align="{{ $oppTextAlign }}">
                      <tr>
                        <td style="padding:0 5px;"><a href="{{ $fbUrl }}" style="text-decoration:none;"><img
                              src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="20"
                              style="display:block;filter:brightness(0) invert(1);opacity:.6;border:0;"></a></td>
                        <td style="padding:0 5px;"><a href="{{ $ytUrl }}" style="text-decoration:none;"><img
                              src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="20"
                              style="display:block;filter:brightness(0) invert(1);opacity:.6;border:0;"></a></td>
                        <td style="padding:0 5px;"><a href="{{ $igUrl }}" style="text-decoration:none;"><img
                              src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="20"
                              style="display:block;filter:brightness(0) invert(1);opacity:.6;border:0;"></a></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>

</html>