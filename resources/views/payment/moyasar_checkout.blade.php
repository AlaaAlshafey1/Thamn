<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الدفع - ثمن</title>

    <style>
        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Light1.otf") }}') format('opentype');
            font-weight: 300;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Book.otf") }}') format('opentype');
            font-weight: 400;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Medium.otf") }}') format('opentype');
            font-weight: 500;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Heavy.otf") }}') format('opentype');
            font-weight: 700;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Black.otf") }}') format('opentype');
            font-weight: 900;
        }
    </style>
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
            font-family: 'Avenir Arabic', -apple-system, BlinkMacSystemFont, sans-serif;
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

        /* Moyasar Form Overrides & Method Buttons Isolation */
        .moyasar-form {
            direction: ltr;
            unicode-bidi: isolate;
            -webkit-tap-highlight-color: transparent !important;
        }

        /* حاوية أزرار الدفع السريع (Apple Pay و STC Pay) */
        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons,
        div.mysr-form-methodButtons {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
            margin-bottom: 24px !important;
            padding: 2px !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        /* عنوان واضح ومميز يوجه المستخدم للاختيار */
        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons::before {
            content: "اختر وسيلة الدفع السريع:";
            display: block;
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            margin-bottom: 4px;
            text-align: right;
            direction: rtl;
        }

        /* كل زر في حاوية منعزلة ومتباعدة تماماً لمنع التداخل والضغط المزدوج */
        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons .mysr-form-methodButton,
        div.mysr-form-methodButtons .mysr-form-methodButton {
            margin-bottom: 16px !important;
            -webkit-margin-after: 16px !important;
            margin-block-end: 16px !important;
            width: 100% !important;
            position: relative !important;
            isolation: isolate !important;
            contain: layout !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons .mysr-form-methodButton:last-of-type,
        div.mysr-form-methodButtons .mysr-form-methodButton:last-of-type {
            margin-bottom: 0 !important;
            -webkit-margin-after: 0 !important;
            margin-block-end: 0 !important;
        }

        /* 1. زر Apple Pay - حواف واضحة وظل وتأثير اختيار مميز */
        apple-pay-button,
        .mysr-form-applePayButton,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-applePayButton {
            display: block !important;
            width: 100% !important;
            height: 52px !important;
            min-height: 52px !important;
            border-radius: 14px !important;
            border: 2.5px solid #000000 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
            -webkit-tap-highlight-color: transparent !important;
            transition: all 0.2s ease !important;
        }

        apple-pay-button:hover,
        .mysr-form-applePayButton:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.22) !important;
        }

        apple-pay-button:active,
        .mysr-form-applePayButton:active,
        .mysr-form-applePayButton.selected-payment,
        apple-pay-button.selected-payment {
            transform: scale(0.98) !important;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.3) !important;
            border-color: #000 !important;
        }

        /* 2. زر STC Pay - لون بنفسجي وحواف بارزة وظل مستقل */
        .mysr-form-stcPayButton,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton,
        button.mysr-form-stcPayButton {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 52px !important;
            min-height: 52px !important;
            border-radius: 14px !important;
            background-color: #5f1896 !important;
            border: 2.5px solid #4a1077 !important;
            box-shadow: 0 4px 14px rgba(95, 24, 150, 0.25) !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
            -webkit-tap-highlight-color: transparent !important;
            transition: all 0.2s ease !important;
        }

        .mysr-form-stcPayButton:hover,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton:hover {
            transform: translateY(-2px) !important;
            background-color: #50137f !important;
            box-shadow: 0 6px 20px rgba(95, 24, 150, 0.4) !important;
        }

        .mysr-form-stcPayButton:active,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton:active,
        .mysr-form-stcPayButton.selected-payment {
            transform: scale(0.98) !important;
            box-shadow: 0 0 0 4px rgba(95, 24, 150, 0.4) !important;
            border-color: #4a1077 !important;
        }

        /* منع التظليل العشوائي في متصفحات الجوال */
        #mysr-form-form-el *,
        .moyasar-form * {
            -webkit-tap-highlight-color: transparent !important;
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== FIX: STC Pay phone input direction =====
         * حقل رقم الجوال في STC Pay يجب أن يظهر الأرقام من اليسار
         * بدون عكس الأرقام أو المحتوى
         */
        .mysr-form input[type="tel"],
        .mysr-form input[type="text"],
        .mysr-form input[name*="phone"],
        .mysr-form input[name*="mobile"],
        .mysr-form input[placeholder*="05"],
        .mysr-form input[placeholder*="5"] {
            direction: ltr !important;
            text-align: left !important;
            unicode-bidi: plaintext !important;
            font-family: 'Avenir Arabic', monospace !important;
            letter-spacing: 1px;
        }

        /* OTP / verification code inputs - أرقام فقط من اليسار */
        .mysr-form input[type="number"],
        .mysr-form input[inputmode="numeric"],
        .mysr-form [class*="otp"] input,
        .mysr-form [class*="code"] input,
        .mysr-form [class*="verify"] input {
            direction: ltr !important;
            text-align: center !important;
            font-size: 22px !important;
            letter-spacing: 4px !important;
            font-family: 'Avenir Arabic', monospace !important;
        }

        /* label للـ stc pay - محاذاة صحيحة */
        .mysr-form label[for*="stc"],
        .mysr-form [class*="stcpay"] label {
            direction: ltr !important;
            text-align: left !important;
        }
    </style>
</head>

<body>

    <div class="checkout-wrapper">
        <div class="payment-card">

            <!-- Header -->
            <div class="header">
                <img src="{{ asset('assets/img/Logo-black.png') }}" alt="ثمن">
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
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
            var formEl = document.querySelector('.moyasar-form');
            if (!formEl) return;

            // تمييز بصري فوري للزر المختار عند الضغط لمنع اللبس
            document.addEventListener('click', function (e) {
                var appleBtn = e.target.closest('apple-pay-button, .mysr-form-applePayButton');
                var stcBtn = e.target.closest('.mysr-form-stcPayButton');
                var cardFields = e.target.closest('.mysr-form-cardInfo, .mysr-form-inputGroup');

                if (appleBtn) {
                    appleBtn.classList.add('selected-payment');
                    var stc = formEl.querySelector('.mysr-form-stcPayButton');
                    if (stc) stc.classList.remove('selected-payment');
                } else if (stcBtn) {
                    stcBtn.classList.add('selected-payment');
                    var apple = formEl.querySelector('apple-pay-button, .mysr-form-applePayButton');
                    if (apple) apple.classList.remove('selected-payment');
                } else if (cardFields) {
                    var stc = formEl.querySelector('.mysr-form-stcPayButton');
                    var apple = formEl.querySelector('apple-pay-button, .mysr-form-applePayButton');
                    if (stc) stc.classList.remove('selected-payment');
                    if (apple) apple.classList.remove('selected-payment');
                }
            }, true);

            if (window.location.protocol !== 'https:') return;

            var observer = new MutationObserver(function () {
                var applePayBtn = formEl.querySelector('apple-pay-button');
                if (!applePayBtn || applePayBtn._fixedClickThrough) return;

                applePayBtn._fixedClickThrough = true;

                // منع الـ click من الوصول للعناصر الأخرى (stopPropagation)
                applePayBtn.addEventListener('click', function (e) {
                    e.stopImmediatePropagation();
                }, true);

                // تأكد إن الـ radio button بتاع Apple Pay محدد
                var applePayRadio = formEl.querySelector('input[value="applepay"]');
                if (applePayRadio && !applePayRadio.checked) {
                    applePayRadio.checked = true;
                    applePayRadio.dispatchEvent(new Event('change', { bubbles: true }));
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