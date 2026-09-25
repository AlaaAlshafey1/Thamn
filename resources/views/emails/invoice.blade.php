@php
  $locale = app()->getLocale();
  $isRtl  = $locale === 'ar';
  $emailTitle = $isRtl ? 'فاتورة طلبك – ثمن' : 'Your Invoice – Thamn';
@endphp
@php $banner = 'invoice_banner.jpg'; @endphp
@php ob_start(); @endphp

  <h1 class="h1" style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1A1A1A;line-height:1.4;">
    {{ $isRtl ? 'فاتورة طلبك جاهزة 🧾' : 'Your Invoice is Ready 🧾' }}
  </h1>

  <p style="margin:0 0 20px;font-size:14px;color:#666;line-height:1.85;">
    {{ $isRtl ? 'شكراً لثقتك بثمن. تجد أدناه تفاصيل فاتورة طلبك.' : 'Thank you for using Thamn. Find your invoice details below.' }}
  </p>

  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#F9F9F9;border-radius:12px;border:1px solid #E5E5E5;margin-bottom:24px;">
    <tr>
      <td style="padding:16px 20px;text-align:right;">
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          🆔 {{ $isRtl ? 'رقم الطلب' : 'Order #' }}: <strong style="color:#C1953E;">#{{ $order->id ?? 'N/A' }}</strong>
        </p>
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          📦 {{ $isRtl ? 'الفئة' : 'Category' }}: <strong style="color:#1A1A1A;">{{ $order->category?->name_ar ?? 'N/A' }}</strong>
        </p>
        <p style="margin:0 0 8px;font-size:13px;color:#555;">
          💰 {{ $isRtl ? 'المبلغ المدفوع' : 'Amount Paid' }}: <strong style="color:#C1953E;">{{ number_format($order->price ?? 0, 2) }} {{ $isRtl ? 'ر.س' : 'SAR' }}</strong>
        </p>
        <p style="margin:0;font-size:13px;color:#555;">
          📅 {{ $isRtl ? 'تاريخ الدفع' : 'Payment Date' }}: <strong style="color:#1A1A1A;">{{ $order->created_at?->format('Y-m-d') ?? now()->format('Y-m-d') }}</strong>
        </p>
      </td>
    </tr>
  </table>

  <a href="https://thmmn.net"
     style="display:inline-block;padding:14px 36px;background:#C1953E;color:#fff;
            font-size:15px;font-weight:700;border-radius:100px;text-decoration:none;">
    {{ $isRtl ? 'عرض الطلب' : 'View Order' }}
  </a>

@php $slot = ob_get_clean(); @endphp
@include('emails._layout', compact('banner','emailTitle','slot'))