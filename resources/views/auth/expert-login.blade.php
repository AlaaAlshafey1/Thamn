<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول الخبراء - ثمن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --ex-gold:       #ff9800;
            --ex-gold-light: #ffb74d;
            --ex-card:       #ffffff;
            --ex-soft:       #f8f9fc;
            --ex-text:       #111111;
            --ex-text-muted: #888888;
            --ex-border:     #eeeeee;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f2f5 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: var(--ex-text);
        }

        /* Background Effects */
        .bg-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,152,0,0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
            pointer-events: none;
        }
        .bg-glow-1 { top: -150px; right: -100px; }
        .bg-glow-2 { bottom: -150px; left: -100px; width: 500px; height: 500px; opacity: 0.7; }
        
        .floating-icon {
            position: absolute;
            color: var(--ex-gold);
            opacity: 0.05;
            z-index: 1;
            animation: float 6s ease-in-out infinite;
        }
        .icon-1 { top: 15%; left: 15%; font-size: 8rem; animation-delay: 0s; }
        .icon-2 { bottom: 20%; right: 10%; font-size: 12rem; animation-delay: -3s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .logo-container {
            position: absolute;
            top: 40px;
            right: 50px;
            z-index: 10;
        }
        .logo-container img {
            height: 45px;
            transition: opacity 0.3s;
        }
        .logo-container img:hover { opacity: 0.8; }

        .login-card {
            background: var(--ex-card);
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            padding: 45px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            border: 1px solid var(--ex-border);
            position: relative;
            z-index: 10;
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,152,0,0.1);
            border: 1px solid rgba(255,152,0,0.2);
            color: var(--ex-gold);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 800;
            margin-bottom: 16px;
        }
        .login-header h1 {
            font-size: 24px;
            color: var(--ex-text);
            font-weight: 900;
            margin-bottom: 8px;
        }
        .login-header p {
            color: var(--ex-text-muted);
            font-size: 14px;
            font-weight: 600;
            line-height: 1.6;
        }

        .form-group { margin-bottom: 22px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--ex-text);
            font-size: 13px;
        }

        .input-group { position: relative; }
        .input-group input {
            width: 100%;
            height: 52px;
            background: var(--ex-soft);
            border: 1px solid var(--ex-border);
            border-radius: 12px;
            padding: 0 15px 0 45px;
            font-size: 15px;
            color: var(--ex-text);
            outline: none;
            transition: all 0.3s;
            direction: ltr;
            text-align: right;
        }
        .input-group input:focus {
            border-color: var(--ex-gold);
            box-shadow: 0 0 0 4px rgba(255,152,0,0.1);
            background: #fff;
        }
        .input-group input::placeholder { color: #aaa; font-weight: 500; font-size: 13px; text-align: right; }
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        .input-group input:focus + i { color: var(--ex-gold); }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 13px;
        }
        
        .custom-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--ex-text-muted);
            font-weight: 600;
        }
        .custom-check input { display: none; }
        .checkmark {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 2px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .custom-check input:checked + .checkmark {
            background: var(--ex-gold);
            border-color: var(--ex-gold);
        }
        .checkmark::after {
            content: '\eb7a';
            font-family: 'boxicons';
            color: #fff;
            font-size: 14px;
            display: none;
        }
        .custom-check input:checked + .checkmark::after { display: block; }
        
        .forgot-link { color: var(--ex-gold); text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .forgot-link:hover { color: var(--ex-gold-light); }

        .btn-login {
            width: 100%;
            height: 52px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            background: #333;
        }
        .btn-login:active { transform: translateY(0); }

        .error-msg {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #ef4444;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link {
            position: absolute;
            top: 40px;
            left: 50px;
            color: var(--ex-text-muted);
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 10;
            transition: color 0.3s;
        }
        .back-link:hover { color: var(--ex-gold); }

        @media (max-width: 576px) {
            .logo-container { top: 25px; right: 25px; }
            .back-link { top: 25px; left: 25px; font-size: 0; }
            .back-link i { font-size: 1.5rem; }
            .login-card { border-radius: 0; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; border: none; padding: 40px 25px; }
        }
    </style>
</head>
<body>

    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <i class='bx bx-check-shield floating-icon icon-1'></i>
    <i class='bx bx-diamond floating-icon icon-2'></i>

    <a href="{{ url('/') }}" class="logo-container">
        <img src="{{ asset('assets/img/Logo2.png') }}" alt="ثمن">
    </a>

    <a href="{{ url('/') }}" class="back-link">
        <i class='bx bx-arrow-back'></i>
        العودة للموقع
    </a>

    <div class="login-card">
        <div class="login-header">
            <div class="badge">
                <i class='bx bxs-shield-check'></i>
                بوابة الخبراء
            </div>
            <h1>مرحباً بعودتك!</h1>
            <p>قم بتسجيل الدخول للوصول إلى لوحة التقييم والتثمين الخاصة بك</p>
        </div>

        @if ($errors->any())
            <div class="error-msg">
                <i class='bx bxs-error-circle'></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="error-msg" style="background: rgba(46, 204, 113, 0.1); border-color: rgba(46, 204, 113, 0.2); color: #2ecc71;">
                <i class='bx bxs-check-circle'></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(!session('otp_sent'))
            <!-- Step 1: Send OTP Form -->
            <form method="POST" action="{{ route('expert.login.send-otp') }}">
                @csrf
                <div class="form-group">
                    <label for="identifier">البريد الإلكتروني أو رقم الجوال</label>
                    <div class="input-group">
                        <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required autofocus placeholder="expert@thamn.com أو 05xxxxxxxx">
                        <i class='bx bx-user'></i>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    إرسال رمز التحقق
                    <i class='bx bx-send' style="font-size: 1.3rem;"></i>
                </button>
            </form>
        @else
            <!-- Step 2: Verify OTP Form -->
            <form method="POST" action="{{ route('expert.login.verify-otp') }}">
                @csrf
                <div class="form-group">
                    <label for="otp">رمز التحقق (OTP)</label>
                    <div class="input-group">
                        <input id="otp" type="text" name="otp" required autofocus placeholder="1234" maxlength="4" style="text-align: center; letter-spacing: 10px; font-size: 1.5rem; font-weight: bold;">
                        <i class='bx bx-key'></i>
                    </div>
                    <div style="font-size: 0.85rem; color: #888; text-align: center; margin-top: 10px;">
                        تم إرسال الرمز إلى حسابك.
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    تأكيد وتسجيل الدخول
                    <i class='bx bx-log-in-circle' style="font-size: 1.3rem;"></i>
                </button>
            </form>
        @endif
    </div>

</body>
</html>
