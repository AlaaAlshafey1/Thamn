@php
  // اللغة تُحدد من محتوى الرسالة نفسها (إن كانت عربية فالإيميل كله عربي)، وإلا من لغة التطبيق
  $hasArabic = preg_match('/\p{Arabic}/u', ($title ?? '') . ($messageBody ?? '')) === 1;
  $locale = app()->getLocale();
  $isRtl  = $hasArabic ? true : ($locale === 'ar');
  $emailTitle = $title ?? ($isRtl ? 'إشعار من ثمن' : 'Notification from Thamn');
  $isOrderLink = !empty($actionUrl) && preg_match('#/orders?(/|$)#', (string) $actionUrl) === 1;
  $btnLabel = !empty($actionLabel) ? $actionLabel : ($isRtl
      ? ($isOrderLink ? 'عرض الطلب' : 'عرض التفاصيل')
      : ($isOrderLink ? 'View Order' : 'View Details'));
  // إبراز نوع الطلب (تثمين ذكي): خط تحت + لون برتقالي
  $bodyHtml = nl2br(e($messageBody ?? ''));
  $bodyHtml = str_replace('تثمين ذكي',
      '<span style="color:#F58220;text-decoration:underline;font-weight:700;">تثمين ذكي</span>', $bodyHtml);
  $titleHtml = str_replace('التثمين الذكي',
      '<span style="color:#F58220;text-decoration:underline;">التثمين الذكي</span>', e($title ?? ($isRtl ? 'إشعار من منصة ثمن 🔔' : 'Notification from Thamn 🔔')));
@endphp
@php $banner = 'notification_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    {!! $titleHtml !!}
  </h1>

  <p style="margin:0 0 28px;font-size:14px;color:#666;line-height:1.85;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    {!! $bodyHtml !!}
  </p>

  @if($actionUrl)
  <a href="{{ $actionUrl }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    {{ $btnLabel }}
  </a>
  @endif

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot','isRtl'))