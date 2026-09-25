@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'مرحباً بك في ثمن' : 'Welcome to Thamn';
@endphp
@php $banner = 'welcome_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'مرحباً بك في ثمن 🎉' : 'Welcome to Thamn! 🎉' }}
  </h1>

  <p style="margin:0 0 28px;font-size:14px;color:#666;line-height:1.85;">
    @if($isRtl)
      أهلاً <strong style="color:#1A1A1A;">{{ $user->first_name ?? '' }}</strong>،<br>
      يسعدنا انضمامك لمنصة <strong>ثمن</strong> — المنصة الأولى للتثمين الاسترشادي للسلع المستعملة.<br>
      حسابك جاهز الآن. ابدأ رحلتك معنا!
    @else
      Hi <strong style="color:#1A1A1A;">{{ $user->first_name ?? '' }}</strong>,<br>
      Welcome to <strong>Thamn</strong> — the leading platform for used goods valuation.<br>
      Your account is ready. Start your journey with us!
    @endif
  </p>

  <a href="https://thmmn.net"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'استكشف ثمن' : 'Explore Thamn' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))