<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نتيجة التقييم</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #c1953e;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .info-box {
            background-color: #fdfbf7;
            border: 1px solid #eaddc4;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-box strong {
            color: #c1953e;
        }
        .price-box {
            text-align: center;
            padding: 20px;
            background-color: #f4fcf6;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .price-box h2 {
            margin: 0;
            color: #28a745;
            font-size: 28px;
        }
        .price-range {
            color: #6c757d;
            font-size: 14px;
            margin-top: 10px;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #c1953e;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>نتيجة تقييم طلبك</h1>
        </div>
        
        <div class="content">
            <p>مرحباً <strong>{{ $order->user->first_name ?? 'عميلنا العزيز' }}</strong>،</p>
            <p>تم الانتهاء من تقييم منتجك ({{ $categoryName }}) بنجاح.</p>
            
            <div class="info-box">
                <p><strong>رقم الطلب:</strong> #{{ $order->id }}</p>
                <p><strong>نوع التقييم:</strong> 
                    @if($evaluationType === 'ai')
                        التقييم الذكي (AI)
                    @elseif($evaluationType === 'expert')
                        تقييم خبير موثوق
                    @else
                        تقييم فريق ثمن
                    @endif
                </p>
            </div>

            @if($recommendedPrice)
            <div class="price-box">
                <p style="margin-top:0; color:#28a745; font-weight:bold;">السعر العادل المقدر:</p>
                <h2>{{ $recommendedPrice }} ريال</h2>
                
                @if($minPrice && $maxPrice)
                <div class="price-range">
                    نطاق السعر: من {{ $minPrice }} إلى {{ $maxPrice }} ريال
                </div>
                @endif
            </div>
            @endif

            @if($reasoning)
            <div class="info-box">
                <strong>ملاحظات التقييم:</strong>
                <p style="margin-top: 5px;">{{ $reasoning }}</p>
            </div>
            @endif

            <p style="text-align:center;">
                <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="btn">عرض تفاصيل الطلب</a>
            </p>
        </div>
        
        <div class="footer">
            <p>شكراً لثقتكم في منصة ثمن.</p>
            <p>&copy; {{ date('Y') }} منصة ثمن. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
