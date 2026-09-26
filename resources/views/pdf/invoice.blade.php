<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة طلب #{{ $order->id }}</title>
    <style>
        body { 
            font-family: 'dejavusans', sans-serif; 
            direction: rtl; 
            font-size: 14px;
            color: #1A1A1A;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
        }
        .container {
            background-color: #FFFFFF;
            padding: 40px;
            margin: 20px auto;
            border-radius: 12px;
            border: 1px solid #E5E5E5;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #C1953E;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .sec-title {
            color: #C1953E;
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #EEEEEE;
            padding-bottom: 8px;
        }
        .invoice-details {
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-details td {
            vertical-align: top;
        }
        .label {
            font-size: 12px;
            color: #888888;
            margin-bottom: 4px;
        }
        .value {
            font-size: 15px;
            font-weight: bold;
            color: #1A1A1A;
            margin-bottom: 15px;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table-items th {
            background-color: #F9F9FB;
            color: #1A1A1A;
            font-size: 13px;
            padding: 12px;
            text-align: right;
            border: 1px solid #EEEEEE;
        }
        .table-items td {
            padding: 15px 12px;
            border: 1px solid #EEEEEE;
            font-size: 14px;
        }
        .total-box {
            width: 100%;
        }
        .total-box-inner {
            background-color: #1A1A1A;
            color: #FFFFFF;
            padding: 20px;
            border-radius: 8px;
            float: left;
            width: 300px;
            text-align: center;
        }
        .total-label {
            font-size: 13px;
            color: #C1953E;
            margin-bottom: 5px;
        }
        .total-amount {
            font-size: 32px;
            font-weight: bold;
        }
        .total-currency {
            font-size: 12px;
            color: #999999;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 50px;
            border-top: 1px solid #EEE;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="70%" valign="middle">
                    <span style="color: #C1953E; font-size: 12px; font-weight: bold;">فاتورة طلب تثمين</span>
                    <h1 style="margin: 5px 0; font-size: 28px; color: #1A1A1A;">فاتورة</h1>
                    <span style="color: #888888; font-size: 13px;">
                        رقم الفاتورة: <strong style="color: #C1953E;">#INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </span>
                </td>
                <td width="30%" align="left" valign="middle">
                    <img src="{{ public_path('assets/img/Logo.png') }}" height="60">
                </td>
            </tr>
        </table>

        <table class="invoice-details" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="50%">
                    <div class="label">تاريخ الإصدار</div>
                    <div class="value">{{ $order->created_at?->format('Y-m-d') ?? now()->format('Y-m-d') }}</div>
                    
                    <div class="label">حالة الدفع</div>
                    <div class="value" style="color: #27ae60;">مدفوعة</div>
                </td>
                <td width="50%">
                    <div class="label">صدرت إلى</div>
                    <div class="value">{{ $order->user?->first_name }} {{ $order->user?->last_name }}</div>
                    
                    <div class="label">رقم التواصل</div>
                    <div class="value" dir="ltr" style="text-align: right;">{{ $order->user?->phone_number ?? '---' }}</div>
                </td>
            </tr>
        </table>

        <div class="sec-title">تفاصيل الطلب</div>
        <table class="table-items" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="70%">الوصف</th>
                    <th width="30%" style="text-align: center;">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="display:block; margin-bottom: 5px;">رسوم تقييم وتثمين لطلب</strong>
                        <span style="color: #555; font-size: 13px;">الطلب: {{ $orderName }}</span>
                    </td>
                    <td align="center" valign="middle">
                        <strong>{{ number_format($order->total_price ?? $order->price ?? 0, 2) }} ر.س</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-box-inner">
                <div class="total-label">المبلغ الإجمالي المدفوع</div>
                <div class="total-amount">{{ number_format($order->total_price ?? $order->price ?? 0, 2) }}</div>
                <div class="total-currency">ريال سعودي</div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            شكراً لثقتكم بنا، منصة <strong style="color: #C1953E;">ثمن</strong> للتثمين الذكي<br>
            هذه الفاتورة مصدّرة إلكترونياً ولا تحتاج إلى توقيع.
        </div>
    </div>
</body>
</html>
