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

        .otp-box {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 15px 30px;
            background-color: #f9f9f9;
            border: 2px dashed #c1953e;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #c1953e;
            border-radius: 5px;
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
            <h2>كود التفعيل</h2>
        </div>
        <div class="content">
            <p>مرحباً <strong>{{ $userName }}</strong>،</p>
            <p>شكراً لتسجيلك في منصة ثمن. لتفعيل حسابك، يرجى استخدام كود التفعيل التالي:</p>

            <div class="otp-box">
                {{ $otp }}
            </div>

            <p>هذا الكود صالح لمدة 5 دقائق فقط.</p>
            <p>إذا لم تكن قد طلبت هذا الكود، يرجى تجاهل هذا البريد.</p>
        </div>
        <div class="footer">
            <p>تم إرسال هذا البريد تلقائياً من منصة ثمن.</p>
        </div>
    </div>
</body>

</html>