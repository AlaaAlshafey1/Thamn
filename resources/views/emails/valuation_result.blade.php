@php
  $userLang = $order->user->preferred_language ?? 'ar';
  $isRtl  = $userLang !== 'en';
  $emailTitle = $isRtl ? 'تم تقييم طلبك بنجاح' : 'Your order has been evaluated';
  $byLabel = $evaluationType == 'ai'
      ? ($isRtl ? 'ذكاء اصطناعي' : 'AI')
      : ($evaluationType == 'expert'
          ? ($isRtl ? 'خبير' : 'Expert')
          : ($isRtl ? 'خبير / ذكاء اصطناعي' : 'Expert / AI'));

  // Use the product image if available, otherwise fallback to the default banner
  $customBanner = $productImageUrl ?? null;
  $banner = 'valuation_banner.jpg';
@endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 10px;font-size:20px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'تم تقييم طلبك بنجاح' : 'Your Order Has Been Evaluated' }}
  </h1>

  <div style="margin:12px 0 16px;padding:10px 22px;background:rgba(193,149,62,0.08);border:1.5px solid #C1953E;border-radius:12px;display:inline-block;">
    <span style="font-size:26px;font-weight:900;color:#C1953E;line-height:1.3;display:block;">
      {{ $isRtl ? 'نوع التقييم ' . $byLabel : 'Evaluation Type ' . $byLabel }}
    </span>
  </div>

  <p style="margin:0 0 20px;font-size:16px;color:#333;line-height:1.85;font-weight:700;">
    {{ $isRtl ? 'طلب رقم ' . $order->id : 'Order ' . $order->id }}
  </p>

  {{-- Price block --}}
  @if($recommendedPrice)
  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#FFF8EC;border-radius:12px;border:1px solid #F5DFA0;margin-bottom:20px;">
    <tr>
      <td style="padding:16px 20px;text-align:center;">
        <p style="margin:0 0 4px;font-size:13px;color:#888;">{{ $isRtl ? 'السعر النهائي' : 'Final price' }}</p>
        <p style="margin:0;font-size:28px;font-weight:900;color:#C1953E;">
          {{ number_format($recommendedPrice, 0) }} {{ $isRtl ? 'ريال' : 'SAR' }}
        </p>
      </td>
    </tr>
  </table>
  @endif

  <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.85;">
    {{ $isRtl ? 'شكرا لاستخدامك ثمن' : 'Thank you for using Thamn' }}
  </p>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('customBanner', 'banner', 'emailTitle', 'slot'))