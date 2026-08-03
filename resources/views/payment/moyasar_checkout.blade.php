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
            margin-bottom: 25px;
        }

        .header img {
            max-height: 120px;
            width: auto;
            object-fit: contain;
            margin-bottom: 8px;
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
        /* لا نعكس الاتجاه للفورم لأن ذلك يسبب تداخل الأزرار */
        .moyasar-form {
            direction: ltr;
            unicode-bidi: isolate;
        }

        /* عزل أزرار طرق الدفع - كل واحد في سطر مستقل */
        .mysr-form-cb-wrapper {
            display: flex !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }

        .mysr-form-cb-wrapper label {
            width: 100% !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            padding: 14px !important;
            border-radius: 12px !important;
            border: 2px solid var(--border) !important;
            cursor: pointer !important;
            transition: border-color 0.2s ease !important;
            /* كل label له stacking context مستقل تماماً */
            isolation: isolate !important;
            contain: layout !important;
        }

        .mysr-form-cb-wrapper label:hover {
            border-color: #475569 !important;
        }

        /*
         * apple-pay-button هو web component خاص من Apple
         * يجب أن يكون له حاوية واضحة ولا يتداخل مع عناصر أخرى
         */
        apple-pay-button {
            display: block !important;
            width: 100% !important;
            min-height: 48px !important;
            margin-top: 10px !important;
            /* عزل تام عن العناصر الأخرى */
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        /* حاوية Apple Pay في Moyasar */
        .mysr-applepay-button-wrapper,
        [class*="applepay"],
        [class*="apple-pay"] {
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
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
            <img src="{{ asset('assets/img/Logo.png') }}" alt="ثمن">
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
    // إعداد طرق الدفع
    // ============================================================
    var paymentMethods = ['creditcard', 'stcpay'];
    var applePayConfig = undefined;

    if (window.location.protocol === 'https:') {
        paymentMethods = ['applepay', 'creditcard', 'stcpay'];
        applePayConfig = {
            country: 'SA',
            currency: 'SAR',
            label: 'Thamn',
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

    // ============================================================
    // FIX: منع تداخل Apple Pay مع STC Pay
    //
    // المشكلة: Moyasar يعرض apple-pay-button كـ web component
    // بيكون فوق radio buttons الأخرى. لما المستخدم يضغط على
    // Apple Pay، الـ click event بيعدي من خلاله ويضغط على
    // STC radio تحته، فبيغير الـ selection تلقائياً.
    //
    // الحل: نراقب DOM بـ MutationObserver وبعد ما Moyasar يعمل
    // render، نضيف event listener على apple-pay-button يمنع
    // الـ event من الوصول للعناصر اللي وراه.
    // ============================================================
    (function fixApplePayClickThrough() {
        if (window.location.protocol !== 'https:') return;

        var formEl = document.querySelector('.moyasar-form');
        if (!formEl) return;

        var observer = new MutationObserver(function() {
            var applePayBtn = formEl.querySelector('apple-pay-button');
            if (!applePayBtn || applePayBtn._fixedClickThrough) return;

            applePayBtn._fixedClickThrough = true;

            // منع الـ click من الوصول للعناصر الأخرى (stopPropagation)
            // لكن نسمح بـ Apple Pay نفسه يكمل عمله (لا نعمل preventDefault)
            applePayBtn.addEventListener('click', function(e) {
                e.stopImmediatePropagation();
            }, true); // capture phase - يمسك الـ event قبل أي listener تاني

            // تأكد إن الـ radio button بتاع Apple Pay محدد
            var applePayRadio = formEl.querySelector('input[value="applepay"]');
            if (applePayRadio && !applePayRadio.checked) {
                applePayRadio.checked = true;
                applePayRadio.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // منع أي radio button تاني يتحدد لما apple-pay-button موجود ومرئي
            var applePayWrapper = applePayBtn.closest('[class*="applepay"], [class*="apple"]');
            if (applePayWrapper) {
                var otherRadios = formEl.querySelectorAll('input[type="radio"]:not([value="applepay"])');
                otherRadios.forEach(function(radio) {
                    radio.addEventListener('click', function(e) {
                        // إذا apple-pay-button مرئي والـ click جاي من داخله، امنعه
                        var applePayVisible = applePayBtn.offsetParent !== null;
                        var clickedInsideApplePay = applePayBtn.contains(e.target);
                        if (applePayVisible && clickedInsideApplePay) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                        }
                    }, true);
                });
            }
        });

        observer.observe(formEl, {
            childList: true,
            subtree: true
        });
    })();
</script>
</body>
</html>
