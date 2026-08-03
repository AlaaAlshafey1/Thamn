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

        /*
         * =====================================================
         * Moyasar Form - الفورم يكون LTR دائماً
         * =====================================================
         * Moyasar يبني الفورم بـ LTR بطبيعته ولازم يفضل كذا.
         * المشكلة الأساسية: الصفحة RTL + الفورم LTR كانت بتخلي
         * الـ click coordinates تتحسب غلط على iOS/Safari.
         */
        .moyasar-form {
            direction: ltr;
        }

        /*
         * =====================================================
         * FIX الحقيقي: Moyasar بيعرض أزرار طرق الدفع كـ
         * .mysr-form-methodButton وكل واحد في div.mysr-form-method
         * Apple Pay بيتعرض كـ button.mysr-form-applePayButton
         * اللي بـ -webkit-appearance: -apple-pay-button
         *
         * المشكلة: الـ .mysr-form-methodButtons بيعرض الأزرار
         * في layout افتراضي فيه احتمال يتداخلوا.
         * الحل: نخلي كل div.mysr-form-method يكون clear تماماً
         * ومفيش أي overlap بين الـ methods.
         * =====================================================
         */

        /* كل method في سطر مستقل وواضح */
        .mysr-form-moyasarForm .mysr-form-method {
            position: relative !important;
            overflow: visible !important;
        }

        /* الأزرار تتراص عمودياً بشكل صريح */
        .mysr-form-moyasarForm .mysr-form-methodButtons {
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
        }

        /* كل زرار method له مساحة واضحة */
        .mysr-form-moyasarForm .mysr-form-methodButton {
            display: block !important;
            width: 100% !important;
            margin-bottom: 0 !important;
        }

        /*
         * Apple Pay button الخاص بـ Moyasar
         * يستخدم -webkit-appearance: -apple-pay-button
         * وليس web component - لازم له مساحة كافية
         */
        .mysr-form-moyasarForm .mysr-form-applePayButton {
            display: block !important;
            width: 100% !important;
            min-height: 50px !important;
            margin: 0 !important;
            cursor: pointer !important;
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
        <div id="moyasar-payment-form"></div>

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
    // ============================================================
    // إعداد Moyasar
    // Apple Pay يشتغل فقط على HTTPS + Safari على iOS/macOS
    // ============================================================
    var paymentMethods = ['creditcard', 'stcpay'];
    var applePayConfig = undefined;

    if (window.location.protocol === 'https:') {
        // Apple Pay أول الـ list حتى يظهر في الأعلى
        paymentMethods = ['applepay', 'creditcard', 'stcpay'];
        applePayConfig = {
            country: 'SA',
            currency: 'SAR',
            label: 'Thamn | ثمن',
            validate_merchant_url: 'https://api.moyasar.com/v1/applepay/initiate'
        };
    }

    Moyasar.init({
        element: '#moyasar-payment-form',
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
    // FIX: منع تبديل طريقة الدفع عرضاً عند الضغط على Apple Pay
    //
    // الوضع الحقيقي (من Moyasar CSS المصدر):
    // - Apple Pay = button.mysr-form-applePayButton بـ
    //   -webkit-appearance: -apple-pay-button  (مش web component!)
    // - يتعرض داخل div.mysr-form-method الخاص بيه
    //
    // المشكلة المحتملة:
    // 1. إن الـ radio labels بتاعة طرق الدفع ممكن تتداخل مع
    //    زرار Apple Pay في الـ layout
    // 2. أو إن event من Apple Pay بيتسرب لـ STC label
    //
    // الحل: نراقب كل تغيير في الـ radio buttons ولو اتغير
    // من applepay لأي طريقة تانية بدون أن يكون click صريح
    // على الـ label بتاعتها، نرجعه لـ applepay.
    // ============================================================
    (function fixApplePaySelection() {
        if (window.location.protocol !== 'https:') return;

        var formEl = document.getElementById('moyasar-payment-form');
        if (!formEl) return;

        var isApplePayLocked = false;
        var lockTimeout = null;

        // راقب الـ DOM لما Moyasar يعمل render للفورم
        var observer = new MutationObserver(function(mutations, obs) {
            // دور على Apple Pay button
            var applePayBtn = formEl.querySelector('.mysr-form-applePayButton');
            if (!applePayBtn) return;

            if (applePayBtn._alreadyFixed) return;
            applePayBtn._alreadyFixed = true;

            console.log('[Moyasar Fix] Apple Pay button found:', applePayBtn);

            // لما تضغط على Apple Pay button:
            // 1. سجل إن Apple Pay محدد
            // 2. امسك أي event تاني خلال 500ms
            applePayBtn.addEventListener('pointerdown', function(e) {
                console.log('[Moyasar Fix] Apple Pay pointerdown');
                isApplePayLocked = true;
                clearTimeout(lockTimeout);
                lockTimeout = setTimeout(function() {
                    isApplePayLocked = false;
                }, 1000);
            }, true);

            applePayBtn.addEventListener('touchstart', function(e) {
                console.log('[Moyasar Fix] Apple Pay touchstart');
                isApplePayLocked = true;
                clearTimeout(lockTimeout);
                lockTimeout = setTimeout(function() {
                    isApplePayLocked = false;
                }, 1000);
            }, true);

            // راقب تغيير الـ radio buttons
            formEl.addEventListener('change', function(e) {
                if (e.target && e.target.type === 'radio' && e.target.value !== 'applepay') {
                    if (isApplePayLocked) {
                        console.log('[Moyasar Fix] Prevented switch from applepay to', e.target.value);
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        // رجّع Apple Pay كـ selected
                        var applePayRadio = formEl.querySelector('input[value="applepay"]');
                        if (applePayRadio) {
                            setTimeout(function() {
                                applePayRadio.click();
                            }, 50);
                        }
                        return false;
                    }
                } else if (e.target && e.target.value === 'applepay') {
                    // Apple Pay اتحدد بشكل صريح - مش locked
                    isApplePayLocked = false;
                }
            }, true);

            // راقب أي click على labels غير Apple Pay
            var allLabels = formEl.querySelectorAll('label');
            allLabels.forEach(function(label) {
                label.addEventListener('click', function(e) {
                    var radio = label.querySelector('input[type="radio"]');
                    if (radio && radio.value !== 'applepay' && isApplePayLocked) {
                        console.log('[Moyasar Fix] Blocked label click for', radio.value);
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                    // لو المستخدم بيختار STC أو creditcard بشكل صريح، مش locked
                    isApplePayLocked = false;
                }, true);
            });
        });

        observer.observe(formEl, {
            childList: true,
            subtree: true
        });
    })();
</script>
</body>
</html>
