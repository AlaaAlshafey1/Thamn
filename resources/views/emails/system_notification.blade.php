@php
  // اللغة تُحدد من محتوى الرسالة نفسها (إن كانت عربية فالإيميل كله عربي)، وإلا من لغة التطبيق
  $hasArabic = preg_match('/\p{Arabic}/u', ($title ?? '') . ($messageBody ?? '')) === 1;
  $locale = app()->getLocale();
  $isRtl  = $hasArabic ? true : ($locale === 'ar');
  $emailTitle = $title ?? ($isRtl ? 'إشعار من ثمن' : 'Notification from Thamn');
  $isOrderLink = !empty($actionUrl) && preg_match('#/orders?(/|$)#', (string) $actionUrl) === 1;
  $btnLabel = $isRtl
      ? ($isOrderLink ? 'عرض الطلب' : 'عرض التفاصيل')
      : ($isOrderLink ? 'View Order' : 'View Details');
@endphp
@php $banner = 'notification_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $title ?? ($isRtl ? 'إشعار من منصة ثمن 🔔' : 'Notification from Thamn 🔔') }}
  </h1>

  <p style="margin:0 0 28px;font-size:14px;color:#666;line-height:1.85;">
    {!! nl2br(e($messageBody ?? '')) !!}
  </p>

  @if($actionUrl)
  <a href="{{ $actionUrl }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $btnLabel }}
  </a>
  @endif

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot','isRtl'))