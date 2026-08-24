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
  <title>{{ $isRtl ? 'إعادة تعيين كلمة المرور – ثمن' : 'Reset Your Password – Thamn' }}</title>
  <style type="text/css">
    @font-face {
      font-family: 'Avenir Arabic';
      src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Light1.otf") }}') format('opentype');
      font-weight: 300;
    }

    @font-face {
      font-family: 'Avenir Arabic';
      src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Book.otf") }}') format('opentype');
      font-weight: 400;
    }

    @font-face {
      font-family: 'Avenir Arabic';
      src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Medium.otf") }}') format('opentype');
      font-weight: 500;
    }

    @font-face {
      font-family: 'Avenir Arabic';
      src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Heavy.otf") }}') format('opentype');
      font-weight: 700;
    }

    @font-face {
      font-family: 'Avenir Arabic';
      src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Black.otf") }}') format('opentype');
      font-weight: 900;
    }
  </style>
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
      background-color: #fff7ed;
      font-family: 'Avenir Arabic', Arial, sans-serif;
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

<body style="margin:0;padding:0;background-color:#fff7ed;">

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff7ed">
    <tr>
      <td class="wrap" align="center" style="padding:36px 16px;">

        <table role="presentation" class="card" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;
                    box-shadow:0 16px 50px rgba(234,88,12,.12);">

          <!-- HEADER -->
          <tr>
            <td class="hdr" align="center"
              style="background:linear-gradient(135deg,#c2410c 0%,#ea580c 55%,#fb923c 100%);padding:38px 28px 34px;">
              <img class="ftr-logo" src="{{ asset('assets/emails/logo_white.png') }}" width="78" alt="ثمن"
                style="display:block;margin:0 auto 20px;border:0;">
              <table cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                  <td style="width:70px;height:70px;background:rgba(255,255,255,.18);border-radius:50%;
                           text-align:center;vertical-align:middle;font-size:30px;line-height:70px;">🔑</td>
                </tr>
              </table>
              <p style="margin:16px 0 0;font-size:11px;font-weight:600;color:rgba(255,255,255,.85);
                      letter-spacing:2px;text-transform:uppercase;">
                {{ $isRtl ? 'إعادة تعيين كلمة المرور' : 'Password Reset' }}
              </p>
            </td>
          </tr>

          <!-- BRIDGE -->
          <tr>
            <td height="18" style="background:linear-gradient(180deg,#ea580c 0%,#ea580c 49%,#ffffff 50%);
                     font-size:0;line-height:0;">&nbsp;</td>
          </tr>

          <!-- BODY -->
          <tr>
            <td class="body-pad" style="padding:10px 44px 36px;text-align:center;">

              <h1 class="h1" style="margin:0 0 12px;font-size:21px;font-weight:700;color:#1a1a1a;
                                   line-height:1.4;text-align:center;">
                {{ $isRtl ? 'طلب إعادة تعيين كلمة المرور' : 'Password Reset Request' }}
              </h1>

              <p class="desc" style="margin:0 0 30px;font-size:14px;color:#64748b;line-height:1.85;
                                    text-align:center;word-break:break-word;">
                @if($isRtl)
                  مرحباً <strong style="color:#c2410c;">{{ $userName }}</strong>،<br>
                  تلقينا طلباً لإعادة تعيين كلمة المرور بحسابك في ثمن.<br>استخدم الرمز أدناه لإتمام العملية.
                @else
                  Hi <strong style="color:#c2410c;">{{ $userName }}</strong>,<br>
                  We received a request to reset your Thamn account password.<br>Use the code below to complete the
                  process.
                @endif
              </p>

              <!-- OTP -->
              <table cellpadding="0" cellspacing="0" border="0" align="center"
                style="margin:0 auto 8px;background:#fff7ed;border-radius:16px;border:1px dashed #fb923c;padding:18px 22px;">
                <tr>
                  @php $otpChars = str_split($otp); @endphp
                  @foreach($otpChars as $char)
                    <td class="otp-td" style="width:54px;height:62px;background:#ffffff;border:2px solid #ea580c;
                                   border-radius:12px;font-size:28px;font-weight:800;color:#c2410c;
                                   text-align:center;vertical-align:middle;padding:0 6px;">{{ $char }}</td>
                    @if(!$loop->last)
                    <td class="otp-gap" width="10">&nbsp;</td>@endif
                  @endforeach
                </tr>
              </table>

              <p style="margin:0 0 26px;font-size:12px;color:#94a3b8;text-align:center;">
                ⏱&nbsp;{{ $isRtl ? 'هذا الرمز صالح لمدة 5 دقائق فقط' : 'This code is valid for 5 minutes only' }}
              </p>

              <table cellpadding="0" cellspacing="0" border="0" width="100%"
                style="background:#fef2f2;border-radius:10px;border:1px solid #fecaca;">
                <tr>
                  <td style="padding:14px 18px;font-size:12px;color:#991b1b;text-align:center;line-height:1.7;">
                    🛡&nbsp;{{ $isRtl
  ? 'إذا لم تطلب أنت هذا التغيير، يرجى تجاهل هذا البريد الإلكتروني.'
  : 'If you did not request this change, please ignore this email.' }}
                  </td>
                </tr>
              </table>

              <!-- APP STORE BUTTONS -->
              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:24px;">
                <tr>
                  <td style="padding:0 0 10px;font-size:13px;font-weight:700;color:#c2410c;text-align:center;">📱
                    {{ $isRtl ? 'حمّل التطبيق الآن' : 'Download the App now' }}</td>
                </tr>
                <tr>
                  <td align="center">
                    <table cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td style="padding:0 6px;">
                          <a href="https://apps.apple.com/eg/app/thmmn-%D8%AB%D9%85%D9%86/id6758052199"
                            style="display:inline-block;text-decoration:none;">
                            <table cellpadding="0" cellspacing="0" border="0"
                              style="background:#000;border-radius:10px;">
                              <tr>
                                <td style="padding:9px 14px;">
                                  <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                      <td style="padding-left:8px;vertical-align:middle;"><svg width="18" height="22"
                                          viewBox="0 0 814 1000" xmlns="http://www.w3.org/2000/svg">
                                          <path fill="#fff"
                                            d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663 0 541.8c0-207.8 135.4-317.7 268.5-317.7 99.8 0 184 65.6 245.7 65.6 57.4 0 147.6-69.7 261.3-69.7 4.9 0 2.6-.3 12.6.3zm-166.4-207.1c51.4-61.4 88.1-147.7 88.1-234 0-12.2-.6-24.5-2.6-35.4-84.4 3.2-183.1 56.6-242.6 126.1-41.1 46.4-81.1 132.7-81.1 220.1 0 13.6 2.6 27.2 3.9 31.5 5.2.6 13.6 1.9 21.9 1.9 76 0 166.3-50.8 212.4-110.2z" />
                                        </svg></td>
                                      <td style="padding-right:8px;vertical-align:middle;">
                                        <p
                                          style="margin:0;font-size:8px;color:rgba(255,255,255,.8);font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                          Download on the</p>
                                        <p
                                          style="margin:0;font-size:14px;color:#fff;font-weight:bold;font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                          App Store</p>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </a>
                        </td>
                        <td style="padding:0 6px;">
                          <a href="https://play.google.com/store/apps/details?id=com.thamin.thamin&hl=ar"
                            style="display:inline-block;text-decoration:none;">
                            <table cellpadding="0" cellspacing="0" border="0"
                              style="background:#000;border-radius:10px;">
                              <tr>
                                <td style="padding:9px 14px;">
                                  <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                      <td style="padding-left:8px;vertical-align:middle;"><svg width="18" height="20"
                                          viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                          <path fill="#4CAF50" d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1z" />
                                          <path fill="#FF5722"
                                            d="M47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l2.7 1.5 247.2-247v-5.8L47 0z" />
                                          <path fill="#FFC107"
                                            d="M330.1 281.1l-82.1-82.2V194l82.1 82.2 48.1-27.3 50 28.4-48.1 27.4-50-23.6z" />
                                          <path fill="#4CAF50" d="M330.1 234.3l50 28.5L104.6 499l225.5-264.7z" />
                                          <path fill="#FF5722" d="M379.6 261.3l-49.5-27.4-225.5 265 275-237.6z" />
                                        </svg></td>
                                      <td style="padding-right:8px;vertical-align:middle;">
                                        <p
                                          style="margin:0;font-size:8px;color:rgba(255,255,255,.8);font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                          GET IT ON</p>
                                        <p
                                          style="margin:0;font-size:14px;color:#fff;font-weight:bold;font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                          Google Play</p>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td class="ftr" style="background:#1a232e;padding:24px 44px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
                <tr>
                  <td align="{{ $textAlign }}" valign="middle">
                    <img class="ftr-logo" src="{{ asset('assets/emails/logo_white.png') }}" width="56" alt="ثمن"
                      style="display:block;border:0;opacity:.9;">
                    <p style="margin:7px 0 0;font-size:11px;color:#ea580c;">
                      {{ $isRtl ? 'تطبيق ثمن – خيارك الأول للتقييم العقاري' : 'Thamn Platform – Real Estate Evaluation' }}
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