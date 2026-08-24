<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ar" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>نتيجة التقييم — تطبيق ثمن</title>
    <style type="text/css">
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

        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: collapse;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            outline: none;
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f0ece4;
            font-family: 'Avenir Arabic', 'Segoe UI', Tahoma, Arial, sans-serif;
        }

        @media only screen and (max-width:620px) {
            .wrap {
                padding: 12px 8px !important;
            }

            .card {
                border-radius: 14px !important;
            }

            .hdr {
                padding: 28px 16px 24px !important;
            }

            .body-pad {
                padding: 20px 16px 24px !important;
            }

            .h1 {
                font-size: 18px !important;
            }

            .price-amt {
                font-size: 34px !important;
            }

            .store-btn {
                display: block !important;
                width: 100% !important;
                margin: 6px 0 !important;
            }

            .store-td {
                display: block !important;
                text-align: center !important;
                padding: 4px 0 !important;
            }
        }
    </style>
</head>

<body style="margin:0;padding:0;background-color:#f0ece4;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f0ece4">
        <tr>
            <td class="wrap" align="center" style="padding:32px 16px;">

                <table role="presentation" class="card" width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.12);">

                    {{-- ===== HEADER ===== --}}
                    <tr>
                        <td class="hdr" align="center"
                            style="background:linear-gradient(135deg,#8B6914 0%,#c1953e 50%,#D4AF37 100%);padding:38px 28px 32px;">
                            <img src="{{ asset('assets/emails/logo_white.png') }}" width="80" alt="ثمن"
                                style="display:block;margin:0 auto 18px;border:0;opacity:.95;">
                            <table cellpadding="0" cellspacing="0" border="0" align="center">
                                <tr>
                                    <td style="width:64px;height:64px;background:rgba(255,255,255,.2);border-radius:50%;
                             text-align:center;vertical-align:middle;font-size:28px;line-height:64px;">✅</td>
                                </tr>
                            </table>
                            <p style="margin:14px 0 0;font-size:11px;font-weight:700;color:rgba(255,255,255,.9);
                        letter-spacing:2.5px;text-transform:uppercase;font-family:'Avenir Arabic',Arial,sans-serif;">
                                نتيجة التقييم — VALUATION RESULT
                            </p>
                        </td>
                    </tr>

                    {{-- Bridge --}}
                    <tr>
                        <td height="16"
                            style="background:linear-gradient(180deg,#c1953e 0%,#c1953e 49%,#ffffff 50%);font-size:0;line-height:0;">
                            &nbsp;</td>
                    </tr>

                    {{-- ===== BODY ===== --}}
                    <tr>
                        <td class="body-pad"
                            style="padding:12px 40px 36px;font-family:'Avenir Arabic',Arial,sans-serif;">

                            {{-- Greeting --}}
                            <h1 class="h1"
                                style="margin:0 0 8px;font-size:20px;font-weight:700;color:#1a1a1a;text-align:right;line-height:1.5;">
                                مرحباً، <span
                                    style="color:#c1953e;">{{ $order->user->first_name ?? 'عميلنا العزيز' }}</span> 👋
                            </h1>
                            <p style="margin:0 0 24px;font-size:14px;color:#6c757d;text-align:right;line-height:1.8;">
                                تم الانتهاء من تقييم منتجك <strong style="color:#2d2d2d;">( {{ $categoryName }}
                                    )</strong> بنجاح.
                                يمكنك الاطلاع على تفاصيل نتيجة التقييم أدناه.
                            </p>

                            {{-- Order Info Box --}}
                            <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="background:#fdfaf4;border:1px solid #ead9b0;border-radius:12px;margin-bottom:18px;">
                                <tr>
                                    <td style="padding:14px 18px;border-bottom:1px solid #f0e8d2;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td
                                                    style="font-size:13px;color:#8a7050;font-weight:700;text-align:right;">
                                                    📦 رقم الطلب:</td>
                                                <td
                                                    style="font-size:14px;color:#2d2d2d;font-weight:700;text-align:left;">
                                                    #{{ $order->id }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td
                                                    style="font-size:13px;color:#8a7050;font-weight:700;text-align:right;">
                                                    🔍 نوع التقييم:</td>
                                                <td style="text-align:left;">
                                                    @if($evaluationType === 'ai')
                                                        <span
                                                            style="display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;background:#e8f4fd;color:#0d6efd;border:1px solid #b6d4fe;">التقييم
                                                            الذكي (AI)</span>
                                                    @elseif($evaluationType === 'expert')
                                                        <span
                                                            style="display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;background:#fff3cd;color:#856404;border:1px solid #ffc107;">تقييم
                                                            خبير موثوق</span>
                                                    @else
                                                        <span
                                                            style="display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;background:#f4ead2;color:#9a7230;border:1px solid #c1953e;">تقييم
                                                            فريق ثمن</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Price Box --}}
                            @if($recommendedPrice)
                                <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                    style="background:linear-gradient(135deg,#f0fff4,#e6f9ed);border:1px solid #b7e4c7;border-radius:14px;margin-bottom:18px;">
                                    <tr>
                                        <td style="padding:24px 20px;text-align:center;">
                                            <p
                                                style="margin:0 0 8px;font-size:13px;color:#198754;font-weight:700;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                💎 السعر العادل المقدر</p>
                                            <p class="price-amt"
                                                style="margin:0;font-size:44px;font-weight:900;color:#157347;line-height:1.1;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                {{ number_format($recommendedPrice, 0) }}
                                                <span
                                                    style="font-size:22px;font-weight:700;color:#198754;margin-right:4px;">ريال</span>
                                            </p>
                                            @if($minPrice && $maxPrice)
                                                <p
                                                    style="margin:12px 0 0;display:inline-block;background:rgba(255,255,255,.7);border:1px solid #c3e6cb;border-radius:20px;padding:4px 16px;font-size:13px;color:#5a8a6a;font-weight:600;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                    نطاق السعر: من {{ number_format($minPrice, 0) }} إلى
                                                    {{ number_format($maxPrice, 0) }} ريال
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Reasoning Box --}}
                            @if($reasoning)
                                <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                    style="background:#fdfaf4;border:1px solid #ead9b0;border-radius:12px;margin-bottom:18px;">
                                    <tr>
                                        <td style="padding:18px 20px;">
                                            <p
                                                style="margin:0 0 10px;font-size:14px;font-weight:700;color:#c1953e;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                📋 ملاحظات التقييم:</p>
                                            <div
                                                style="font-size:14px;color:#4a4a4a;line-height:1.9;text-align:justify;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                @if(!preg_match('/<[a-z][\s\S]*>/i', $reasoning))
                                                    {!! \Illuminate\Support\Str::markdown($reasoning) !!}
                                                @else
                                                    {!! $reasoning !!}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Infographic Cards Grid --}}
                            @php
                                $infCards = is_array($order->ai_features) && isset($order->ai_features[0]['icon'])
                                    ? $order->ai_features
                                    : [];
                                $infPairs = array_chunk($infCards, 2);
                            @endphp

                            @if(count($infCards) > 0)
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:18px;">
                                    <tr>
                                        <td style="padding-bottom:10px;">
                                            <p
                                                style="margin:0 0 12px;font-size:14px;font-weight:700;color:#c1953e;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                🔎 تحليل تفصيلي للسلعة:
                                            </p>
                                        </td>
                                    </tr>
                                    @foreach($infPairs as $pair)
                                        <tr>
                                            @foreach($pair as $card)
                                                <td width="50%" style="padding:4px 4px 4px 4px;vertical-align:top;">
                                                    <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                                        style="background:#fdfaf4;border:1px solid #ead9b0;border-radius:12px;">
                                                        <tr>
                                                            <td style="padding:14px 12px;text-align:center;">
                                                                <div style="font-size:26px;line-height:1;margin-bottom:6px;">
                                                                    {{ $card['icon'] ?? '📌' }}</div>
                                                                <div
                                                                    style="font-size:10px;color:#a08050;font-weight:700;margin-bottom:4px;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                                    {{ $card['title'] ?? '' }}</div>
                                                                <div
                                                                    style="font-size:14px;font-weight:900;color:#1a1a1a;margin-bottom:4px;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                                    {{ $card['value'] ?? '—' }}</div>
                                                                @if(!empty($card['description']))
                                                                    <div
                                                                        style="font-size:10px;color:#999;line-height:1.4;font-family:'Avenir Arabic',Arial,sans-serif;">
                                                                        {{ $card['description'] }}</div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            @endforeach
                                            {{-- Fill empty cell if odd number --}}
                                            @if(count($pair) === 1)
                                                <td width="50%" style="padding:4px;"></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            {{-- Divider --}}
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:22px 0;">
                                <tr>
                                    <td height="1"
                                        style="background:linear-gradient(to left,transparent,#e0d0b0,transparent);font-size:0;line-height:0;">
                                        &nbsp;</td>
                                </tr>
                            </table>

                            {{-- App Store Buttons --}}
                            <p
                                style="margin:0 0 16px;font-size:14px;font-weight:700;color:#2d2d2d;text-align:center;font-family:'Avenir Arabic',Arial,sans-serif;">
                                🚀 تابع تفاصيل طلبك من خلال التطبيق
                            </p>

                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto;">
                                <tr>
                                    {{-- App Store Button --}}
                                    <td class="store-td" style="padding:0 8px;">
                                        <a href="https://apps.apple.com/eg/app/thmmn-%D8%AB%D9%85%D9%86/id6758052199"
                                            style="display:inline-block;text-decoration:none;">
                                            <table cellpadding="0" cellspacing="0" border="0"
                                                style="background:#000000;border-radius:12px;min-width:150px;">
                                                <tr>
                                                    <td style="padding:10px 18px;">
                                                        <table cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td style="padding-left:10px;vertical-align:middle;"
                                                                    align="left">
                                                                    {{-- Apple SVG Icon --}}
                                                                    <svg width="22" height="26" viewBox="0 0 814 1000"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill="#ffffff"
                                                                            d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663 0 541.8c0-207.8 135.4-317.7 268.5-317.7 99.8 0 184 65.6 245.7 65.6 57.4 0 147.6-69.7 261.3-69.7 4.9 0 2.6-.3 12.6.3zm-166.4-207.1c51.4-61.4 88.1-147.7 88.1-234 0-12.2-.6-24.5-2.6-35.4-84.4 3.2-183.1 56.6-242.6 126.1-41.1 46.4-81.1 132.7-81.1 220.1 0 13.6 2.6 27.2 3.9 31.5 5.2.6 13.6 1.9 21.9 1.9 76 0 166.3-50.8 212.4-110.2z" />
                                                                    </svg>
                                                                </td>
                                                                <td style="padding-right:10px;vertical-align:middle;">
                                                                    <p
                                                                        style="margin:0;font-size:9px;color:rgba(255,255,255,.8);font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                                                        Download on the</p>
                                                                    <p
                                                                        style="margin:0;font-size:16px;color:#ffffff;font-weight:bold;font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                                                        App Store</p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </a>
                                    </td>

                                    {{-- Google Play Button --}}
                                    <td class="store-td" style="padding:0 8px;">
                                        <a href="https://play.google.com/store/apps/details?id=com.thamin.thamin&hl=ar"
                                            style="display:inline-block;text-decoration:none;">
                                            <table cellpadding="0" cellspacing="0" border="0"
                                                style="background:#000000;border-radius:12px;min-width:150px;">
                                                <tr>
                                                    <td style="padding:10px 18px;">
                                                        <table cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td style="padding-left:10px;vertical-align:middle;"
                                                                    align="left">
                                                                    {{-- Google Play SVG Icon --}}
                                                                    <svg width="22" height="24" viewBox="0 0 512 512"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill="#4CAF50"
                                                                            d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1z" />
                                                                        <path fill="#FF5722"
                                                                            d="M47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l2.7 1.5 247.2-247v-5.8L47 0z" />
                                                                        <path fill="#FFC107"
                                                                            d="M330.1 281.1l-82.1-82.2V194l82.1 82.2 48.1-27.3 50 28.4-48.1 27.4-50-23.6z" />
                                                                        <path fill="#4CAF50"
                                                                            d="M330.1 234.3l50 28.5L104.6 499l225.5-264.7z" />
                                                                        <path fill="#FF5722"
                                                                            d="M379.6 261.3l-49.5-27.4-225.5 265 275-237.6z" />
                                                                    </svg>
                                                                </td>
                                                                <td style="padding-right:10px;vertical-align:middle;">
                                                                    <p
                                                                        style="margin:0;font-size:9px;color:rgba(255,255,255,.8);font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                                                        GET IT ON</p>
                                                                    <p
                                                                        style="margin:0;font-size:16px;color:#ffffff;font-weight:bold;font-family:Arial,sans-serif;line-height:1.2;white-space:nowrap;">
                                                                        Google Play</p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- ===== FOOTER ===== --}}
                    <tr>
                        <td style="background:#1a1a2e;padding:24px 40px;border-radius:0 0 20px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="right" valign="middle">
                                        <img src="{{ asset('assets/emails/logo_white.png') }}" width="52" alt="ثمن"
                                            style="display:block;border:0;opacity:.85;">
                                        <p
                                            style="margin:6px 0 0;font-size:11px;color:#c1953e;font-family:'Avenir Arabic',Arial,sans-serif;">
                                            تطبيق ثمن — خيارك الأول للتقييم
                                        </p>
                                    </td>
                                    <td align="left" valign="middle">
                                        <p
                                            style="margin:0;font-size:11px;color:#666;text-align:left;font-family:Arial,sans-serif;line-height:1.7;">
                                            &copy; {{ date('Y') }} Thamn<br>
                                            <span style="font-size:10px;color:#444;">جميع الحقوق محفوظة</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>