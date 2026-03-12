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

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            border: 1px solid #eee;
            text-align: right;
        }

        .invoice-table th {
            background-color: #f9f9f9;
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
            <h2>فاتورة الدفع</h2>
        </div>
        <div class="content">
            <p>مرحباً <strong>{{ $order->user->first_name }} {{ $order->user->last_name }}</strong>،</p>
            <p>شكراً لثقتك في منصة ثمن. تم استلام مبلغ الدفع لطلبك رقم <strong>#{{ $order->id }}</strong> بنجاح.</p>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>البيان</th>
                        <th>القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>رقم الطلب</td>
                        <td>#{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <td>التاريخ</td>
                        <td>{{ $order->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <td>المبلغ الإجمالي</td>
                        <td>{{ number_format($order->total_price, 2) }} ريال</td>
                    </tr>
                    <tr>
                        <td>حالة الدفع</td>
                        <td>تم الدفع بنجاح</td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top: 20px;">بدأنا العمل على طلبك وسوف نوافيك بالنتائج في أقرب وقت.</p>
        </div>
        <div class="footer">
            <p>تم إرسال هذا البريد تلقائياً من منصة ثمن.</p>
        </div>
    </div>
</body>

</html>