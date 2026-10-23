<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>فاتورة تثمين رقم {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin-top: 0mm;
            margin-bottom: 8mm;
            margin-left: 0mm;
            margin-right: 0mm;
        }

        body {
            font-family: 'ibmplexsansarabic', 'tajawal', 'cairo', 'dejavusans', sans-serif;
            direction: rtl;
            font-size: 11px;
            color: #1F2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
        }

        /* Top Dark Header */
        .header-bar {
            background-color: #162221;
            width: 100%;
            padding: 14px 26px;
        }

        .gold-stripe {
            height: 4px;
            background-color: #C1953E;
            width: 100%;
            margin-bottom: 14px;
        }

        /* Content Container */
        .content-container {
            padding: 0 26px 10px;
        }

        /* 4-Column Metadata Card */
        .meta-card {
            width: 100%;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            background-color: #FFFFFF;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .meta-cell {
            padding: 7px 10px;
            text-align: center;
            vertical-align: middle;
            border-left: 1px solid #E5E7EB;
        }

        .meta-cell-last {
            padding: 7px 10px;
            text-align: center;
            vertical-align: middle;
        }

        .meta-lbl {
            font-size: 9px;
            color: #6B7280;
            margin-bottom: 2px;
        }

        .meta-val {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }

        /* Hero Product & Valuation Section */
        .hero-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .product-title {
            font-size: 17px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 3px;
            line-height: 1.25;
        }

        .product-subtitle {
            font-size: 10px;
            color: #4B5563;
            margin-bottom: 6px;
        }

        .tag-pill {
            display: inline-block;
            padding: 2px 7px;
            margin-left: 4px;
            margin-bottom: 3px;
            background-color: #F3F4F6;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 8.5px;
            color: #4B5563;
        }

        /* Prominent Service Price Card */
        .price-card {
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            padding: 8px 12px;
            background-color: #FFFFFF;
            margin-top: 8px;
        }

        .price-num {
            font-size: 26px;
            font-weight: bold;
            color: #111827;
            line-height: 1;
        }

        .price-unit {
            font-size: 10.5px;
            color: #4B5563;
            font-weight: bold;
            margin-right: 3px;
        }

        .image-frame {
            width: 100%;
            height: 145px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            background-color: #FAFAFA;
            text-align: center;
            vertical-align: middle;
        }

        /* Section Headings */
        .sec-header {
            font-size: 12.5px;
            font-weight: bold;
            color: #111827;
            margin-top: 10px;
            margin-bottom: 6px;
        }

        /* Pricing Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border: 1px solid #E5E7EB;
        }

        .items-table th {
            background-color: #162221;
            color: #FFFFFF;
            padding: 6px 10px;
            font-size: 9.5px;
            font-weight: bold;
            border-bottom: 1px solid #162221;
        }

        .items-table td {
            padding: 6px 10px;
            font-size: 9px;
            border-bottom: 1px solid #E5E7EB;
            color: #1F2937;
        }

        .items-table tr.total-row td {
            background-color: #F3F4F6;
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            padding: 7px 10px;
        }

        /* Two-Column Specs & Condition */
        .two-cols-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .sub-sec-title {
            font-size: 11.5px;
            font-weight: bold;
            color: #111827;
            padding-bottom: 4px;
            border-bottom: 2px solid #111827;
            margin-bottom: 5px;
            text-align: center;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table td {
            padding: 4px 6px;
            font-size: 8.5px;
            border-bottom: 1px solid #F0F2F5;
        }

        .detail-lbl {
            color: #6B7280;
            width: 42%;
        }

        .detail-val {
            color: #111827;
            font-weight: bold;
            width: 58%;
        }

        /* Report Box */
        .report-box {
            background-color: #F9FAFB;
            border-right: 4px solid #C1953E;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 9px;
            line-height: 1.6;
            color: #374151;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            font-size: 8.5px;
            color: #6B7280;
            padding-top: 8px;
            border-top: 1px solid #E5E7EB;
            margin-top: 6px;
        }
    </style>
</head>

<body>

    {{-- Top Dark Header --}}
    <div class="header-bar">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="30%" align="right" valign="middle">
                    <div style="font-size: 16px; font-weight: bold; color: #FFFFFF; line-height: 1.2;">فاتورة تثمين</div>
                    <div style="font-size: 9px; color: #9CA3AF; margin-top: 3px;">مواصفات وتسعير</div>
                </td>
                <td width="40%" align="center" valign="middle">
                    <table cellpadding="0" cellspacing="0" border="0" align="center">
                        <tr>
                            <td valign="middle" align="center">
                                @if(file_exists(public_path('assets/emails/logo_white.png')))
                                    <img src="{{ public_path('assets/emails/logo_white.png') }}" height="38" style="display:inline-block; vertical-align:middle;">
                                @elseif(file_exists(public_path('assets/img/brand/logo-white.png')))
                                    <img src="{{ public_path('assets/img/brand/logo-white.png') }}" height="30" style="display:inline-block; vertical-align:middle;">
                                @endif
                                <span style="font-size: 22px; font-weight: bold; color: #FFFFFF; vertical-align:middle; margin-right: 6px;">ثمن</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="padding-top: 2px;">
                                <div style="font-size: 10px; color: #C1953E; font-weight: bold; letter-spacing: 0.5px;">فرص رقمية</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="30%" align="left" valign="middle">
                    <div style="font-size: 9px; color: #9CA3AF;">رقم الفاتورة</div>
                    <div style="font-size: 13px; font-weight: bold; color: #FFFFFF; margin-top: 2px;">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Gold Accent Stripe --}}
    <div class="gold-stripe"></div>

    {{-- Page Body Container --}}
    <div class="content-container">

        {{-- 4-Column Metadata Card --}}
        <table class="meta-card">
            <tr>
                <td class="meta-cell" width="25%">
                    <div class="meta-lbl">رقم البطاقة</div>
                    <div class="meta-val">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td class="meta-cell" width="25%">
                    <div class="meta-lbl">تاريخ الإصدار</div>
                    <div class="meta-val">{{ $order->created_at ? $order->created_at->format('Y/m/d') : now()->format('Y/m/d') }}</div>
                </td>
                <td class="meta-cell" width="25%">
                    <div class="meta-lbl">القسم</div>
                    <div class="meta-val">{{ $category }}</div>
                </td>
                <td class="meta-cell-last" width="25%">
                    <div class="meta-lbl">طالب التثمين</div>
                    <div class="meta-val">{{ $customerName }}</div>
                </td>
            </tr>
        </table>

        {{-- Hero Product & Service Price Section --}}
        <table class="hero-table">
            <tr>
                <td width="65%" valign="top" style="padding-left: 14px;">
                    <div class="product-title">{{ $itemTitle }}</div>
                    <div class="product-subtitle">{{ $itemSubDescription }}</div>

                    @if(!empty($tags))
                        <div style="margin-bottom: 6px;">
                            @foreach($tags as $tag)
                                <span class="tag-pill">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Prominent Service Price Card --}}
                    <div class="price-card">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="58%" align="right" valign="middle">
                                    <div style="font-size: 9px; color: #6B7280; margin-bottom: 2px;">رسوم خدمة التثمين المعتمدة</div>
                                    <div>
                                        <span class="price-num">{{ number_format($servicePrice, 0) }}</span>
                                        <span class="price-unit">ريال سعودي</span>
                                    </div>
                                </td>
                                <td width="42%" align="left" valign="middle" style="border-right: 1px solid #E5E7EB; padding-right: 8px;">
                                    <div style="font-size: 8.5px; color: #888888; margin-bottom: 2px;">الباقة المختارة</div>
                                    <div style="font-size: 10.5px; font-weight: bold; color: #C1953E; margin-bottom: 4px;">{{ $packageName }}</div>
                                    <span style="display: inline-block; background-color: #ECFDF5; color: #059669; font-size: 8px; font-weight: bold; padding: 2px 6px; border-radius: 4px; border: 1px solid #A7F3D0;">
                                        مدفوع بالكامل
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

                <td width="35%" valign="top" align="center">
                    <div class="image-frame">
                        @if(isset($image) && $image && file_exists($image))
                            <table width="100%" height="145" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" valign="middle">
                                        <img src="{{ $image }}" style="max-width: 92%; max-height: 135px; border-radius: 4px; object-fit: contain;">
                                    </td>
                                </tr>
                            </table>
                        @else
                            <table width="100%" height="145" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" valign="middle">
                                        <div style="font-size: 24px; color: #9CA3AF; margin-bottom: 4px;">&#128247;</div>
                                        <div style="font-size: 10px; font-weight: bold; color: #6B7280;">مكان صورة المنتج</div>
                                        <div style="font-size: 8.5px; color: #9CA3AF; margin-top: 2px;">يُرفق هنا</div>
                                    </td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- بنود التثمين (Pricing Breakdown Table) --}}
        <div class="sec-header">بنود التثمين</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th align="right" width="40%">البند</th>
                    <th align="right" width="42%">التفاصيل</th>
                    <th align="center" width="18%">القيمة (ر.س)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">رسوم خدمة التثمين</td>
                    <td style="color: #4B5563;">{{ $packageName }} (سداد إلكتروني معتمد)</td>
                    <td align="center" style="font-weight: bold;">{{ number_format($servicePrice, 2) }}</td>
                </tr>
                @if($prices['average'] > 0)
                    <tr>
                        <td>القيمة السوقية الاسترشادية للسلعة</td>
                        <td style="color: #4B5563;">{{ $itemTitle }} (تقدير عادل حسب المواصفات والحالة)</td>
                        <td align="center" style="font-weight: bold;">{{ number_format($prices['average']) }}</td>
                    </tr>
                @endif
                @if($prices['lowest'] || $prices['highest'])
                    <tr>
                        <td>النطاق التقديري العادل (السوقي)</td>
                        <td style="color: #4B5563;">
                            الحد الأدنى: {{ $prices['lowest'] ? number_format($prices['lowest']) : '---' }}
                            &nbsp; | &nbsp;
                            الحد الأعلى: {{ $prices['highest'] ? number_format($prices['highest']) : '---' }}
                        </td>
                        <td align="center" style="color: #6B7280;">نطاق تقديري</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2">إجمالي رسوم الخدمة المدفوعة للفاتورة ({{ $packageName }})</td>
                    <td align="center" style="color: #C1953E; font-size: 11px;">{{ number_format($servicePrice, 2) }} ر.س</td>
                </tr>
            </tbody>
        </table>

        {{-- Two-Column Specifications & Condition --}}
        <table class="two-cols-table">
            <tr>
                {{-- Right Column: Specifications --}}
                <td width="49%" valign="top">
                    <div class="sub-sec-title">المواصفات الفنية</div>
                    <table class="detail-table">
                        @forelse(array_slice($specsDetails, 0, 7) as $index => $spec)
                            <tr style="background-color: {{ $index % 2 === 0 ? '#FFFFFF' : '#F9FAFB' }};">
                                <td class="detail-lbl">{{ $spec['title'] }}</td>
                                <td class="detail-val" align="left">{{ $spec['value'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #9CA3AF; padding: 6px;">لا توجد مواصفات إضافية</td>
                            </tr>
                        @endforelse
                    </table>
                </td>

                {{-- Spacer --}}
                <td width="2%"></td>

                {{-- Left Column: Condition & Usage --}}
                <td width="49%" valign="top">
                    <div class="sub-sec-title">الحالة والاستخدام</div>
                    <table class="detail-table">
                        @forelse(array_slice($conditionDetails, 0, 7) as $index => $cond)
                            <tr style="background-color: {{ $index % 2 === 0 ? '#FFFFFF' : '#F9FAFB' }};">
                                <td class="detail-lbl">{{ $cond['title'] }}</td>
                                <td class="detail-val" align="left">{{ $cond['value'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #9CA3AF; padding: 6px;">لا توجد بيانات حالة إضافية</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
            </tr>
        </table>

        {{-- Expert / AI Report --}}
        @php
            $cleanReasoning = trim($reasoning ?? '');
            $hasHtml = strip_tags($cleanReasoning) !== $cleanReasoning;
        @endphp

        @if($cleanReasoning)
            <div class="sec-header">تقرير الخبير</div>
            <div class="report-box">
                @if($hasHtml)
                    {!! strip_tags($cleanReasoning, '<p><br><b><strong><span><div><ul><li><ol><table><tr><td><th>') !!}
                @else
                    {!! nl2br(e($cleanReasoning)) !!}
                @endif
            </div>
        @endif

        {{-- Footer Attribution --}}
        <div class="footer-note">
            * القيمة استرشادية مبنية على البيانات المدخلة ولا تُعد عرض شراء ملزماً.<br>
            صادر عن <strong style="color: #C1953E;">ثمن</strong> – فرص رقمية
        </div>

    </div>

</body>

</html>