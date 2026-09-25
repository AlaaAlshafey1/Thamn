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
    if (str_contains($name, 'facebook'))  $fbUrl = $social['url'] ?? '#';
    if (str_contains($name, 'youtube'))   $ytUrl = $social['url'] ?? '#';
    if (str_contains($name, 'instagram')) $igUrl = $social['url'] ?? '#';
  }
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $locale }}" dir="{{ $dir }}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $isRtl ? 'رمز التحقق — ثمن' : 'Verification Code — Thamn' }}</title>
  <style type="text/css">
    body, table, td, a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
    table, td { mso-table-lspace:0pt; mso-table-rspace:0pt; border-collapse:collapse; }
    img { -ms-interpolation-mode:bicubic; border:0; height:auto; outline:none; text-decoration:none; }
    body { margin:0; padding:0; background-color:#F2F4F7; font-family:'Cairo',Arial,sans-serif; }

    @media only screen and (max-width:640px) {
      .email-card { width:100% !important; border-radius:0 !important; }
      .banner-img { height:200px !important; }
      .body-cell { padding:28px 20px 32px !important; }
      .otp-box { font-size:26px !important; letter-spacing:10px !important; padding:14px 24px !important; }
      .footer-cell { padding:20px 20px !important; }
      .h1 { font-size:20px !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#F2F4F7;">

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F2F4F7">
    <tr>
      <td align="center" style="padding:40px 16px;">

        <!-- CARD -->
        <table class="email-card" role="presentation" width="520" cellpadding="0" cellspacing="0" border="0"
          style="max-width:520px;width:100%;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);">

@php
  $baseUrl = rtrim(config('app.url', 'https://thmmn.net'), '/');
  // For emails, always use the production URL so images load in Gmail
  if (str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1')) {
      $baseUrl = 'https://thmmn.net';
  }
@endphp
          <!-- ===== BANNER ===== -->
          <tr>
            <td style="padding:0;margin:0;font-size:0;line-height:0;">
              <img class="banner-img"
                src="{{ $baseUrl }}/assets/emails/otp_banner.png"
                width="520" height="240"
                alt="OTP"
                style="display:block;width:100%;height:240px;object-fit:cover;border-radius:0;">
            </td>
          </tr>

          <!-- ===== LOGO ===== -->
          <tr>
            <td align="center" style="padding:28px 40px 0;">
              <img src="{{ $baseUrl }}/assets/emails/logo.png"
                   width="64" alt="ثمن"
                   style="display:block;margin:0 auto;border:0;">
            </td>
          </tr>

          <!-- ===== BODY ===== -->
          <tr>
            <td class="body-cell" style="padding:20px 44px 36px;text-align:center;">

              <!-- OTP Label -->
              <p style="margin:0 0 10px;font-size:12px;font-weight:700;color:#C1953E;
                         letter-spacing:3px;text-transform:uppercase;">OTP</p>

              <!-- Title -->
              <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
                {{ $isRtl ? 'التحقق من تسجيلك في ثمن' : 'Verify Your Thamn Account' }}
              </h1>

              <!-- Description -->
              <p style="margin:0 0 28px;font-size:14px;color:#666666;line-height:1.85;text-align:center;">
                @if($isRtl)
                  @if(isset($userName))<strong style="color:#1A1A1A;">{{ $userName }}</strong>، @endif
                  تلقينا محاولة تسجيل باستخدام الرمز التالي. يرجى إدخاله في نافذة المتصفح التي بدأت منها عملية التسجيل في ثمن.
                @else
                  @if(isset($userName))Hi <strong style="color:#1A1A1A;">{{ $userName }}</strong>,<br>@endif
                  We received a registration attempt. Please enter the code below in the browser window where you started the Thamn sign-up process.
                @endif
              </p>

              <!-- OTP Code Box -->
              <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 10px;">
                <tr>
                  <td class="otp-box"
                    style="background:#F5F5F5;border-radius:12px;border:1px solid #E5E5E5;
                           padding:16px 36px;font-size:32px;font-weight:900;color:#1A1A1A;
                           letter-spacing:14px;text-align:center;font-family:'Courier New',monospace;">
                    {{ $otp }}
                  </td>
                </tr>
              </table>

              <!-- Timer note -->
              <p style="margin:0 0 24px;font-size:12px;color:#999999;text-align:center;">
                ⏱&nbsp;{{ $isRtl ? 'الرمز صالح لمدة 5 دقائق' : 'Code expires in 5 minutes' }}
              </p>

              <!-- Warning -->
              <table cellpadding="0" cellspacing="0" border="0" width="100%"
                style="background:#FFF8EC;border-radius:10px;border:1px solid #F5DFA0;">
                <tr>
                  <td style="padding:13px 18px;font-size:12px;color:#7A5B00;text-align:center;line-height:1.7;">
                    ⚠️&nbsp;{{ $isRtl
                      ? 'لا تشارك هذا الرمز مع أي شخص. فريق ثمن لن يطلبه منك أبداً.'
                      : "Never share this code. Thamn's team will never ask for it." }}
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- ===== FOOTER ===== -->
          <tr>
            <td class="footer-cell"
              style="background:#1A1A1A;padding:22px 40px;border-radius:0 0 24px 24px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
                <tr>
                  <!-- Logo + location -->
                  <td align="{{ $textAlign }}" valign="middle">
                    <img src="{{ $baseUrl }}/assets/emails/logo_white.png"
                         width="48" alt="ثمن"
                         style="display:block;border:0;opacity:.85;margin-bottom:6px;">
                    <p style="margin:0;font-size:11px;color:#888888;font-family:Arial,sans-serif;">
                      {{ $isRtl ? 'المملكة العربية السعودية، الجبيل' : 'Saudi Arabia, Al Jubail' }}
                    </p>
                  </td>
                  <!-- Social icons -->
                  <td align="{{ $oppTextAlign }}" valign="middle">
                    <table cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td style="padding:0 5px;">
                          <a href="{{ $fbUrl }}" style="text-decoration:none;">
                            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png"
                                 width="22" style="display:block;filter:brightness(0) invert(1);opacity:.55;border:0;">
                          </a>
                        </td>
                        <td style="padding:0 5px;">
                          <a href="{{ $ytUrl }}" style="text-decoration:none;">
                            <img src="https://cdn-icons-png.flaticon.com/512/733/733590.png"
                                 width="22" style="display:block;filter:brightness(0) invert(1);opacity:.55;border:0;">
                          </a>
                        </td>
                        <td style="padding:0 5px;">
                          <a href="{{ $igUrl }}" style="text-decoration:none;">
                            <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png"
                                 width="22" style="display:block;filter:brightness(0) invert(1);opacity:.55;border:0;">
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
        <!-- /CARD -->

      </td>
    </tr>
  </table>

</body>
</html>