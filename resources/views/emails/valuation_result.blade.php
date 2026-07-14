<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتيجة التقييم — تطبيق ثمن</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            background-color: #f0ece4;
            color: #2d2d2d;
            direction: rtl;
            text-align: right;
            padding: 30px 15px;
        }

        .wrapper { max-width: 580px; margin: 0 auto; }

        /* ===== HEADER ===== */
        .header {
            background: linear-gradient(135deg, #c1953e 0%, #9a7230 50%, #c1953e 100%);
            border-radius: 16px 16px 0 0;
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -30px; left: -30px;
            width: 150px; height: 150px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -40px; right: -20px;
            width: 120px; height: 120px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .header-logo {
            font-size: 13px;
            color: rgba(255,255,255,0.75);
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 900;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .header-badge {
            display: inline-block;
            margin-top: 12px;
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* ===== BODY ===== */
        .body { background: #ffffff; padding: 35px 30px; }

        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #2d2d2d;
            margin-bottom: 8px;
        }
        .greeting span { color: #c1953e; }

        .subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 28px;
            line-height: 1.7;
        }

        /* ===== INFO BOX ===== */
        .info-box {
            background: #fdfaf4;
            border: 1px solid #ead9b0;
            border-radius: 12px;
            padding: 6px 20px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0e8d2;
        }
        .info-row:last-child { border-bottom: none; }

        .info-label {
            font-size: 13px;
            color: #8a7050;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .info-value {
            font-size: 14px;
            color: #2d2d2d;
            font-weight: 700;
            text-align: left;
            margin-right: 12px;
        }

        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-ai    { background: #e8f4fd; color: #0d6efd; border: 1px solid #b6d4fe; }
        .badge-expert{ background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .badge-thamn { background: #f4ead2; color: #9a7230; border: 1px solid #c1953e; }

        /* ===== PRICE BOX ===== */
        .price-box {
            background: linear-gradient(135deg, #f0fff4 0%, #e6f9ed 100%);
            border: 1px solid #b7e4c7;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .price-label {
            font-size: 13px;
            color: #198754;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .price-amount {
            font-size: 42px;
            font-weight: 900;
            color: #157347;
            line-height: 1.1;
        }
        .price-currency {
            font-size: 20px;
            font-weight: 700;
            color: #198754;
            margin-right: 4px;
        }
        .price-range {
            display: inline-block;
            margin-top: 12px;
            background: rgba(255,255,255,0.7);
            border: 1px solid #c3e6cb;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 13px;
            color: #5a8a6a;
            font-weight: 600;
        }

        /* ===== REASONING BOX ===== */
        .reasoning-box {
            background: #fdfaf4;
            border: 1px solid #ead9b0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .reasoning-title {
            font-size: 14px;
            font-weight: 700;
            color: #c1953e;
            margin-bottom: 10px;
        }
        .reasoning-text {
            font-size: 14px;
            color: #4a4a4a;
            line-height: 1.9;
            text-align: justify;
        }

        /* ===== DIVIDER ===== */
        .divider {
            height: 1px;
            background: linear-gradient(to left, transparent, #e0d0b0, transparent);
            margin: 25px 0;
        }

        /* ===== CTA BUTTON ===== */
        .cta-wrapper { text-align: center; margin: 25px 0 10px; }
        .btn {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(135deg, #c1953e, #a07830);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 20px rgba(193,149,62,0.35);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: #f8f5ef;
            border-radius: 0 0 16px 16px;
            padding: 22px 30px;
            text-align: center;
            border-top: 1px solid #e8dcc4;
        }
        .footer p {
            font-size: 12px;
            color: #9a8870;
            margin: 4px 0;
            line-height: 1.6;
        }
        .footer strong { color: #c1953e; }
        .footer-divider { height: 1px; background: #e0d0b0; margin: 12px 0; }

        @media (max-width: 480px) {
            body { padding: 15px 8px; }
            .header { padding: 25px 20px; }
            .body { padding: 25px 20px; }
            .price-amount { font-size: 34px; }
            .header h1 { font-size: 20px; }
        }
    </style>
</head>

<body>
    <div class="wrapper">

        {{-- HEADER --}}
        <div class="header">
            <div class="header-logo">تطبيق ثمن · Thamn</div>
            <h1>✅ نتيجة تقييم طلبك</h1>
            <span class="header-badge">تم التقييم بنجاح</span>
        </div>

        {{-- BODY --}}
        <div class="body">

            <p class="greeting">مرحباً، <span>{{ $order->user->first_name ?? 'عميلنا العزيز' }}</span> 👋</p>
            <p class="subtitle">
                تم الانتهاء من تقييم منتجك <strong>({{ $categoryName }})</strong> بنجاح.
                يمكنك الاطلاع على تفاصيل نتيجة التقييم أدناه.
            </p>

            {{-- ORDER INFO BOX --}}
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">📦 رقم الطلب:</span>
                    <span class="info-value">#{{ $order->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🔍 نوع التقييم:</span>
                    <span class="info-value">
                        @if($evaluationType === 'ai')
                            <span class="badge badge-ai">التقييم الذكي (AI)</span>
                        @elseif($evaluationType === 'expert')
                            <span class="badge badge-expert">تقييم خبير موثوق</span>
                        @else
                            <span class="badge badge-thamn">تقييم فريق ثمن</span>
                        @endif
                    </span>
                </div>
            </div>

            {{-- PRICE BOX --}}
            @if($recommendedPrice)
                <div class="price-box">
                    <div class="price-label">💎 السعر العادل المقدر</div>
                    <div class="price-amount">
                        {{ number_format($recommendedPrice, 0) }}
                        <span class="price-currency">ريال</span>
                    </div>
                    @if($minPrice && $maxPrice)
                        <div class="price-range">
                            نطاق السعر: من {{ number_format($minPrice, 0) }} إلى {{ number_format($maxPrice, 0) }} ريال
                        </div>
                    @endif
                </div>
            @endif

            {{-- REASONING BOX --}}
            @if($reasoning)
                <div class="reasoning-box">
                    <div class="reasoning-title">📋 ملاحظات التقييم:</div>
                    <p class="reasoning-text">{{ $reasoning }}</p>
                </div>
            @endif

            <div class="divider"></div>

            {{-- CTA BUTTON --}}
            <div class="cta-wrapper">
                <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="btn">
                    عرض تفاصيل الطلب &larr;
                </a>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <p>📧 شكراً لثقتكم في <strong>تطبيق ثمن</strong></p>
            <div class="footer-divider"></div>
            <p>&copy; {{ date('Y') }} تطبيق ثمن — جميع الحقوق محفوظة</p>
            <p style="margin-top:6px; font-size:11px;">إذا كنت تعتقد أن هذا البريد وصلك بالخطأ، يُرجى تجاهله.</p>
        </div>

    </div>
</body>

</html>