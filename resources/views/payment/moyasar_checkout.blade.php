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
            gap: 14px !important;
            margin: 0 0 20px 0 !important;
            padding: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
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
            margin: 0 0 14px 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            position: relative !important;
            isolation: isolate !important;
            contain: layout !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons .mysr-form-methodButton:last-of-type,
        div.mysr-form-methodButtons .mysr-form-methodButton:last-of-type {
            margin-bottom: 0 !important;
        }

        /* 1. زر Apple Pay - متطابق 100% في الطول والعرض والحواف */
        apple-pay-button,
        .mysr-form-applePayButton,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-applePayButton {
            display: block !important;
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 48px !important;
            min-height: 48px !important;
            max-height: 48px !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 12px !important;
            border: 2px solid #000000 !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12) !important;
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.22) !important;
        }

        apple-pay-button:active,
        .mysr-form-applePayButton:active,
        .mysr-form-applePayButton.selected-payment,
        apple-pay-button.selected-payment {
            transform: scale(0.98) !important;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.3) !important;
            border-color: #000 !important;
        }

        /* 2. زر STC Pay - متطابق 100% في الطول والعرض والحواف مع Apple Pay */
        .mysr-form-stcPayButton,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton,
        button.mysr-form-stcPayButton {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 48px !important;
            min-height: 48px !important;
            max-height: 48px !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 12px !important;
            background-color: #5f1896 !important;
            border: 2px solid #4a1077 !important;
            box-shadow: 0 3px 10px rgba(95, 24, 150, 0.25) !important;
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
            box-shadow: 0 5px 15px rgba(95, 24, 150, 0.4) !important;
        }

        .mysr-form-stcPayButton:active,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton:active,
        .mysr-form-stcPayButton.selected-payment {
            transform: scale(0.98) !important;
            box-shadow: 0 0 0 3px rgba(95, 24, 150, 0.4) !important;
            border-color: #4a1077 !important;
        }

        /* شعار STC Pay داخل الزر: متطابق بصرياً 100% مع حجم وأبعاد أيقونة Apple Pay */
        .mysr-form-stcPayButton .mysr-form-stcPayLogo {
            display: none !important;
        }

        .mysr-form-stcPayButton > *:not(:first-child) {
            display: none !important;
        }

        #mysr-form-form-el#mysr-form-form-el#mysr-form-form-el.mysr-form-moyasarForm[payment-form=true] .mysr-form-stcPayLogoLight,
        .mysr-form-stcPayButton > *:first-child,
        .mysr-form-stcPayButton .mysr-form-stcPayLogoLight {
            display: block !important;
            box-sizing: border-box !important;
            width: 60px !important;
            min-width: 60px !important;
            max-width: 60px !important;
            height: 18px !important;
            min-height: 18px !important;
            max-height: 18px !important;
            padding: 0 !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            margin: 0 auto !important;
            pointer-events: none !important;
            flex-shrink: 0 !important;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 450 133'%3E%3Cpath d='M291.862,97.715l0,-69.89l11.852,0l0,5.604c3.607,-4.509 8.954,-6.957 15.588,-6.957c13.656,0 23.447,10.886 23.447,26.474c0,15.588 -9.855,26.668 -23.447,26.668c-6.699,0 -11.981,-2.577 -15.588,-7.086l0,25.186l-11.852,0l0,0.001Zm11.853,-51.209l-0,12.883c2.512,5.218 7.021,9.018 13.527,9.018c7.859,0 13.141,-5.862 13.141,-15.524c-0,-9.533 -5.282,-15.331 -13.141,-15.331c-6.506,0.064 -11.015,3.607 -13.527,8.954' fill='%2302aa7c'/%3E%3Cpath d='M350.415,63.769c0,-8.503 5.282,-14.429 14.042,-15.524l20.162,-2.577l0,-2.512c0,-3.027 -1.546,-4.638 -4.38,-4.638l-25.315,0l0,-10.757l25.766,0c10.242,0 15.846,5.024 15.846,13.914l0,36.394l-11.981,0l0,-6.119c-3.478,4.896 -9.211,7.665 -16.619,7.665c-10.435,0 -17.521,-6.377 -17.521,-15.846m20.419,6.183c6.055,0 11.659,-3.414 13.72,-8.438l0,-6.313l-15.395,2.126c-4.251,0.58 -6.635,2.834 -6.635,6.506c0.001,3.929 3.028,6.119 8.31,6.119' fill='%2302aa7c'/%3E%3Cpath d='M405.167,86.958l8.632,0c3.285,0 4.767,-1.353 5.862,-4.445l1.546,-4.445l-18.294,-50.308l12.368,0l11.401,35.042l0.258,0l10.886,-35.042l12.174,0l-20.033,57.78c-2.705,7.923 -6.313,12.174 -15.137,12.174l-9.662,0l-0.001,-10.756Z' fill='%2302aa7c'/%3E%3Cpath d='M43.48,132.306c13.785,0 25.122,-4.316 32.594,-11.53c5.604,-5.54 8.889,-13.012 8.889,-21.643c0,-7.794 -2.899,-14.815 -8.245,-20.162c-5.346,-5.347 -13.012,-9.276 -22.674,-11.144l-15.846,-3.092c-6.57,-1.224 -10.306,-4.509 -10.306,-9.082c0,-5.991 5.797,-10.113 15.073,-10.113c5.797,0 10.757,1.868 14.042,5.153c2.061,2.255 3.478,5.153 3.929,8.438l22.867,-5.153c-0.644,-6.57 -3.736,-12.368 -8.438,-17.134c-7.536,-7.408 -19.067,-12.11 -32.658,-12.11c-12.561,0 -23.06,4.123 -30.339,10.693c-6.184,5.797 -9.662,13.591 -9.662,22.287c0,7.601 2.448,14.042 7.408,18.938c4.96,4.96 12.174,8.632 21.643,10.95l15.653,3.736c7.859,1.868 11.337,4.767 11.337,9.92c0,6.377 -5.797,10.113 -15.266,10.113c-6.828,0 -12.368,-2.255 -15.846,-5.991c-2.448,-2.448 -3.929,-5.797 -4.123,-9.469l-23.512,5.154c0.644,7.021 3.929,13.205 8.889,18.165c7.794,8.18 20.42,13.076 34.591,13.076m174.048,0c15.266,0 26.99,-5.54 35.041,-13.398c6.377,-6.184 10.306,-13.398 12.368,-20.806l-24.091,-8.052c-1.031,3.736 -3.092,7.665 -6.377,10.693c-3.929,3.736 -9.276,6.377 -16.941,6.377c-7.021,0 -13.591,-2.705 -18.358,-7.408c-4.767,-4.96 -7.665,-12.174 -7.665,-21.257c-0,-9.276 2.899,-16.297 7.665,-21.257c4.702,-4.767 11.144,-7.214 18.165,-7.214c7.408,-0 12.561,2.448 16.297,6.184c3.092,3.092 4.96,7.021 6.184,10.95l24.542,-8.245c-1.868,-7.214 -5.797,-14.429 -11.53,-20.419c-8.245,-8.052 -20.226,-13.785 -36.072,-13.785c-14.622,-0 -27.827,5.54 -37.296,15.073c-9.469,9.662 -15.266,23.125 -15.266,38.778c0,15.653 5.991,29.051 15.653,38.778c9.403,9.468 22.801,15.008 37.681,15.008m-83.288,0c10.693,0 18.358,-3.285 21.643,-6.184l0,-22.287c-2.512,1.868 -7.408,4.123 -13.849,4.123c-4.573,0 -7.859,-1.031 -10.306,-3.285c-2.061,-2.061 -3.092,-5.604 -3.092,-10.306l0,-94.367l-27.247,0l0,27.827l54.43,0l0,26.41l-54.43,0l0,46.829c0,9.469 2.899,17.134 8.052,22.481c5.797,5.667 14.299,8.759 24.799,8.759' fill='%23fff'/%3E%3C/svg%3E") !important;
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
                    طلب رقم {{ $order->id }}
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
                label: 'Thmmn',
                validate_merchant_url: 'https://api.moyasar.com/v1/applepay/initiate'
            };
        }

        Moyasar.init({
            element: '.moyasar-form',
            amount: {{ (int) round($order->total_price * 100) }},
            currency: 'SAR',
            description: 'طلب تثمين رقم {{ $order->id }}',
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
