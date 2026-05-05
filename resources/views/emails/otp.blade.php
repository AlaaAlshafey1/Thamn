<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من تسجيلك في منصة ثمن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #e9e9e9;
            padding: 50px 0;
            margin: 0;
            direction: rtl;
        }
        .email-container {
            max-width: 520px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .top-bar {
            background-color: #3d3d3d;
            height: 12px;
        }
        .header-logo {
            padding: 30px 0;
            text-align: center;
        }
        .header-logo img {
            width: 85px;
        }
        .hero-section {
            padding: 0 25px;
            text-align: center;
        }
        .banner-img {
            border-radius: 15px;
            width: 100%;
            height: auto;
            max-height: 250px;
            object-fit: cover;
        }
        .content {
            text-align: center;
            padding: 20px;
        }
        .otp-label {
            color: #7c4dff;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 25px;
            display: block;
        }
        .main-title {
            color: #1a232e;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 10px 0;
        }
        .description {
            color: #8a94a0;
            font-size: 0.85rem;
            padding: 0 40px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .otp-box {
            background-color: #f1f3f9;
            color: #1a232e;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 12px;
            padding: 15px 0;
            border-radius: 12px;
            width: 85%;
            margin: 0 auto 40px auto;
            text-align: center;
        }
        .footer {
            padding: 25px 30px;
            border-top: 1px solid #f0f0f0;
            background-color: #ffffff;
        }
        .footer-table {
            width: 100%;
        }
        .social-icons a {
            color: #9ba4ae;
            font-size: 1.4rem;
            margin: 0 8px;
            text-decoration: none;
        }
        .footer-info {
            color: #9ba4ae;
            font-size: 0.8rem;
            margin-top: 5px;
            line-height: 1.5;
            text-align: right;
        }
        .footer-logo {
            text-align: left;
        }
        .footer-logo img {
            width: 75px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <div class="top-bar"></div>

        <div class="header-logo">
            <img src="{{ asset('assets/emails/logo.png') }}" alt="شعار ثمن">
        </div>

        <hr style="margin: 0 25px; border: 0; border-top: 1px solid #eee; opacity: 0.5;">

        <div class="hero-section" style="margin-top: 20px;">
            <img src="{{ asset('assets/emails/otp_banner.png') }}" class="banner-img" alt="كود التحقق">
        </div>

        <div class="content">
            <span class="otp-label">رمز التحقق (OTP)</span>
            <h1 class="main-title">التحقق من تسجيلك في منصة ثمن</h1>
            <p class="description">
                @if(isset($userName))
                    مرحباً {{ $userName }}،<br>
                @endif
                تلقينا محاولة تسجيل باستخدام الرمز التالي. يرجى إدخاله في نافذة المتصفح أو التطبيق التي بدأت منها عملية التسجيل.
            </p>
            
            <div class="otp-box">
                @php
                    $otp_spaced = implode(' ', str_split($otp));
                @endphp
                {{ $otp_spaced }}
            </div>
        </div>

        <div class="footer">
            <table class="footer-table" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                <tr>
                    <td style="text-align: right; vertical-align: middle;">
                        <div class="social-icons" style="margin-bottom: 10px;">
                            <a href="#" style="color: #9ba4ae; text-decoration: none; margin-left: 15px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="18" height="18" alt="FB" style="vertical-align: middle; opacity: 0.6;">
                            </a>
                            <a href="#" style="color: #9ba4ae; text-decoration: none; margin-left: 15px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/733/733590.png" width="18" height="18" alt="YT" style="vertical-align: middle; opacity: 0.6;">
                            </a>
                            <a href="#" style="color: #9ba4ae; text-decoration: none; margin-left: 15px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="18" height="18" alt="IG" style="vertical-align: middle; opacity: 0.6;">
                            </a>
                        </div>
                        <div class="footer-info" style="color: #9ba4ae; font-size: 0.85rem; line-height: 1.5;">
                            المملكة العربية السعودية،<br>
                            الجبيل
                        </div>
                    </td>
                    <td style="text-align: left; vertical-align: middle;">
                        <div class="footer-logo">
                            <img src="{{ asset('assets/emails/logo.png') }}" alt="Logo" style="width: 80px; opacity: 0.8;">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>