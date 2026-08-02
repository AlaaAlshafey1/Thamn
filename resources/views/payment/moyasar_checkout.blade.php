<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الدفع - ثمن</title>

    <!-- Google Fonts: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Moyasar Payment Form CSS -->
    <link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.14.0/moyasar.css">

    <style>
        :root {
            --primary: #1e293b;
            --primary-hover: #0f172a;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkout-wrapper {
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.4s ease-out;
        }

        .payment-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid var(--border);
        }

        /* Header / Logo */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            max-height: 50px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .header h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .header p {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
        }

        /* Order Summary Box */
        .order-summary {
            background: var(--bg);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .order-summary .label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .order-summary .amount {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin-bottom: 12px;
        }

        .order-summary .amount span {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .order-badge {
            display: inline-flex;
            align-items: center;
            background: #e2e8f0;
            color: #475569;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Moyasar Form Overrides */
        .moyasar-form {
            direction: ltr;
        }

        /* Trust Badges */
        .trust-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #10b981;
            background: #ecfdf5;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .secure-badge svg {
            width: 14px;
            height: 14px;
        }

        .payment-methods {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .payment-methods svg {
            height: 24px;
            width: auto;
            opacity: 0.6;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="checkout-wrapper">
    <div class="payment-card">
        
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('assets/img/Logo2.png') }}" alt="ثمن">
            <h2>إتمام الدفع</h2>
            <p>بوابة الدفع الإلكتروني الآمنة</p>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="label">إجمالي المبلغ</div>
            <div class="amount">
                {{ number_format($order->total_price, 2) }}
                <span>ر.س</span>
            </div>
            <div class="order-badge">
                طلب رقم #{{ $order->id }}
            </div>
        </div>

        <!-- Moyasar Payment Form -->
        <div class="moyasar-form"></div>

        <!-- Trust Indicators -->
        <div class="trust-section">
            <div class="secure-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
                <span>مدفوعات آمنة وموثقة</span>
            </div>
        </div>

    </div>
</div>

<!-- Moyasar Payment Form JS -->
<script src="https://cdn.moyasar.com/mpf/1.14.0/moyasar.js"></script>
<script>
    // نحدد طرق الدفع ديناميكياً. نفعل Apple Pay فقط إذا كان الموقع يعمل بـ HTTPS (مثل بيئة الإنتاج)
    // لتجنب توقف الفورم بالكامل في بيئة التطوير المحلية (Localhost HTTP)
    var paymentMethods = ['creditcard', 'stcpay'];
    var applePayConfig = undefined;

    if (window.location.protocol === 'https:') {
        paymentMethods.push('applepay');
        applePayConfig = {
            country: 'SA',
            currency: 'SAR',
            label: 'Thamn | ثمن',
            validate_merchant_url: 'https://api.moyasar.com/v1/applepay/initiate'
        };
    }

    Moyasar.init({
        element: '.moyasar-form',
        amount: {{ (int) round($order->total_price * 100) }},
        currency: 'SAR',
        description: 'طلب تثمين رقم #{{ $order->id }}',
        publishable_api_key: '{{ config("services.moyasar.publishable_key") }}',
        callback_url: '{{ $callbackUrl }}',
        methods: paymentMethods,
        apple_pay: applePayConfig,
        metadata: {
            order_id: {{ $order->id }},
            user_id: {{ $order->user_id ?? 0 }},
        }
    });
</script>
</body>
</html>
