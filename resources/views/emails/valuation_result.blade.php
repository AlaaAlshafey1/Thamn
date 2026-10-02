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
    تم تقييم طلبك رقم [{{ $order->id }}] بنجاح بواسطة : 
    [{{ $evaluationType == 'ai' ? 'ذكاء اصطناعي' : ($evaluationType == 'expert' ? 'خبير' : 'خبير / ذكاء اصطناعي') }}]
  </p>

  {{-- Price block --}}
  @if($recommendedPrice)
  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#FFF8EC;border-radius:12px;border:1px solid #F5DFA0;margin-bottom:20px;">
    <tr>
      <td style="padding:16px 20px;text-align:center;">
        <p style="margin:0 0 4px;font-size:13px;color:#888;">السعر النهائي :</p>
        <p style="margin:0;font-size:28px;font-weight:900;color:#C1953E;">
          [{{ number_format($recommendedPrice, 0) }}] ريال
        </p>
      </td>
    </tr>
  </table>
  @endif

  <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.85;">
    شكراً لاستخدامك ثمن.
  </p>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('customBanner', 'banner', 'emailTitle', 'slot'))