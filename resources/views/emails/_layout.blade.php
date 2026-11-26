{{--
Shared email layout partial.
Usage: @include('emails._layout', ['banner'=>'banner_file.jpg', 'title'=>'...', 'slot'=>'...'])
Wrap content in $slot variable before including.
--}}
@php
  $locale = app()->getLocale();
  $isRtl = $isRtl ?? ($locale === 'ar');
  $dir = $isRtl ? 'rtl' : 'ltr';
  $txtAlign = $isRtl ? 'right' : 'left';
  $oppAlign = $isRtl ? 'left' : 'right';

  $base = rtrim(config('app.url', 'https://thmmn.net'), '/');
  if (str_contains($base, 'localhost') || str_contains($base, '127.0.0.1')) {
    $base = 'https://thmmn.net';
  }

  $bannerSrc = $customBanner ?? ($base . '/assets/emails/' . ($banner ?? 'otp_banner.png'));
  $canEmbed = isset($message) && is_object($message) && method_exists($message, 'embed');
  $logoPath = public_path('assets/emails/logo.png');
  $logoSrc  = ($canEmbed && is_file($logoPath))
    ? $message->embed($logoPath)
    : ($base . '/assets/emails/logo.png');
@endphp
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $locale }}" dir="{{ $dir }}">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $emailTitle ?? 'ثمّن' }}</title>
  <!--[if !mso]><!-->
  <style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap');
  </style>
  <!--<![endif]-->
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
      border: 0;
      height: auto;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic
    }

    *,
    body,
    table,
    td,
    p,
    h1,
    h2,
    h3,
    h4,
    h5,
    a,
    span,
    strong {
      font-family: 'IBM Plex Sans Arabic', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Tahoma, Arial, sans-serif !important
    }

    body {
      margin: 0;
      padding: 0;
      background: #F2F4F7;
      font-family: 'IBM Plex Sans Arabic', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Tahoma, Arial, sans-serif
    }

    @media only screen and (max-width:600px) {
      .email-card {
        width: 100% !important;
        border-radius: 0 !important
      }

      .banner-img {
        height: 200px !important
      }

      .body-cell {
        padding: 24px 18px 28px !important
      }

      .footer-cell {
        padding: 20px 18px !important
      }

      .h1 {
        font-size: 18px !important
      }

      .btn-link {
        padding: 12px 24px !important;
        font-size: 14px !important
      }
    }
  </style>
</head>

<body
  style="margin:0;padding:0;background:#F2F4F7;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding:36px 12px;">

        <table class="email-card" role="presentation" width="520" cellpadding="0" cellspacing="0" border="0" style="max-width:520px;width:100%;background:#fff;border-radius:20px;overflow:hidden;
               box-shadow:0 8px 40px rgba(0,0,0,.10);">

          {{-- TOP LOGO --}}
          <tr>
            <td align="center" style="padding:20px 32px 0;border-bottom:1px solid #F0F0F0;">
              <img src="{{ $logoSrc }}" width="56" alt="ثمّن" style="display:block;margin:0 auto 16px;border:0;">
            </td>
          </tr>

          {{-- BANNER --}}
          <tr>
            <td style="font-size:0;line-height:0;padding:0;">
              <img class="banner-img" src="{{ $bannerSrc }}" width="520" height="230" alt=""
                style="display:block;width:100%;height:230px;object-fit:cover;">
            </td>
          </tr>

          {{-- BODY --}}
          <tr>
            <td class="body-cell"
              style="padding:28px 40px 36px;text-align:center;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
              {!! $slot !!}
            </td>
          </tr>

          {{-- FOOTER (logo + address + social icons from the dashboard) --}}
          @include('emails._footer', ['dir' => $dir, 'isRtl' => $isRtl, 'assetBase' => $base])

        </table>
      </td>
    </tr>
  </table>
</body>

</html>