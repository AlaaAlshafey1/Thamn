@extends('layouts.master')
@section('title','تفاصيل الطلب')

@section('css')
<style>
body {
    font-family: 'Tahoma', sans-serif;
    background-color: #f5f5f5;
}

/* ====================== Invoice Container ====================== */
.invoice {
    max-width: 850px;
    margin: auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    color: #333;
}

/* ====================== Header ====================== */
.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #c1953e;
    padding-bottom: 15px;
}

.invoice-header img {
    max-height: 80px;
}

.invoice-header .company-details h2 {
    color: #c1953e;
    margin: 0 0 5px 0;
}

.invoice-header .company-details p {
    margin: 2px 0;
    font-size: 14px;
}

/* ====================== Customer Info ====================== */
.invoice-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    background-color: #fdf6e3;
    padding: 15px;
    border-radius: 8px;
}

.invoice-info p {
    margin: 3px 0;
    font-size: 14px;
}

/* ====================== Table ====================== */
.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.invoice-table th, .invoice-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    font-size: 14px;
}

.invoice-table th {
    background-color: #c1953e;
    color: #fff;
}

.invoice-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* ====================== Total ====================== */
.invoice-total {
    text-align: right;
    font-size: 18px;
    font-weight: bold;
    margin-top: 10px;
    padding: 10px;
    background-color: #fdf6e3;
    border-radius: 8px;
}

/* ====================== Print Styles ====================== */
@media print {
    body * {
        visibility: hidden;
    }
    #printableInvoice, #printableInvoice * {
        visibility: visible;
    }
    #printableInvoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection

@section('content')
<div class="container py-3">

    <div class="mb-3 text-end">
        <button class="btn btn-warning" onclick="window.print()" style="background-color:#c1953e; border-color:#c1953e;">
            🖨️ طباعة الفاتورة
        </button>
    </div>

    <div id="printableInvoice" class="invoice">

        <!-- Header -->
        <div class="invoice-header">
            <img src="{{ URL::asset('assets/img/Logo.png') }}" alt="Logo">
            <div class="company-details">
                <h2>ثمن</h2>
                <p>الرياض</p>
                <p>الهاتف: 0123456789</p>
                <p>البريد الإلكتروني: info@thamn.sa</p>
            </div>
        </div>

        <!-- Customer & Order Info -->
        <div class="invoice-info">
            <div>
                <p><strong>رقم الطلب:</strong> {{ $order->id }}</p>
                <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d') }}</p>
            </div>
            <div>
                <p><strong>المستخدم:</strong> {{ $order->user->first_name . ' ' . $order->user->last_name }}</p>
                <p><strong>حالة الطلب:</strong> {{ $order->status }}</p>
                <p><strong>الدفع:</strong> {{ $order->payment_status ?? '-' }}</p>
            </div>
        </div>

        <!-- Order Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>السؤال</th>
                    <th>الإجابة</th>
                    {{-- <th>السعر (SAR)</th> --}}
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; $total = 0; @endphp
                @foreach($order->details as $detail)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $detail->question->question_ar ?? '-' }}</td>
                    <td>{{ $detail->option->option_ar ?? $detail->value ?? '-' }}</td>
                    {{-- <td>{{ number_format($detail->price ?? 0,2) }}</td>
                    @php $total += $detail->price ?? 0; @endphp --}}
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <div class="invoice-total">
            الإجمالي: {{ number_format($order->total_price,2) }} SAR
        </div>

    </div>
</div>
@endsection
