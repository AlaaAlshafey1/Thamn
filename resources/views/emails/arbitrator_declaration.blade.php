<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وثيقة الشروط والأحكام - ثمن</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f5f0;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e0d0b0;
        }
        .header {
            background-color: #fdf9f4;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #c9933a;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #1a1a2e;
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
            color: #4a4a4a;
            line-height: 1.8;
            font-size: 16px;
            text-align: right;
        }
        .content h2 {
            color: #c9933a;
            font-size: 20px;
            margin-top: 0;
        }
        .btn-box {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #d4af37, #c9933a);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(201, 147, 58, 0.3);
        }
        .footer {
            background-color: #1a1a2e;
            color: #aaaaaa;
            text-align: center;
            padding: 20px;
            font-size: 13px;
        }
        .footer a {
            color: #c9933a;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="{{ url('assets/img/Logo.png') }}" alt="شعار ثمن">
        <h1>وثيقة الشروط والأحكام وإقرار السرية</h1>
    </div>

    <div class="content">
        <h2>مرحباً أ. {{ $arbitrator->first_name }}،</h2>
        <p>نرحب بك في منصة <strong>ثمن</strong> كأحد المحكمين المستقلين.</p>
        <p>لاستكمال إجراءات انضمامك وتفعيل حسابك كمحكم وتسلم طلبات التثمين، يرجى الاطلاع على <strong>وثيقة الشروط والأحكام وإقرار السرية</strong> والموافقة عليها والتوقيع عليها إلكترونياً.</p>
        
        <div class="btn-box">
            <a href="{{ $declarationUrl }}" class="btn">عرض وتوقيع الوثيقة الآن</a>
        </div>

        <p style="font-size: 14px; color: #777;">إذا كنت تواجه صعوبة في الضغط على الزر، يمكنك نسخ هذا الرابط ولصقه في متصفحك:<br>
        <a href="{{ $declarationUrl }}" style="color:#c9933a; word-break: break-all;">{{ $declarationUrl }}</a></p>
    </div>

    <div class="footer">
        هذه رسالة تلقائية من <a href="{{ url('/') }}">منصة ثمن للتثمين المهني</a>. يرجى عدم الرد على هذا البريد.<br>
        &copy; {{ date('Y') }} جميع الحقوق محفوظة.
    </div>
</div>

</body>
</html>
