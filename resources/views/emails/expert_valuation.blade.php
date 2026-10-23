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
  
  $actionLink = 'https://thmmn.net/expert/login';
  if (isset($expert) && $expert) {
      $actionLink = \Illuminate\Support\Facades\URL::temporarySignedRoute('expert.auto-login', now()->addDays(7), ['user' => $expert->id, 'redirect' => route('orders.show', $order->id)]);
  } elseif (isset($order->expert) && $order->expert) {
      $actionLink = \Illuminate\Support\Facades\URL::temporarySignedRoute('expert.auto-login', now()->addDays(7), ['user' => $order->expert->id, 'redirect' => route('orders.show', $order->id)]);
  }
@endphp
@php $banner = 'expert_valuation_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 10px;font-size:20px;font-weight:800;color:#1A1A1A;line-height:1.4;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    {{ $isRtl ? 'يا هلا خبير التثمين' : 'Hello Valuation Expert' }}
  </h1>

  <div style="margin:12px 0 16px;padding:10px 22px;background:rgba(193,149,62,0.08);border:1.5px solid #C1953E;border-radius:12px;display:inline-block;">
    <span style="font-size:26px;font-weight:900;color:#C1953E;line-height:1.3;display:block;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
      {{ $packageTitle }}
    </span>
  </div>

  <p style="margin:0 0 20px;font-size:15px;color:#444;line-height:1.85;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    @if($isRtl)
      <strong>للأهمية</strong> رقم الطلب <strong style="color:#C1953E;">( {{ $order->id ?? '' }} )</strong><br><br>
      وصلك طلب تقييم استرشادي لسلعة<br>
      هناك عميل بانتظارك<br><br>
      {{ $instructionText }}
    @else
      <strong>Urgent:</strong> Order <strong style="color:#C1953E;">( {{ $order->id ?? '' }} )</strong><br><br>
      You have received a new valuation request<br>
      A client is waiting for you<br><br>
      {{ $instructionText }}
    @endif
  </p>

  <a href="{{ $actionLink }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;margin-bottom:24px;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    {{ $isRtl ? 'الدخول للطلب والتقييم' : 'Proceed to Order' }}
  </a>

  <p style="margin:0;font-size:14px;color:#666;line-height:1.85;font-family:'IBM Plex Sans Arabic',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Tahoma,Arial,sans-serif;">
    {{ $isRtl ? 'شاكرين تعاونكم' : 'Thank you for your cooperation' }}<br>
    <strong>{{ $isRtl ? 'فريق تطبيق ثمن' : 'Thamn App Team' }}</strong>
  </p>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))