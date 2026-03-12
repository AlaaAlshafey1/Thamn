<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .header {
            background-color: #c1953e;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 20px;
            text-align: right;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>{{ $title }}</h2>
        </div>
        <div class="content">
            <p>مرحباً <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>،</p>
            <p>مرحباً بك في منصة <strong>ثمن</strong>. نحن سعداء بانضمامك إلينا.</p>
            <p>يمكنك الآن البدء في استخدام كافة مميزات المنصة لتقييم مقتنياتك بكل سهولة واحترافية.</p>
        </div>
        <div class="footer">
            <p>تم إرسال هذا البريد من منصة ثمن.</p>
        </div>
    </div>
</body>

</html>