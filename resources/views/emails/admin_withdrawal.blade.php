@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'طلب سحب رصيد جديد – ثمن (للإدارة)' : 'New Withdrawal Request – Thamn (Admin)';
@endphp
@php $banner = 'withdrawal_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'طلب سحب رصيد جديد 💰' : 'New Withdrawal Request 💰' }}
  </h1>

  <p style="margin:0 0 20px;font-size:14px;color:#666;line-height:1.85;">
    {{ $isRtl ? 'تم استلام طلب سحب رصيد جديد من الخبير التالي:' : 'A new withdrawal request has been received from:' }}
  </p>

  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#F9F9F9;border-radius:12px;border:1px solid #E5E5E5;margin-bottom:24px;">
    <tr>
      <td style="padding:16px 20px;text-align:right;">
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          👤 {{ $isRtl ? 'اسم الخبير' : 'Expert Name' }}: <strong style="color:#1A1A1A;">{{ $withdrawal->user->first_name ?? '' }} {{ $withdrawal->user->last_name ?? '' }}</strong>
        </p>
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          💵 {{ $isRtl ? 'المبلغ المطلوب' : 'Requested Amount' }}: <strong style="color:#C1953E;">{{ number_format($withdrawal->amount ?? 0, 2) }} {{ $isRtl ? 'ر.س' : 'SAR' }}</strong>
        </p>
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          🏦 {{ $isRtl ? 'رقم الآيبان' : 'IBAN' }}: <strong style="color:#1A1A1A;">{{ $withdrawal->iban ?? 'N/A' }}</strong>
        </p>
        <p style="margin:0;font-size:13px;color:#555;">
          📅 {{ $isRtl ? 'تاريخ الطلب' : 'Request Date' }}: <strong style="color:#1A1A1A;">{{ $withdrawal->created_at?->format('Y-m-d') ?? now()->format('Y-m-d') }}</strong>
        </p>
      </td>
    </tr>
  </table>

  <a href="{{ url('/admin/withdrawals') }}"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'مراجعة الطلب' : 'Review Request' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))