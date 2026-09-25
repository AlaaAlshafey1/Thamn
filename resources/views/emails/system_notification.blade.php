@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $title ?? ($isRtl ? 'إشعار من ثمن' : 'Notification from Thamn');
@endphp
@php $banner = 'notification_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $title ?? ($isRtl ? 'إشعار من منصة ثمن 🔔' : 'Notification from Thamn 🔔') }}
  </h1>

  <p style="margin:0 0 28px;font-size:14px;color:#666;line-height:1.85;">
    {{ $messageBody ?? '' }}
  </p>

  @if($actionUrl)
  <a href="{{ $actionUrl }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'عرض التفاصيل' : 'View Details' }}
  </a>
  @endif

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))