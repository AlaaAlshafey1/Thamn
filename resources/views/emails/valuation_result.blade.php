@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'تم إصدار بطاقة التثمين لطلبك بنجاح' : 'Your Valuation Card is Ready';

  // Use the product image if available, otherwise fallback to the default banner
  $customBanner = $productImageUrl ?? null;
  if (!$customBanner) {
      $banner = 'valuation_banner.jpg';
  }
@endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'تطبيق ثمن 🏷️' : 'Thamn App 🏷️' }}
  </h1>

  <p style="margin:0 0 20px;font-size:15px;color:#444;line-height:1.85;font-weight:600;">
    @if($isRtl)
      مرحباً بك،<br>
      تم إصدار بطاقة التثمين لطلبك رقم 
      <strong style="color:#C1953E;">(#{{ $order->id }})</strong> 
      بنجاح.
    @else
      Hello,<br>
      The valuation card for your order 
      <strong style="color:#C1953E;">(#{{ $order->id }})</strong> 
      has been successfully issued.
    @endif
  </p>

  {{-- Price block --}}
  @if($recommendedPrice)
  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#FFF8EC;border-radius:12px;border:1px solid #F5DFA0;margin-bottom:20px;">
    <tr>
      <td style="padding:16px 20px;text-align:center;">
        <p style="margin:0 0 4px;font-size:13px;color:#888;">{{ $isRtl ? 'القيمة الاسترشادية المقترحة' : 'Recommended Value' }}</p>
        <p style="margin:0;font-size:28px;font-weight:900;color:#C1953E;">
          {{ number_format($recommendedPrice, 0) }} {{ $isRtl ? 'ر.س' : 'SAR' }}
        </p>
        @if($minPrice && $maxPrice)
        <p style="margin:6px 0 0;font-size:13px;color:#888;">
          {{ $isRtl ? 'النطاق:' : 'Range:' }}
          {{ number_format($minPrice,0) }} – {{ number_format($maxPrice,0) }} {{ $isRtl ? 'ر.س' : 'SAR' }}
        </p>
        @endif
      </td>
    </tr>
  </table>
  @endif

  @if($reasoning)
  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#F9F9F9;border-radius:12px;border:1px solid #E5E5E5;margin-bottom:24px;">
    <tr>
      <td style="padding:14px 18px;font-size:14px;color:#555;line-height:1.7;text-align:{{ $isRtl ? 'right' : 'left' }};">
        {{ $reasoning }}
      </td>
    </tr>
  </table>
  @endif

  <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.85;">
    {{ $isRtl ? 'يرجى فتح التطبيق للاطلاع على كافة التفاصيل.' : 'Please open the app to view full details.' }}
  </p>

  {{-- Fallback deep link to the app (since the user does not have a web dashboard) --}}
  <a href="https://thmmn.net"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'الذهاب للتطبيق' : 'Open App' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('customBanner', 'banner', 'emailTitle', 'slot'))