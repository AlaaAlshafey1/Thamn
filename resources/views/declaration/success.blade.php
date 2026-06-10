<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم التوقيع بنجاح - منصة ثمن</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 60px 48px;
            max-width: 540px;
            width: calc(100% - 40px);
            text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto 28px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.35);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 10px 30px rgba(16, 185, 129, 0.35); }
            50% { box-shadow: 0 10px 50px rgba(16, 185, 129, 0.55); }
        }

        h1 {
            font-size: 26px;
            font-weight: 900;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .info-grid {
            background: #f8f5f0;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 28px;
            text-align: right;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #ede9e0;
            font-size: 14px;
        }

        .info-row:last-child { border-bottom: none; }

        .info-label { color: #9ca3af; }
        .info-value { font-weight: 700; color: #1a1a2e; }

        .divider {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #d4af37, #c9933a);
            border-radius: 2px;
            margin: 0 auto 24px;
        }

        .download-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #d4af37, #c9933a);
            color: white;
            text-decoration: none;
            border-radius: 14px;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
            transition: all 0.3s;
            margin-bottom: 14px;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.55);
            color: white;
        }

        .close-note {
            font-size: 12px;
            color: #aaa;
        }

        .logo {
            font-size: 20px;
            font-weight: 900;
            color: #d4af37;
            margin-top: 24px;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="success-icon">✅</div>

    <h1>تم التوقيع بنجاح!</h1>
    <div class="divider"></div>
    <p class="subtitle">
        شكراً {{ $declaration->full_name }}،<br>
        تم حفظ إقرارك على وثيقة الشروط والأحكام وإقرار السرية بنجاح.
    </p>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">الاسم</span>
            <span class="info-value">{{ $declaration->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">رقم الهوية</span>
            <span class="info-value">{{ $declaration->national_id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">تاريخ التوقيع</span>
            <span class="info-value">{{ $declaration->signed_at->format('d/m/Y - H:i') }}</span>
        </div>
    </div>

    @if($declaration->pdf_path)
    <a href="{{ route('declaration.download', ['token' => $token]) }}" class="download-btn">
        ⬇️ تحميل نسخة PDF موقعة
    </a>
    @endif

    <p class="close-note">يمكنك إغلاق هذه النافذة</p>
    <div class="logo">ثمن</div>
</div>
</body>
</html>
