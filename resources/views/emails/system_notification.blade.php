@php
  // اللغة تُحدد من محتوى الرسالة نفسها (إن كانت عربية فالإيميل كله عربي)، وإلا من لغة التطبيق
  $rawTitle = $title ?? '';
  $rawBody = $messageBody ?? '';

  // تنظيف علامات الترقيم والتعجب والاستفهام والهاشتاج التي تعكس النصوص العربية
  $cleanTitle = str_replace(['!', '！', '؟', '?', '#'], '', $rawTitle);
  $cleanTitle = trim(preg_replace('/\s+/', ' ', $cleanTitle));

  $cleanBody = str_replace(['!', '！', '؟', '?', '#'], '', $rawBody);

  $hasArabic = preg_match('/\p{Arabic}/u', $cleanTitle . $cleanBody) === 1;
  $locale = app()->getLocale();
  $isRtl  = $hasArabic ? true : ($locale === 'ar');
  $emailTitle = $cleanTitle ?: ($isRtl ? 'إشعار من ثمن' : 'Notification from Thamn');

  $isOrderLink = !empty($actionUrl) && preg_match('#/orders?(/|$)#', (string) $actionUrl) === 1;
  $btnLabel = !empty($actionLabel) ? $actionLabel : ($isRtl
      ? ($isOrderLink ? 'عرض الطلب' : 'عرض التفاصيل')
      : ($isOrderLink ? 'View Order' : 'View Details'));

  // التعرف على نوع التقييم لفصله في سطر مستقل بخط كبير
  $evalKeywords = [
      'التثمين الذكي', 'تثمين ذكي', 'التقييم الذكي', 'تقييم ذكي',
      'التثمين الاحترافي', 'تثمين احترافي', 'التقييم الاحترافي', 'تقييم احترافي',
      'التثمين المحكم', 'تثمين محكم', 'التقييم المحكم', 'تقييم محكم',
      'التثمين الهجين', 'تثمين هجين', 'التقييم الهجين', 'تقييم هجين',
      'طلب تثمين جديد', 'طلب تقييم جديد', 'تقييم استرشادي', 'التثمين الاسترشادي'
  ];

  $foundEvalType = null;
  $titleTop = '';
  $titleBottom = '';

  foreach ($evalKeywords as $kw) {
      $pos = mb_strpos($cleanTitle, $kw);
      if ($pos !== false) {
          $foundEvalType = $kw;
          $titleTop = trim(mb_substr($cleanTitle, 0, $pos));
          $titleBottom = trim(mb_substr($cleanTitle, $pos + mb_strlen($kw)));
          // إزالة أي فواصل أو نقط عالقة
          $titleTop = trim($titleTop, " \t\n\r\0\x0B:-،,.");
          $titleBottom = trim($titleBottom, " \t\n\r\0\x0B:-،,.");
          break;
      }
  }

  $bodyHtml = nl2br(e($cleanBody));
@endphp
@php $banner = 'notification_banner.jpg'; @endphp
@php ob_start(); @endphp

  @if($foundEvalType)
    @if(!empty($titleTop))
      <p style="margin:0 0 10px;font-size:16px;font-weight:700;color:#555555;line-height:1.5;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
        {{ $titleTop }}
      </p>
    @endif

    <div style="margin:12px 0 16px;padding:10px 22px;background:rgba(245,130,32,0.08);border:1.5px solid #F58220;border-radius:12px;display:inline-block;">
      <span style="font-size:26px;font-weight:900;color:#F58220;line-height:1.3;display:block;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
        {{ $foundEvalType }}
      </span>
    </div>

    @if(!empty($titleBottom))
      <p style="margin:0 0 20px;font-size:18px;font-weight:800;color:#1A1A1A;line-height:1.5;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
        {{ $titleBottom }}
      </p>
    @else
      <div style="height:10px;"></div>
    @endif
  @else
    <h1 class="h1" style="margin:0 0 20px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
      {{ $cleanTitle }}
    </h1>
  @endif

  <p style="margin:0 0 28px;font-size:15px;color:#444444;line-height:1.85;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
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