<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب سحب رصيد جديد</title>
    <style>
        body { font-family: 'Tajawal', Tahoma, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 40px 20px; }
        .container { max-width: 550px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { max-width: 120px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #111827; font-size: 24px; text-align: center; margin-bottom: 10px; }
        p.subtitle { text-align: center; font-size: 16px; color: #4b5563; line-height: 1.6; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px dashed #e5e7eb; }
        .user-name { font-weight: bold; color: #c1953e; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 14px 15px; text-align: right; font-size: 15px; }
        th { color: #6b7280; font-weight: normal; width: 40%; border-bottom: 1px solid #f3f4f6; }
        td { color: #111827; font-weight: 600; border-bottom: 1px solid #f3f4f6; }
        .amount-row td { color: #10b981; font-size: 18px; }
        .btn-wrapper { text-align: center; margin-top: 35px; margin-bottom: 20px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #c1953e, #e8b64c); color: #ffffff !important; text-decoration: none; padding: 14px 35px; border-radius: 50px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(193, 149, 62, 0.4); transition: transform 0.2s, box-shadow 0.2s; }
        .footer { text-align: center; color: #9ca3af; font-size: 13px; border-top: 1px solid #f3f4f6; padding-top: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- استخدام المسار المطلق للوجو حتى يظهر في الإيميل -->
            <img src="{{ asset('assets/img/Logo.png') }}" alt="شعار منصة ثمن">
        </div>
        
        <h2>طلب سحب رصيد جديد 💰</h2>
        <p class="subtitle">قام الخبير <span class="user-name">{{ $withdrawal->user->first_name }} {{ $withdrawal->user->last_name }}</span> بتقديم طلب سحب رصيد من حسابه في منصة ثمن.</p>
        
        <table>
            <tbody>
                <tr class="amount-row">
                    <th>المبلغ المطلوب</th>
                    <td dir="ltr" style="text-align: right;">{{ number_format($withdrawal->amount, 2) }} SAR</td>
                </tr>
                <tr>
                    <th>رقم الجوال</th>
                    <td dir="ltr" style="text-align: right;">{{ $withdrawal->user->phone ?? 'غير متوفر' }}</td>
                </tr>
                <tr>
                    <th>البنك الخاص به</th>
                    <td>{{ $withdrawal->user->bank_name ?? 'غير متوفر' }}</td>
                </tr>
                <tr>
                    <th>الآيبان (IBAN)</th>
                    <td dir="ltr" style="text-align: right; font-size: 13px;">{{ $withdrawal->user->iban ?? 'غير متوفر' }}</td>
                </tr>
                <tr>
                    <th>تاريخ الطلب</th>
                    <td dir="ltr" style="text-align: right; font-size: 14px;">{{ $withdrawal->created_at->format('Y-m-d h:i A') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrapper">
            <!-- توجيه الإدمن مباشرة لصفحة تفاصيل الطلب بدلاً من القائمة العامة -->
            <a href="{{ route('withdrawals.show', $withdrawal->id) }}" class="btn">عرض الطلب والموافقة</a>
        </div>

        <div class="footer">
            <p>هذه رسالة إشعار تلقائية من النظام - منصة ثمن</p>
        </div>
    </div>
</body>
</html>
