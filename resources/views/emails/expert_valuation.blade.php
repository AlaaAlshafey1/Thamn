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
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #c1953e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>إشعار تقييم جديد</h2>
        </div>
        <div class="content">
            <p>مرحباً مدير النظام،</p>
            <p>نود إعلامك بأن الخبير <strong>{{ $expert->first_name }} {{ $expert->last_name }}</strong> قد انتهى من
                تقييم الطلب رقم <strong>#{{ $order->id }}</strong>.</p>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>السعر الأدنى:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">
                        {{ number_format($order->expert_min_price, 2) }} ريال</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>السعر الأعلى:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">
                        {{ number_format($order->expert_max_price, 2) }} ريال</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>السعر المقترح:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">
                        {{ number_format($order->expert_price, 2) }} ريال</td>
                </tr>
            </table>

            <p style="margin-top: 20px;"><strong>توضيح الخبير:</strong><br>
                {{ $order->expert_reasoning }}</p>

            <a href="{{ url('/orders/' . $order->id) }}" class="button">عرض تفاصيل الطلب في لوحة التحكم</a>
        </div>
        <div class="footer">
            <p>تم إرسال هذا البريد تلقائياً من منصة تثمين.</p>
        </div>
    </div>
</body>

</html>