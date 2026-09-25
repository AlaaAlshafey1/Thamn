@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  
  // Determine Evaluation Type / Package Name
  $evalType = $order->evaluation_type ?? 'expert';
  
  if ($evalType === 'ai') {
      $packageTitle = 'باقة التثمين الذكي';
      $instructionText = 'يرجى إبداء رأيك كخبير في السعر الاسترشادي وفق النتيجة المسبقة للتثمين الذكي وذلك بالاختيار من التالي:';
  } elseif ($evalType === 'thamn' || $evalType === 'best') {
      $packageTitle = 'باقة التثمين الاحترافي';
      $instructionText = 'يرجى إدخال السعر التقديري وفق التقرير المرفق ثم إبداء رأيك كخبير في نتيجة التثمين وذلك بالاختيار من التالي:';
  } else {
      // Default to Expert / Arbitrator
      $packageTitle = 'باقة التثمين المحكم';
      $instructionText = 'يرجى إدخال السعر التقديري المعتمد ثم إبداء رأيك كخبير في نتيجة التثمين وذلك بالاختيار من التالي:';
  }

  $emailTitle = $isRtl ? 'مطلوب المصادقة على طلب تثمين' : 'Valuation Authentication Required';
@endphp
@php $banner = 'expert_valuation_banner.jpg'; @endphp
@php ob_start(); @endphp

  <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#C1953E;text-transform:uppercase;">
    {{ $packageTitle }}
  </p>

  <h1 class="h1" style="margin:0 0 16px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'يا هلا خبير التثمين 👋' : 'Hello Valuation Expert 👋' }}
  </h1>

  <p style="margin:0 0 20px;font-size:15px;color:#444;line-height:1.85;">
    @if($isRtl)
      <strong>للأهمية:</strong> رقم الطلب <strong style="color:#C1953E;">( {{ $order->id ?? 'N/A' }} )</strong><br><br>
      وصلك طلب (تقييم استرشادي لسلعة).<br>
      هناك عميل بانتظارك.<br><br>
      {{ $instructionText }}
    @else
      <strong>Urgent:</strong> Order #<strong style="color:#C1953E;">( {{ $order->id ?? 'N/A' }} )</strong><br><br>
      You have received a new valuation request.<br>
      A client is waiting for you.<br><br>
      {{ $instructionText }}
    @endif
  </p>

  <a href="https://thmmn.net/expert/login"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;margin-bottom:24px;">
    {{ $isRtl ? 'اضغط هنا للمتابعة' : 'Click Here to Proceed' }}
  </a>

  <p style="margin:0;font-size:14px;color:#666;line-height:1.85;">
    {{ $isRtl ? 'شاكرين تعاونكم،' : 'Thank you for your cooperation,' }}<br>
    <strong>{{ $isRtl ? 'فريق تطبيق ثمن' : 'Thamn App Team' }}</strong>
  </p>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))