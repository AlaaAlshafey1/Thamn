<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من تسجيلك - Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Reset styles for email clients */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        /* Basic styles */
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f4f4;
            font-family: 'Cairo', 'Inter', sans-serif;
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
        }

        .footer {
            background-color: #ffffff;
            padding: 30px;
            border-top: 1px solid #eeeeee;
        }

        .social-icons {
            margin-bottom: 15px;
        }

        .social-icons a {
            text-decoration: none;
            margin: 0 8px;
        }

        .footer-text {
            color: #999999;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 95% !important;
            }
            .otp-box {
                width: 90% !important;
                font-size: 24px !important;
                letter-spacing: 6px !important;
            }
            .title {
                font-size: 20px !important;
            }
        }
    </style>
</head>
<body style="background-color: #f4f4f4; margin: 0; padding: 0;">
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Top Accent Bar -->
            <div class="top-bar"></div>

            <!-- Logo Section -->
            <div class="header">
                <img src="{{ asset('assets/emails/logo.png') }}" alt="Thamn Logo" width="100">
            </div>

            <!-- Content Section -->
            <div class="content">
                <!-- Hero Image -->
                <img src="{{ asset('assets/emails/otp_banner.png') }}" alt="Verification" class="banner-img">

                <!-- OTP Info -->
                <span class="otp-label">رمز التحقق (OTP) &bull; VERIFICATION CODE</span>
                
                <h1 class="title">
                    التحقق من تسجيلك في منصة ثمن<br>
                    <span style="font-size: 18px; font-weight: 400; color: #555;">Verify your registration in Thamn</span>
                </h1>

                <div class="description">
                    <p style="margin-bottom: 10px;">
                        @if(isset($userName))
                            مرحباً <strong>{{ $userName }}</strong>،<br>
                        @endif
                        تلقينا محاولة تسجيل باستخدام الرمز التالي. يرجى إدخاله في نافذة المتصفح أو التطبيق التي بدأت منها عملية التسجيل.
                    </p>
                    <p style="border-top: 1px solid #f0f0f0; padding-top: 10px; font-style: italic;">
                        We received a registration attempt with the following code. Please enter it in the browser window or app where you started.
                    </p>
                </div>

                <!-- OTP Code -->
                <div class="otp-box">
                    @php
                        $otp_spaced = implode(' ', str_split($otp));
                    @endphp
                    {{ $otp_spaced }}
                </div>
            </div>

            <!-- Footer Section -->
            <div class="footer">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <!-- Left/Right depending on direction - using table for alignment -->
                        <td align="right" style="text-align: right;">
                            <div class="social-icons">
                                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24" alt="Facebook"></a>
                                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="24" alt="YouTube"></a>
                                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="24" alt="Instagram"></a>
                            </div>
                            <div class="footer-text">
                                المملكة العربية السعودية، الجبيل<br>
                                Kingdom of Saudi Arabia, Al Jubail
                            </div>
                        </td>
                        <td align="left" style="text-align: left; vertical-align: middle;">
                            <img src="{{ asset('assets/emails/logo.png') }}" alt="Thamn" width="80" style="opacity: 0.8;">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Legal / Unsubscribe -->
        <div style="text-align: center; padding: 20px; color: #999999; font-size: 12px;">
            &copy; {{ date('Y') }} منصة ثمن (Thamn Platform). جميع الحقوق محفوظة.<br>
            All rights reserved.
        </div>
    </div>
</body>
</html>