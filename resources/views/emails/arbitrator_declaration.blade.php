@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'وثيقة الشروط والأحكام – ثمن' : 'Terms & Conditions Declaration – Thamn';
@endphp
@php $banner = 'declaration_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'وثيقة الشروط والأحكام وإقرار السرية 📄' : 'Terms & Confidentiality Declaration 📄' }}
  </h1>

  <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.85;">
    @if($isRtl)
      مرحباً <strong style="color:#1A1A1A;">{{ $arbitrator->full_name ?? '' }}</strong>،<br>
      يرجى مراجعة وثيقة الشروط والأحكام وإقرار السرية الخاصة بمنصة ثمن والتوقيع عليها.
    @else
      Hi <strong style="color:#1A1A1A;">{{ $arbitrator->full_name ?? '' }}</strong>,<br>
      Please review and sign the Terms & Confidentiality Declaration for the Thamn platform.
    @endif
  </p>

  <a href="{{ $declarationUrl }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'مراجعة الوثيقة والتوقيع' : 'Review & Sign Declaration' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))