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
            font-display: swap;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Book.otf") }}') format('opentype');
            font-weight: 400;
            font-display: swap;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Medium.otf") }}') format('opentype');
            font-weight: 500;
            font-display: swap;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Heavy.otf") }}') format('opentype');
            font-weight: 700;
            font-display: swap;
        }

        @font-face {
            font-family: 'Avenir Arabic';
            src: url('{{ asset("assets/fonts/AvenirArabic/AvenirArabic-Black.otf") }}') format('opentype');
            font-weight: 900;
            font-display: swap;
        }
    </style>
    <!-- Moyasar Payment Form CSS -->
    <link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.14.0/moyasar.css">

    <style>
        :root {
            --navy: #0b1f3a;
            --navy-2: #14305a;
            --bg: #f3f5f8;
            --card: #ffffff;
            --text: #334155;
            --muted: #6b7a90;
            --line: #e3e8ef;
            --ok: #0f7b52;
            --focus: #2457c5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Avenir Arabic', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ---------- Header ---------- */
        .top {
            background: var(--card);
            border-bottom: 1px solid var(--line);
        }

        .top-in {
            max-width: 960px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top-in img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .secure {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--ok);
        }

        .secure svg {
            width: 16px;
            height: 16px;
        }

        /* ---------- Layout ---------- */
        main {
            flex: 1;
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            padding: 40px 24px;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 24px;
            align-items: start;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(11, 31, 58, .04), 0 8px 24px -12px rgba(11, 31, 58, .12);
            padding: 28px;
        }

        .panel h2 {
            font-size: 15px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 20px;
        }

        /* ---------- Order summary ---------- */
        .total-label {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .total {
            display: flex;
            align-items: baseline;
            gap: 8px;
            font-size: 38px;
            font-weight: 800;
            line-height: 1.1;
            color: var(--navy);
            font-variant-numeric: tabular-nums;
        }

        .total small {
            font-size: 16px;
            font-weight: 500;
            color: var(--muted);
        }

        .rows {
            margin-top: 24px;
            border-top: 1px solid var(--line);
        }

        .rows div {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--line);
            font-size: 14px;
        }

        .rows span {
            color: var(--muted);
        }

        .rows b {
            font-weight: 500;
            color: var(--navy);
            font-variant-numeric: tabular-nums;
        }

        .rows .sum {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .rows .sum b {
            font-weight: 800;
        }

        /* ---------- Moyasar form ---------- */
        .moyasar-form {
            direction: ltr;
            unicode-bidi: isolate;
            -webkit-tap-highlight-color: transparent !important;
        }

        #mysr-form-form-el *,
        .moyasar-form * {
            -webkit-tap-highlight-color: transparent !important;
        }

        /* أزرار الدفع السريع: كل زر بعرض كامل (نفس ترتيب النسخة الأصلية) */
        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons,
        div.mysr-form-methodButtons {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            margin-bottom: 20px !important;
            padding: 2px !important;
        }

        #mysr-form-form-el.mysr-form-moyasarForm div.mysr-form-methodButtons .mysr-form-methodButton,
        div.mysr-form-methodButtons .mysr-form-methodButton {
            margin: 0 !important;
            width: 100% !important;
            position: relative !important;
            isolation: isolate !important;
            contain: layout !important;
        }

        /* Apple Pay */
        apple-pay-button,
        .mysr-form-applePayButton,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-applePayButton {
            display: block !important;
            width: 100% !important;
            height: 48px !important;
            min-height: 48px !important;
            border-radius: 10px !important;
            border: 0 !important;
            box-shadow: none !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
            transition: transform .15s ease, box-shadow .15s ease !important;
        }

        /* STC Pay */
        .mysr-form-stcPayButton,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton,
        button.mysr-form-stcPayButton {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 48px !important;
            min-height: 48px !important;
            border-radius: 10px !important;
            background-color: #5f1896 !important;
            border: 0 !important;
            box-shadow: none !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
            transition: transform .15s ease, background-color .15s ease !important;
            padding: 0 !important;
        }

        .mysr-form-stcPayButton:hover,
        #mysr-form-form-el.mysr-form-moyasarForm .mysr-form-stcPayButton:hover {
            background-color: #50137f !important;
        }

        /* تأكيد الاختيار: حلقة رفيعة */
        apple-pay-button:active,
        .mysr-form-applePayButton:active,
        .mysr-form-applePayButton.selected-payment,
        apple-pay-button.selected-payment,
        .mysr-form-stcPayButton:active,
        .mysr-form-stcPayButton.selected-payment {
            transform: scale(.985) !important;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--navy) !important;
        }

        /* شعار STC Pay داخل الزر: إظهار لوجو واحد فقط وإخفاء أي لوجو مكرر */
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
            width: 105px !important;
            min-width: 105px !important;
            max-width: 105px !important;
            height: 30px !important;
            min-height: 30px !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            margin: 0 auto !important;
            pointer-events: none !important;
            flex-shrink: 0 !important;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 450 133'%3E%3Cpath d='M291.862,97.715l0,-69.89l11.852,0l0,5.604c3.607,-4.509 8.954,-6.957 15.588,-6.957c13.656,0 23.447,10.886 23.447,26.474c0,15.588 -9.855,26.668 -23.447,26.668c-6.699,0 -11.981,-2.577 -15.588,-7.086l0,25.186l-11.852,0l0,0.001Zm11.853,-51.209l-0,12.883c2.512,5.218 7.021,9.018 13.527,9.018c7.859,0 13.141,-5.862 13.141,-15.524c-0,-9.533 -5.282,-15.331 -13.141,-15.331c-6.506,0.064 -11.015,3.607 -13.527,8.954' fill='%2302aa7c'/%3E%3Cpath d='M350.415,63.769c0,-8.503 5.282,-14.429 14.042,-15.524l20.162,-2.577l0,-2.512c0,-3.027 -1.546,-4.638 -4.38,-4.638l-25.315,0l0,-10.757l25.766,0c10.242,0 15.846,5.024 15.846,13.914l0,36.394l-11.981,0l0,-6.119c-3.478,4.896 -9.211,7.665 -16.619,7.665c-10.435,0 -17.521,-6.377 -17.521,-15.846m20.419,6.183c6.055,0 11.659,-3.414 13.72,-8.438l0,-6.313l-15.395,2.126c-4.251,0.58 -6.635,2.834 -6.635,6.506c0.001,3.929 3.028,6.119 8.31,6.119' fill='%2302aa7c'/%3E%3Cpath d='M405.167,86.958l8.632,0c3.285,0 4.767,-1.353 5.862,-4.445l1.546,-4.445l-18.294,-50.308l12.368,0l11.401,35.042l0.258,0l10.886,-35.042l12.174,0l-20.033,57.78c-2.705,7.923 -6.313,12.174 -15.137,12.174l-9.662,0l-0.001,-10.756Z' fill='%2302aa7c'/%3E%3Cpath d='M43.48,132.306c13.785,0 25.122,-4.316 32.594,-11.53c5.604,-5.54 8.889,-13.012 8.889,-21.643c0,-7.794 -2.899,-14.815 -8.245,-20.162c-5.346,-5.347 -13.012,-9.276 -22.674,-11.144l-15.846,-3.092c-6.57,-1.224 -10.306,-4.509 -10.306,-9.082c0,-5.991 5.797,-10.113 15.073,-10.113c5.797,0 10.757,1.868 14.042,5.153c2.061,2.255 3.478,5.153 3.929,8.438l22.867,-5.153c-0.644,-6.57 -3.736,-12.368 -8.438,-17.134c-7.536,-7.408 -19.067,-12.11 -32.658,-12.11c-12.561,0 -23.06,4.123 -30.339,10.693c-6.184,5.797 -9.662,13.591 -9.662,22.287c0,7.601 2.448,14.042 7.408,18.938c4.96,4.96 12.174,8.632 21.643,10.95l15.653,3.736c7.859,1.868 11.337,4.767 11.337,9.92c0,6.377 -5.797,10.113 -15.266,10.113c-6.828,0 -12.368,-2.255 -15.846,-5.991c-2.448,-2.448 -3.929,-5.797 -4.123,-9.469l-23.512,5.154c0.644,7.021 3.929,13.205 8.889,18.165c7.794,8.18 20.42,13.076 34.591,13.076m174.048,0c15.266,0 26.99,-5.54 35.041,-13.398c6.377,-6.184 10.306,-13.398 12.368,-20.806l-24.091,-8.052c-1.031,3.736 -3.092,7.665 -6.377,10.693c-3.929,3.736 -9.276,6.377 -16.941,6.377c-7.021,0 -13.591,-2.705 -18.358,-7.408c-4.767,-4.96 -7.665,-12.174 -7.665,-21.257c-0,-9.276 2.899,-16.297 7.665,-21.257c4.702,-4.767 11.144,-7.214 18.165,-7.214c7.408,-0 12.561,2.448 16.297,6.184c3.092,3.092 4.96,7.021 6.184,10.95l24.542,-8.245c-1.868,-7.214 -5.797,-14.429 -11.53,-20.419c-8.245,-8.052 -20.226,-13.785 -36.072,-13.785c-14.622,-0 -27.827,5.54 -37.296,15.073c-9.469,9.662 -15.266,23.125 -15.266,38.778c0,15.653 5.991,29.051 15.653,38.778c9.403,9.468 22.801,15.008 37.681,15.008m-83.288,0c10.693,0 18.358,-3.285 21.643,-6.184l0,-22.287c-2.512,1.868 -7.408,4.123 -13.849,4.123c-4.573,0 -7.859,-1.031 -10.306,-3.285c-2.061,-2.061 -3.092,-5.604 -3.092,-10.306l0,-94.367l-27.247,0l0,27.827l54.43,0l0,26.41l-54.43,0l0,46.829c0,9.469 2.899,17.134 8.052,22.481c5.797,5.667 14.299,8.759 24.799,8.759' fill='%23fff'/%3E%3C/svg%3E") !important;
        }

        /* حقول البطاقة */
        .moyasar-form .mysr-form-input,
        .moyasar-form input[type="text"],
        .moyasar-form input[type="tel"],
        .moyasar-form input[type="number"] {
            height: 46px !important;
            border: 1px solid #cfd7e3 !important;
            border-radius: 8px !important;
            background: #fff !important;
            padding: 0 14px !important;
            font-size: 15px !important;
            color: var(--navy) !important;
            box-shadow: none !important;
            transition: border-color .15s ease, box-shadow .15s ease !important;
        }

        .moyasar-form input:focus {
            outline: none !important;
            border-color: var(--focus) !important;
            box-shadow: 0 0 0 3px rgba(36, 87, 197, .15) !important;
        }

        .moyasar-form label {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: var(--text) !important;
        }

        /* زر الدفع النهائي */
        .moyasar-form .mysr-form-button,
        .moyasar-form button[type="submit"] {
            height: 50px !important;
            border-radius: 10px !important;
            border: 0 !important;
            background: var(--navy) !important;
            color: #fff !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            box-shadow: none !important;
            transition: background .15s ease !important;
        }

        .moyasar-form .mysr-form-button:hover:not(:disabled),
        .moyasar-form button[type="submit"]:hover:not(:disabled) {
            background: var(--navy-2) !important;
        }

        .moyasar-form .mysr-form-button:disabled {
            opacity: .55 !important;
            cursor: not-allowed !important;
        }

        /* ---------- Trust ---------- */
        .brands {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
        }

        .brands span {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 4px 9px;
        }

        .note {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-top: 12px;
        }

        footer {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            padding: 0 24px 28px;
        }

        /* ===== FIX: STC Pay phone input direction (كما هو) ===== */
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

        .mysr-form label[for*="stc"],
        .mysr-form [class*="stcpay"] label {
            direction: ltr !important;
            text-align: left !important;
        }

        @media (prefers-reduced-motion: reduce) {
            .moyasar-form * {
                transition: none !important;
            }
        }

        /* ---------- Mobile ---------- */
        @media (max-width: 820px) {
            main {
                grid-template-columns: 1fr;
                padding: 20px 16px;
                gap: 16px;
            }

            .panel {
                padding: 22px 20px;
            }

            .total {
                font-size: 32px;
            }

            .top-in {
                padding: 12px 16px;
            }
        }
    </style>
</head>

<body>

    <header class="top">
        <div class="top-in">
            <img src="{{ asset('assets/img/Logo-black.png') }}" alt="ثمن">
            <div class="secure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="4" y="11" width="16" height="10" rx="2"></rect>
                    <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                </svg>
                دفع آمن
            </div>
        </div>
    </header>

    <main>
        <!-- Order Summary -->
        <section class="panel">
            <h2>ملخص الطلب</h2>
            <div class="total-label">المبلغ المستحق</div>
            <div class="total">
                {{ number_format($order->total_price, 2) }}
                <small>ر.س</small>
            </div>
            <div class="rows">
                <div><span>الخدمة</span><b>طلب تثمين</b></div>
                <div><span>رقم الطلب</span><b>#{{ $order->id }}</b></div>
                <div class="sum"><span>الإجمالي</span><b>{{ number_format($order->total_price, 2) }} ر.س</b></div>
            </div>
        </section>

        <!-- Payment -->
        <section class="panel">
            <h2>طريقة الدفع</h2>

            <!-- Moyasar Payment Form -->
            <div class="moyasar-form"></div>

            <div class="brands"><span>mada</span><span>VISA</span><span>Mastercard</span></div>
            <p class="note">تتم معالجة الدفع بأمان عبر ميسر</p>
        </section>
    </main>

    <footer>© ثمن، جميع الحقوق محفوظة</footer>

    <!-- Moyasar Payment Form JS -->
    <script src="https://cdn.moyasar.com/mpf/1.14.0/moyasar.js"></script>

    <script>
        // إعداد طرق الدفع
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

        // FIX: منع تداخل Apple Pay مع STC Pay + ضمان شعار STC Pay (نفس المنطق السابق)
        (function fixApplePayClickThrough() {
            var formEl = document.querySelector('.moyasar-form');
            if (!formEl) return;

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

            var observer = new MutationObserver(function () {
                // 1. عزل وتثبيت زر Apple Pay
                if (window.location.protocol === 'https:') {
                    var applePayBtn = formEl.querySelector('apple-pay-button');
                    if (applePayBtn && !applePayBtn._fixedClickThrough) {
                        applePayBtn._fixedClickThrough = true;

                        applePayBtn.addEventListener('click', function (e) {
                            e.stopImmediatePropagation();
                        }, true);

                        var applePayRadio = formEl.querySelector('input[value="applepay"]');
                        if (applePayRadio && !applePayRadio.checked) {
                            applePayRadio.checked = true;
                            applePayRadio.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
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