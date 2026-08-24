<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
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

        body {
            font-family: 'Avenir Arabic', Tahoma, Arial, sans-serif;
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
            <img src="{{ asset('assets/img/Logo2.png') }}" alt="شعار ثمن"
                style="max-height: 80px; margin-bottom: 10px;">
            <h2>{{ $title }}</h2>
        </div>
        <div class="content">
            <p>مرحباً <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>،</p>
            <p>مرحباً بك في تطبيق <strong>ثمن</strong>. نحن سعداء بانضمامك إلينا.</p>
            <p>يمكنك الآن البدء في استخدام كافة مميزات التطبيق لتقييم مقتنياتك بكل سهولة واحترافية.</p>
        </div>
        <div class="footer">
            <p>تم إرسال هذا البريد من تطبيق ثمن.</p>
        </div>
    </div>
</body>

</html>