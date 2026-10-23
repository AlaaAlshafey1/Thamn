<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>دخول الخبراء - ثمن</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --ex-gold:       #ff9800;
            --ex-gold-light: #ffb74d;
            --ex-card:       #ffffff;
            --ex-soft:       #f8f9fc;
            --ex-text:       #111111;
            --ex-text-muted: #777777;
            --ex-border:     #e9ecef;
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'IBM Plex Sans Arabic', sans-serif;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f0f2f5 0%, #ffffff 100%);
            color: var(--ex-text);
        }

        /* Isolated background glow layer that cannot cause overflow */
        .bg-decorations {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        .bg-glow {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .bg-glow-1 {
            width: 380px;
            height: 380px;
            top: -120px;
            right: -100px;
            background: radial-gradient(circle, rgba(255,152,0,0.12) 0%, transparent 70%);
        }
        .bg-glow-2 {
            width: 440px;
            height: 440px;
            bottom: -120px;
            left: -100px;
            background: radial-gradient(circle, rgba(255,152,0,0.08) 0%, transparent 70%);
        }
        
        .floating-icon {
            position: absolute;
            color: var(--ex-gold);
            opacity: 0.04;
            pointer-events: none;
            animation: float 7s ease-in-out infinite;
        }
        .icon-1 { top: 12%; left: 8%; font-size: 7rem; animation-delay: 0s; }
        .icon-2 { bottom: 12%; right: 8%; font-size: 9rem; animation-delay: -3.5s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-16px) rotate(4deg); }
        }

        /* Full page layout container */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 24px 20px;
            box-sizing: border-box;
        }

        /* Top Navigation Header */
        .top-nav {
            width: 100%;
            max-width: 440px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }
        .logo-link img {
            height: 42px;
            width: auto;
            display: block;
            transition: opacity 0.25s;
        }
        .logo-link:hover img { opacity: 0.8; }
        
        .back-link {
            color: var(--ex-text-muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid var(--ex-border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            transition: all 0.25s;
        }
        .back-link:hover {
            color: var(--ex-gold);
            border-color: rgba(255, 152, 0, 0.35);
            background: #ffffff;
        }

        /* Main Center Card */
        .login-main {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            margin: auto 0;
            padding: 10px 0;
        }

        .login-card {
            background: var(--ex-card);
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            padding: 42px 36px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--ex-border);
            box-sizing: border-box;
            position: relative;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 152, 0, 0.1);
            border: 1px solid rgba(255, 152, 0, 0.25);
            color: var(--ex-gold);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 800;
            margin-bottom: 16px;
        }
        .login-header h1 {
            font-size: 24px;
            color: var(--ex-text);
            font-weight: 900;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .login-header p {
            color: var(--ex-text-muted);
            font-size: 14px;
            font-weight: 600;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 22px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--ex-text);
            font-size: 13.5px;
        }

        .input-group {
            position: relative;
            width: 100%;
        }
        .input-group input {
            width: 100%;
            height: 52px;
            background: var(--ex-soft);
            border: 1.5px solid var(--ex-border);
            border-radius: 12px;
            padding: 0 16px 0 46px;
            font-size: 15px;
            color: var(--ex-text);
            outline: none;
            transition: all 0.25s;
            direction: rtl;
            text-align: right;
            box-sizing: border-box;
        }
        .input-group input:focus {
            border-color: var(--ex-gold);
            box-shadow: 0 0 0 4px rgba(255, 152, 0, 0.12);
            background: #ffffff;
        }
        .input-group input::placeholder {
            color: #aaa;
            font-weight: 500;
            font-size: 13.5px;
            text-align: right;
            direction: rtl;
        }
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.25rem;
            pointer-events: none;
            transition: color 0.25s;
        }
        .input-group input:focus ~ i {
            color: var(--ex-gold);
        }

        .btn-login {
            width: 100%;
            height: 52px;
            background: #111111;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.15);
            background: #222222;
        }
        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
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

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .page-wrapper {
                padding: 16px 14px 20px;
            }
            .top-nav {
                margin-bottom: 14px;
            }
            .logo-link img {
                height: 36px;
            }
            .back-link {
                font-size: 0.82rem;
                padding: 6px 12px;
            }
            .login-card {
                border-radius: 20px;
                padding: 28px 20px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            }
            .login-header {
                margin-bottom: 24px;
            }
            .login-header h1 {
                font-size: 21px;
            }
            .login-header p {
                font-size: 13px;
            }
            .btn-login {
                height: 50px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Background visual decorations (constrained in fixed container) -->
    <div class="bg-decorations" aria-hidden="true">
        <div class="bg-glow bg-glow-1"></div>
        <div class="bg-glow bg-glow-2"></div>
        <i class='bx bx-check-shield floating-icon icon-1'></i>
        <i class='bx bx-diamond floating-icon icon-2'></i>
    </div>

    <!-- Main Page Layout -->
    <div class="page-wrapper">

        <!-- Top Navigation -->
        <header class="top-nav">
            <a href="{{ url('/') }}" class="logo-link">
                <img src="{{ asset('assets/img/Logo-black.png') }}" alt="ثمن">
            </a>

            <a href="{{ url('/') }}" class="back-link">
                <i class='bx bx-right-arrow-alt'></i>
                العودة للموقع
            </a>
        </header>

        <!-- Login Main Area -->
        <main class="login-main">
            <div class="login-card">
                <div class="login-header">
                    <div class="badge">
                        <i class='bx bxs-shield-check'></i>
                        بوابة الخبراء
                    </div>
                    <h1>مرحباً بعودتك</h1>
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
                            <span>إرسال رمز التحقق</span>
                            <i class='bx bx-send' style="font-size: 1.25rem;"></i>
                        </button>
                    </form>
                @else
                    <!-- Step 2: Verify OTP Form -->
                    <form method="POST" action="{{ route('expert.login.verify-otp') }}">
                        @csrf
                        <div class="form-group">
                            <label for="otp">رمز التحقق (OTP)</label>
                            <div class="input-group">
                                <input id="otp" type="text" name="otp" required autofocus placeholder="1234" maxlength="4" style="text-align: center; letter-spacing: 10px; font-size: 1.5rem; font-weight: bold; direction: ltr;">
                                <i class='bx bx-key'></i>
                            </div>
                            <div style="font-size: 0.85rem; color: #888; text-align: center; margin-top: 10px;">
                                تم إرسال الرمز إلى حسابك.
                            </div>
                        </div>

                        <button type="submit" class="btn-login">
                            <span>تأكيد وتسجيل الدخول</span>
                            <i class='bx bx-log-in-circle' style="font-size: 1.25rem;"></i>
                        </button>
                    </form>
                @endif
            </div>
        </main>

        <!-- Subtle Footer Spacer -->
        <div style="height: 10px;"></div>

    </div>

</body>
</html>
