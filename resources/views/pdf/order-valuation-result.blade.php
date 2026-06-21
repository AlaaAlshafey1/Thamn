<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>بطاقة تسعير ومواصفات #{{ $order->id }}</title>
    <style>
        body { 
            font-family: 'dejavusans', sans-serif; 
            direction: rtl; 
            font-size: 13px;
            color: #1A1A1A;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #C1953E;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .sec-title {
            color: #C1953E;
            font-size: 16px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 1px solid #EEEEEE;
            padding-bottom: 5px;
        }
        .specs-table {
            width: 100%;
            border-collapse: collapse;
        }
        .specs-table td {
            padding: 10px;
            border: 1px solid #EEEEEE;
            background-color: #F9F9FB;
        }
        .spec-lbl {
            font-size: 11px;
            color: #888888;
            font-weight: bold;
            display: block;
        }
        .spec-val {
            font-size: 13px;
            font-weight: bold;
            color: #1A1A1A;
        }
        .price-box {
            background-color: #1A1A1A;
            color: #FFFFFF;
            text-align: center;
            padding: 25px;
            border-radius: 8px;
        }
        .notes-box {
            background-color: #FDFDFD;
            border-right: 4px solid #C1953E;
            padding: 15px;
            font-size: 13px;
            color: #444;
        }
        .owner-box {
            background-color: #F9F9FB;
            padding: 15px;
            border: 1px solid #EEEEEE;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <table class="header" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="70%" valign="middle">
                <span style="color: #C1953E; font-size: 11px; font-weight: bold;">&#9670; بطاقة تثمين رسمية &#9670;</span>
                <h1 style="margin: 5px 0; font-size: 24px; color: #1A1A1A;">مواصفات وتسعير</h1>
                <span style="color: #888888; font-size: 12px;">
                    رقم البطاقة: <strong style="color: #C1953E;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    &nbsp;|&nbsp; {{ $category }} &nbsp;|&nbsp; {{ $order->updated_at?->format('Y/m/d') }}
                </span>
            </td>
            <td width="30%" align="left" valign="middle">
                <img src="{{ public_path('assets/img/Logo.png') }}" height="60">
            </td>
        </tr>
    </table>

    @if(isset($image) && $image)
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ $image }}" height="220" style="border: 1px solid #EEEEEE; padding: 5px; border-radius: 8px;">
    </div>
    @endif

    <div class="sec-title">المواصفات الفنية</div>
    
    <table class="specs-table">
        @php $chunks = array_chunk($details, 2); @endphp
        @foreach($chunks as $chunk)
        <tr>
            @foreach($chunk as $detail)
            <td width="50%">
                <span class="spec-lbl">{{ $detail['title'] }}</span>
                <span class="spec-val">{{ $detail['value'] }}</span>
            </td>
            @endforeach
            @if(count($chunk) == 1)
            <td width="50%" style="background: transparent; border: none;"></td>
            @endif
        </tr>
        @endforeach
    </table>

    <div class="sec-title">التسعير المعتمد</div>
    <div class="price-box">
        <div style="font-size: 12px; color: #C1953E; margin-bottom: 5px;">&#10022; التسعير العادل المعتمد &#10022;</div>
        <div style="font-size: 12px; color: #999999;">القيمة الاسترشادية للسلعة</div>
        <div style="font-size: 42px; font-weight: bold; margin: 10px 0;">
            {{ $prices['average'] ? number_format($prices['average']) : '---' }}
        </div>
        <div style="font-size: 14px; color: #999999;">ريال سعودي</div>

        @if($prices['lowest'] || $prices['highest'])
        <table width="60%" align="center" style="margin-top: 15px; border-top: 1px solid #333; padding-top: 15px;" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="50%" align="center">
                    <span style="font-size: 11px; color: #888;">الحد الأدنى</span><br>
                    <strong style="font-size: 16px; color: #CCC;">{{ $prices['lowest'] ? number_format($prices['lowest']) : '---' }}</strong>
                </td>
                <td width="50%" align="center" style="border-right: 1px solid #444;">
                    <span style="font-size: 11px; color: #888;">الحد الأعلى</span><br>
                    <strong style="font-size: 16px; color: #CCC;">{{ $prices['highest'] ? number_format($prices['highest']) : '---' }}</strong>
                </td>
            </tr>
        </table>
        @endif
    </div>

    @if($reasoning)
    <div class="sec-title">ملاحظات وتقرير الخبير</div>
    <div class="notes-box">
        {{ $reasoning }}
    </div>
    @endif

    @if($order->user)
    <div class="sec-title">بيانات طالب التثمين</div>
    <div class="owner-box">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="50%">
                    <span style="font-size: 11px; color: #888; display: block;">الاسم</span>
                    <strong style="font-size: 14px;">{{ $order->user->first_name }} {{ $order->user->last_name }}</strong>
                </td>
                <td width="50%" align="left">
                    <span style="font-size: 11px; color: #888; display: block;">رقم التواصل</span>
                    <strong style="font-size: 14px;" dir="ltr">{{ $order->user->phone_number ?? '---' }}</strong>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <div style="text-align: center; font-size: 11px; color: #999; margin-top: 30px; border-top: 1px solid #EEE; padding-top: 15px;">
        * السعر المذكور قيمة استرشادية مبنية على البيانات المدخلة ولا يُعدّ عرض شراء ملزماً.<br>
        صادر عن منصة <strong style="color: #C1953E;">ثمن</strong> للتثمين الذكي
    </div>

</body>
</html>
