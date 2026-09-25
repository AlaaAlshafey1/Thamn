{{--
  Shared email layout partial.
  Usage: @include('emails._layout', ['banner'=>'banner_file.jpg', 'title'=>'...', 'slot'=>'...'])
  Wrap content in $slot variable before including.
--}}
@php
  $locale   = app()->getLocale();
  $isRtl    = $locale === 'ar';
  $dir      = $isRtl ? 'rtl' : 'ltr';
  $txtAlign = $isRtl ? 'right' : 'left';
  $oppAlign = $isRtl ? 'left'  : 'right';

  $contact = \App\Models\Contact::first();
  $socials  = $contact ? ($contact->social_media ?? []) : [];
  $fbUrl = $ytUrl = $igUrl = '#';
  foreach ($socials as $s) {
    $n = strtolower($s['name'] ?? '');
    if (str_contains($n, 'facebook'))  $fbUrl = $s['url'] ?? '#';
    if (str_contains($n, 'youtube'))   $ytUrl = $s['url'] ?? '#';
    if (str_contains($n, 'instagram')) $igUrl = $s['url'] ?? '#';
  }

  $base = rtrim(config('app.url','https://thmmn.net'), '/');
  if (str_contains($base,'localhost') || str_contains($base,'127.0.0.1')) {
    $base = 'https://thmmn.net';
  }

  $bannerSrc = $customBanner ?? ($base . '/assets/emails/' . ($banner ?? 'otp_banner.png'));
  $logoSrc   = $base . '/assets/emails/logo.png';
  $logoWht   = $base . '/assets/emails/logo_white.png';
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $locale }}" dir="{{ $dir }}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $emailTitle ?? 'ثمن' }}</title>
  <style>
    body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
    table,td{mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse}
    img{border:0;height:auto;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic}
    body{margin:0;padding:0;background:#F2F4F7;font-family:'Cairo',Arial,sans-serif}
    @media only screen and (max-width:600px){
      .email-card{width:100% !important;border-radius:0 !important}
      .banner-img{height:200px !important}
      .body-cell{padding:24px 18px 28px !important}
      .footer-cell{padding:20px 18px !important}
      .h1{font-size:18px !important}
      .btn-link{padding:12px 24px !important;font-size:14px !important}
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#F2F4F7;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="center" style="padding:36px 12px;">

      <table class="email-card" role="presentation" width="520" cellpadding="0" cellspacing="0" border="0"
        style="max-width:520px;width:100%;background:#fff;border-radius:20px;overflow:hidden;
               box-shadow:0 8px 40px rgba(0,0,0,.10);">

        {{-- TOP LOGO --}}
        <tr>
          <td align="center" style="padding:20px 32px 0;border-bottom:1px solid #F0F0F0;">
            <img src="{{ $logoSrc }}" width="56" alt="ثمن"
                 style="display:block;margin:0 auto 16px;border:0;">
          </td>
        </tr>

        {{-- BANNER --}}
        <tr>
          <td style="font-size:0;line-height:0;padding:0;">
            <img class="banner-img" src="{{ $bannerSrc }}" width="520" height="230"
                 alt="" style="display:block;width:100%;height:230px;object-fit:cover;">
          </td>
        </tr>

        {{-- BODY --}}
        <tr>
          <td class="body-cell" style="padding:28px 40px 36px;text-align:center;">
            {!! $slot !!}
          </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
          <td class="footer-cell"
            style="background:#1A1A1A;padding:20px 36px;border-radius:0 0 20px 20px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="direction:{{ $dir }};">
              <tr>
                <td align="{{ $txtAlign }}" valign="middle">
                  <img src="{{ $logoWht }}" width="44" alt="ثمن"
                       style="display:block;border:0;opacity:.85;margin-bottom:5px;">
                  <p style="margin:0;font-size:11px;color:#888;font-family:Arial,sans-serif;">
                    {{ $isRtl ? 'المملكة العربية السعودية، الجبيل' : 'Saudi Arabia, Al Jubail' }}
                  </p>
                </td>
                <td align="{{ $oppAlign }}" valign="middle">
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="padding:0 5px;">
                        <a href="{{ $fbUrl }}" style="text-decoration:none;">
                          <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="20"
                               style="display:block;filter:brightness(0) invert(1);opacity:.5;border:0;">
                        </a>
                      </td>
                      <td style="padding:0 5px;">
                        <a href="{{ $ytUrl }}" style="text-decoration:none;">
                          <img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="20"
                               style="display:block;filter:brightness(0) invert(1);opacity:.5;border:0;">
                        </a>
                      </td>
                      <td style="padding:0 5px;">
                        <a href="{{ $igUrl }}" style="text-decoration:none;">
                          <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="20"
                               style="display:block;filter:brightness(0) invert(1);opacity:.5;border:0;">
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
    </td>
  </tr>
</table>
</body>
</html>
