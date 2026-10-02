@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'نسختك من الاتفاقية القانونية – ثمن' : 'Your Legal Agreement Copy – Thamn';
@endphp
@php $banner = 'declaration_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'تم توقيع الاتفاقية بنجاح ✅' : 'Agreement Signed Successfully ✅' }}
  </h1>

  <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.85;">
    مرحباً <strong style="color:#1A1A1A;">{{ $declaration->full_name ?? '' }}</strong><br>
    استلمنا طلب انضمامك لفريق الخبراء في ثمن وسنقوم بمراجعة طلبك والتواصل معك قريباً
  </p>

  <a href="{{ $downloadUrl }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'تحميل نسخة الاتفاقية' : 'Download Agreement' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))