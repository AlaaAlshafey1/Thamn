@php
    $locale = app()->getLocale();
    $isRtl = $locale == 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $textAlign = $isRtl ? 'right' : 'left';
    $oppTextAlign = $isRtl ? 'left' : 'right';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isRtl ? 'التحقق من تسجيلك' : 'Verify Your Registration' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f4f4;
            font-family: @if($isRtl) 'Cairo' @else 'Inter' @endif, sans-serif;
            direction: {{ $dir }};
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .top-bar {
            background-color: #333333;
            height: 8px;
            width: 100%;
        }

        .header {
            padding: 30px 20px;
            text-align: center;
        }

        .content {
            padding: 0 30px 30px;
            text-align: center;
        }

        .banner-img {
            width: 100%;
            max-width: 540px;
            height: auto;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .otp-label {
            color: #7c4dff;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .title {
            color: #1a232e;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 15px;
            line-height: 1.3;
        }

        .description {
            color: #666666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
            text-align: {{ $textAlign }};
        }

        .otp-box {
            background-color: #f8f9fc;
            border-radius: 12px;
            padding: 20px;
            margin: 0 auto 30px;
            width: 80%;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 10px;
            color: #1a232e;
            border: 1px solid #edf2f7;
            text-align: center;
        }

        .footer {
            background-color: #ffffff;
            padding: 30px;
            border-top: 1px solid #eeeeee;
        }

        .footer-text {
            color: #999999;
            font-size: 12px;
            line-height: 1.5;
            text-align: {{ $textAlign }};
        }

        @media screen and (max-width: 600px) {
            .email-container { width: 95% !important; }
            .otp-box { width: 90% !important; font-size: 24px !important; letter-spacing: 6px !important; }
            .title { font-size: 20px !important; }
        }
    </style>
</head>
<body style="background-color: #f4f4f4; margin: 0; padding: 0;">
    <div class="email-wrapper">
        <div class="email-container">
            <div class="top-bar"></div>

            <div class="header">
                <img src="{{ asset('assets/emails/logo.png') }}" alt="Thamn Logo" width="100">
            </div>

            <div class="content">
                <img src="{{ asset('assets/emails/otp_banner.png') }}" alt="Verification" class="banner-img">

                <span class="otp-label">
                    {{ $isRtl ? 'رمز التحقق (OTP)' : 'Verification Code (OTP)' }}
                </span>
                
                <h1 class="title">
                    {{ $isRtl ? 'التحقق من تسجيلك في منصة ثمن' : 'Verify your registration in Thamn' }}
                </h1>

                <div class="description">
                    <p>
                        @if($isRtl)
                            @if(isset($userName)) مرحباً <strong>{{ $userName }}</strong>،<br> @endif
                            تلقينا محاولة تسجيل باستخدام الرمز التالي. يرجى إدخاله في نافذة المتصفح أو التطبيق التي بدأت منها عملية التسجيل.
                        @else
                            @if(isset($userName)) Hi <strong>{{ $userName }}</strong>,<br> @endif
                            We received a registration attempt with the following code. Please enter it in the browser window or app where you started.
                        @endif
                    </p>
                </div>

                <div class="otp-box">
                    @php
                        $otp_spaced = implode(' ', str_split($otp));
                    @endphp
                    {{ $otp_spaced }}
                </div>
            </div>

            <div class="footer">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                    <tr>
                        <td align="{{ $textAlign }}" style="text-align: {{ $textAlign }}; vertical-align: middle;">
                            <div style="margin-bottom: 8px;">
                                <a href="#" style="text-decoration: none; @if($isRtl) margin-left: 10px; @else margin-right: 10px; @endif"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="20" alt="FB"></a>
                                <a href="#" style="text-decoration: none; @if($isRtl) margin-left: 10px; @else margin-right: 10px; @endif"><img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="20" alt="YT"></a>
                                <a href="#" style="text-decoration: none;"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="20" alt="IG"></a>
                            </div>
                            <div class="footer-text">
                                {{ $isRtl ? 'المملكة العربية السعودية، الجبيل' : 'Kingdom of Saudi Arabia, Al Jubail' }}
                            </div>
                        </td>
                        <td align="{{ $oppTextAlign }}" style="text-align: {{ $oppTextAlign }}; vertical-align: middle;">
                            <img src="{{ asset('assets/emails/logo.png') }}" alt="Thamn" width="75" style="opacity: 0.8;">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div style="text-align: center; padding: 20px; color: #999999; font-size: 12px;">
            &copy; {{ date('Y') }} {{ $isRtl ? 'منصة ثمن. جميع الحقوق محفوظة.' : 'Thamn Platform. All rights reserved.' }}
        </div>
    </div>
</body>
</html>