<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول الخبراء - ثمن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #fcf9f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Abstract Background Elements */
        .bg-shape {
            position: absolute;
            z-index: 1;
        }

        .bg-bars {
            bottom: -50px;
            left: -50px;
            width: 400px;
            opacity: 0.9;
        }

        .bg-circle-top {
            top: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: #fff0e6;
        }

        .bg-circle-bottom {
            bottom: -150px;
            right: -100px;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            border: 60px solid #fff0e6;
        }

        .logo-container {
            position: absolute;
            top: 40px;
            right: 50px;
            z-index: 10;
        }

        .logo-container img {
            height: 50px;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 460px;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            position: relative;
            z-index: 10;
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header h1 {
            font-size: 26px;
            color: #ef7021;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #777;
            font-size: 15px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #333;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            width: 100%;
            height: 52px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 0 15px 0 45px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s;
            direction: ltr;
            text-align: right;
        }

        .input-group input:focus {
            border-color: #ef7021;
        }

        .input-group svg {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #aaa;
        }

        /* Adjust input padding for OTP to be centered */
        .input-otp input {
            text-align: center;
            padding: 0;
            letter-spacing: 15px;
            font-size: 24px;
            font-weight: 700;
        }

        .btn-submit {
            width: 100%;
            height: 52px;
            background: #ef7021;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #d86219;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }

        .alert-danger {
            background: #ffebe6;
            color: #d93025;
            border: 1px solid #f6c8c4;
        }

        .alert-success {
            background: #e6f4ea;
            color: #137333;
            border: 1px solid #ceead6;
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
        }

        .footer-links a {
            display: block;
            color: #ef7021;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .footer-links p {
            color: #888;
            font-size: 13px;
        }

        .footer-links p a {
            display: inline;
            color: #ef7021;
        }

        @media (max-width: 768px) {
            .bg-bars, .bg-circle-top, .bg-circle-bottom {
                display: none;
            }
            .logo-container {
                top: 20px;
                right: 20px;
            }
            .login-card {
                padding: 40px 20px;
                box-shadow: none;
                background: transparent;
            }
            body {
                background: #fff;
                align-items: flex-start;
                padding-top: 100px;
            }
        }
    </style>
</head>
<body>

    <!-- Background Elements -->
    <div class="bg-shape bg-circle-top"></div>
    <div class="bg-shape bg-circle-bottom"></div>
    
    <!-- Using SVG for the abstract bars on the bottom left -->
    <svg class="bg-shape bg-bars" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M40 400V300C40 288.954 48.9543 280 60 280H80C91.0457 280 100 288.954 100 300V400H40Z" fill="#ffdac2"/>
        <path d="M120 400V200C120 188.954 128.954 180 140 180H160C171.046 180 180 188.954 180 200V400H120Z" fill="#ffc8a6"/>
        <path d="M200 400V100C200 88.9543 208.954 80 220 80H240C251.046 80 260 88.9543 260 100V400H200Z" fill="#ffb487"/>
    </svg>

    <div class="logo-container">
        <a href="{{ url('/') }}">
            <img src="{{ asset('assets/img/Logo2.png') }}" alt="ثمن">
        </a>
    </div>

    <div class="login-card">
        <div class="login-header">
            <h1>مرحباً بعودتك 👋</h1>
            <p>{{ session('otp_sent') ? 'أدخل رمز التحقق المرسل إليك' : 'من فضلك قم بتسجيل الدخول كخبير' }}</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(!session('otp_sent'))
            <!-- Step 1: Request OTP -->
            <form method="POST" action="{{ route('expert.login.send-otp') }}">
                @csrf
                <div class="form-group">
                    <label>رقم الجوال</label>
                    <div class="input-group">
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="05XXXXXXXX" required autofocus>
                        <!-- Phone Icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    إرسال رمز التحقق
                </button>
            </form>
        @else
            <!-- Step 2: Verify OTP -->
            <form method="POST" action="{{ route('expert.login.verify-otp') }}">
                @csrf
                <div class="form-group">
                    <label>رمز التحقق (OTP)</label>
                    <div class="input-group input-otp">
                        <input type="text" name="otp" placeholder="----" maxlength="4" required autofocus>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    تسجيل الدخول
                </button>
            </form>
            
            <div class="footer-links" style="margin-top:15px;">
                <a href="{{ route('expert.login') }}" style="font-weight:600; color:#888;">لم يصلك الرمز؟ حاول مرة أخرى</a>
            </div>
        @endif

        <div class="footer-links">
            <a href="{{ url('/') }}">العودة للرئيسية</a>
        </div>
    </div>

</body>
</html>
