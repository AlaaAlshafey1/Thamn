@php
  // Test preview routes — accessed via: /preview/email/{name}
  // Available: otp | reset_password | welcome | expert_registration | valuation_result
  //            admin_withdrawal | arbitrator_declaration | declaration_signed
  //            expert_valuation | invoice | system_notification | expert_declaration
  $name = request()->segment(3) ?? 'otp';
  app()->setLocale('ar');
@endphp

<div style="font-family:Arial,sans-serif;padding:20px;background:#E5E7EB;">
  <h2 style="margin:0 0 20px;font-size:16px;color:#111;">📧 معاينة الإيميلات — اختر إيميلاً:</h2>
  <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:30px;">
    @foreach([
      'otp'                    => 'OTP تسجيل',
      'reset_password'         => 'إعادة كلمة المرور',
      'welcome'                => 'مرحباً بك',
      'expert_registration'    => 'تسجيل خبير',
      'valuation_result'       => 'نتيجة التقييم',
      'admin_withdrawal'       => 'سحب رصيد (أدمن)',
      'arbitrator_declaration' => 'إقرار المحكّم',
      'declaration_signed'     => 'الاتفاقية الموقّعة',
      'expert_valuation'       => 'تقييم خبير',
      'invoice'                => 'فاتورة',
      'system_notification'    => 'إشعار نظام',
      'expert_declaration'     => 'إقرار الخبير',
    ] as $key => $label)
      <a href="/preview/email/{{ $key }}"
         style="padding:8px 16px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;
                background:{{ $name==$key ? '#C1953E' : '#1A1A1A' }};
                color:#fff;">{{ $label }}</a>
    @endforeach
  </div>
</div>
