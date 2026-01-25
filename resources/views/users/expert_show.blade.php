@extends('layouts.master')
@section('title','تفاصيل الخبير')

@section('css')
<style>
body {
    font-family: 'Tahoma', sans-serif;
    background-color: #f5f5f5;
}

.invoice {
    max-width: 1000px;
    margin: auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    color: #333;
}

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

/* ====================== VISA CARD ====================== */
.visa-card {
    margin: 20px 0;
    padding: 25px;
    border-radius: 18px;
    background: linear-gradient(135deg, #1f2937, #111827);
    color: #fff;
    box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}

.visa-card:before {
    content: "";
    position: absolute;
    top: -30px;
    right: -30px;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
}

.visa-card .card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.visa-card .card-top img {
    width: 70px;
}

.visa-card .card-number {
    margin-top: 25px;
    font-size: 22px;
    letter-spacing: 3px;
}

.visa-card .card-meta {
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    opacity: 0.9;
}

.card-label {
    font-size: 12px;
    opacity: 0.7;
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

.invoice-table tbody tr:hover {
    background-color: #f1f5f9;
    cursor: pointer;
}

.invoice-total {
    text-align: right;
    font-size: 18px;
    font-weight: bold;
    margin-top: 10px;
    padding: 10px;
    background-color: #fdf6e3;
    border-radius: 8px;
}

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
            🖨️ طباعة
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

        <!-- VISA CARD -->
        <div class="visa-card">
            <div class="card-top">
                <div>
                    <div class="card-number">رقم الحساب: {{ $bank['account_number'] }}</div>
                    <div class="card-meta">
                        <div>
                            <div class="card-label">اسم الحساب</div>
                            <div>{{ $bank['account_name'] }}</div>
                        </div>
                        <div>
                            <div class="card-label">الـ IBAN</div>
                            <div>{{ $bank['iban'] }}</div>
                        </div>
                        <div>
                            <div class="card-label">البنك</div>
                            <div>{{ $bank['bank_name'] }}</div>
                        </div>
                    </div>
                </div>
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
            </div>
        </div>

        <!-- Expert Info -->
        <div class="invoice-info">
            <div>
                <p><strong>اسم الخبير:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                <p><strong>البريد:</strong> {{ $user->email }}</p>
                <p><strong>الهاتف:</strong> {{ $user->phone ?? '-' }}</p>
            </div>
            <div>
                <p><strong>عدد الطلبات التي قيمها:</strong> {{ $ordersCount }}</p>
                <p><strong>الأرباح (4 ريال لكل طلب):</strong> {{ $totalEarned }} SAR</p>
                <p><strong>الرصيد الحالي:</strong> {{ $user->balance ?? 0 }} SAR</p>
            </div>
        </div>

        <!-- Orders Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الطلب</th>
                    <th>الحالة</th>
                    <th>سعر التقييم</th>
                    <th>تاريخ التقييم</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach($orders as $order)
                <tr onclick="window.location='{{ route('orders.show', $order->id) }}'">
                    <td>{{ $counter++ }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->expert_price ?? '-' }}</td>
                    <td>{{ $order->updated_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="invoice-total">
            إجمالي الأرباح: {{ $totalEarned }} SAR
        </div>

    </div>
</div>
@endsection
