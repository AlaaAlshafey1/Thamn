@extends('layouts.master')
@section('title','تفاصيل الخبير')

@section('css')
<style>
body { font-family: 'Tahoma', sans-serif; background-color: #f5f5f5; }

.invoice {
    max-width: 1050px;
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

.invoice-header img { max-height: 80px; }
.invoice-header .company-details h2 { color: #c1953e; margin: 0 0 5px 0; }
.invoice-header .company-details p { margin: 2px 0; font-size: 14px; }

/* VISA CARD */
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
    top: -30px; right: -30px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
}
.visa-card .card-top { display: flex; justify-content: space-between; align-items: center; }
.visa-card .card-top img { width: 70px; }
.visa-card .card-number { margin-top: 25px; font-size: 22px; letter-spacing: 3px; }
.visa-card .card-meta { margin-top: 15px; display: flex; justify-content: space-between; font-size: 14px; opacity: 0.9; }
.card-label { font-size: 12px; opacity: 0.7; }

/* Table */
.invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; }
.invoice-table th { background-color: #c1953e; color: #fff; }
.invoice-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
.invoice-table tbody tr:hover { background-color: #f1f5f9; cursor: pointer; }

.invoice-total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 10px; padding: 10px; background-color: #fdf6e3; border-radius: 8px; }

/* ===== Declaration Card ===== */
.declaration-card {
    border: 2px solid #e5ddd0;
    border-radius: 14px;
    padding: 24px;
    margin: 24px 0;
    position: relative;
    overflow: hidden;
    background: #fefcf9;
}
.declaration-card.signed { border-color: #10b981; background: #f0fdf4; }
.declaration-card.unsigned { border-color: #f59e0b; background: #fffbeb; }

.declaration-card .dec-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.declaration-card .dec-title { font-size: 16px; font-weight: 700; color: #1a1a2e; }
.declaration-card .dec-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}
.badge-signed { background: #dcfce7; color: #16a34a; }
.badge-unsigned { background: #fef3c7; color: #d97706; }
.badge-not-sent { background: #fee2e2; color: #dc2626; }

.dec-info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin: 16px 0; }
.dec-info-item { background: white; border-radius: 10px; padding: 12px 14px; border: 1px solid #e5ddd0; }
.dec-info-label { font-size: 11px; color: #999; margin-bottom: 3px; }
.dec-info-value { font-size: 13px; font-weight: 700; color: #1a1a2e; }

.dec-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
.btn-gold { background: linear-gradient(135deg, #d4af37, #c9933a); color: white; border: none; padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(212,175,55,0.4); color: white; }

/* ===== Action Buttons ===== */
.action-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    padding: 16px 20px;
    background: #f8f5f0;
    border-radius: 12px;
    border: 1px solid #e5ddd0;
    align-items: center;
}
.action-bar .bar-title { font-size: 13px; font-weight: 700; color: #666; margin-left: auto; }

@media print {
    body * { visibility: hidden; }
    #printableInvoice, #printableInvoice * { visibility: visible; }
    #printableInvoice { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
@endsection

@section('content')
<div class="container py-3">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== Action Bar ===== --}}
    @php
        $declaration = $user->declaration;
    @endphp

    <div class="action-bar">
        <span class="bar-title">⚡ إجراءات سريعة:</span>

        {{-- زر إرسال / إعادة إرسال الوثيقة --}}
        <form action="{{ route('experts.sendDeclaration', $user->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('{{ $declaration ? 'سيتم إرسال رابط الوثيقة مجدداً، هل أنت متأكد؟' : 'سيتم إرسال وثيقة الإقرار للخبير، هل أنت متأكد؟' }}')">
            @csrf
            <button type="submit" class="btn-gold">
                📧 {{ $declaration ? 'إعادة إرسال الوثيقة' : 'إرسال وثيقة الإقرار' }}
            </button>
        </form>

        {{-- زر التفعيل / الإيقاف --}}
        <form action="{{ route('experts.activate', $user->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('{{ $user->is_active ? 'هل تريد إيقاف هذا الخبير؟' : 'هل تريد تفعيل هذا الخبير؟' }}')">
            @csrf
            @if($user->is_active)
                <button type="submit" class="btn btn-warning btn-sm" style="font-size:13px; padding:10px 18px;">
                    ⏸️ إيقاف الخبير
                </button>
            @else
                <button type="submit" class="btn btn-success btn-sm" style="font-size:13px; padding:10px 18px;">
                    ✅ تفعيل الخبير
                </button>
            @endif
        </form>

        {{-- زر الطباعة --}}
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()" style="font-size:13px; padding:10px 18px;">
            🖨️ طباعة
        </button>

        {{-- زر تعديل --}}
        <a href="{{ route('experts.edit', $user->id) }}" class="btn btn-outline-warning btn-sm" style="font-size:13px; padding:10px 18px;">
            ✏️ تعديل البيانات
        </a>
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

        {{-- ===== قسم وثيقة الإقرار ===== --}}
        @if($declaration)
            @if($declaration->isSigned())
            {{-- موقعة --}}
            <div class="declaration-card signed">
                <div class="dec-header">
                    <div class="dec-title">📋 وثيقة الشروط والأحكام وإقرار السرية</div>
                    <span class="dec-badge badge-signed">✅ موقعة</span>
                </div>

                <div class="dec-info-grid">
                    <div class="dec-info-item">
                        <div class="dec-info-label">الاسم الكامل</div>
                        <div class="dec-info-value">{{ $declaration->full_name }}</div>
                    </div>
                    <div class="dec-info-item">
                        <div class="dec-info-label">رقم الهوية</div>
                        <div class="dec-info-value">{{ $declaration->national_id }}</div>
                    </div>
                    <div class="dec-info-item">
                        <div class="dec-info-label">رقم الجوال</div>
                        <div class="dec-info-value">{{ $declaration->phone }}</div>
                    </div>
                    <div class="dec-info-item">
                        <div class="dec-info-label">تاريخ التوقيع</div>
                        <div class="dec-info-value">{{ $declaration->signed_at->format('d/m/Y - H:i') }}</div>
                    </div>
                </div>

                @if($declaration->signature)
                <div style="margin: 12px 0;">
                    <div class="dec-info-label" style="margin-bottom:6px;">التوقيع الرقمي:</div>
                    <div style="border: 1px solid #d1e7dd; border-radius: 8px; padding: 8px; background: white; display: inline-block;">
                        <img src="{{ $declaration->signature }}" style="max-height: 70px; max-width: 300px;" alt="توقيع الخبير">
                    </div>
                </div>
                @endif

                <div class="dec-actions">
                    @if($declaration->pdf_path)
                        <a href="{{ route('declaration.download', ['token' => $declaration->token]) }}"
                           class="btn-gold" target="_blank">
                            ⬇️ تحميل PDF الإقرار الموقع
                        </a>
                    @endif
                    <a href="{{ route('declaration.show', ['token' => $declaration->token]) }}"
                       class="btn btn-outline-secondary btn-sm" target="_blank">
                        👁️ عرض الوثيقة
                    </a>
                </div>
            </div>

            @else
            {{-- أُرسلت لكن لم توقع --}}
            <div class="declaration-card unsigned">
                <div class="dec-header">
                    <div class="dec-title">📋 وثيقة الشروط والأحكام وإقرار السرية</div>
                    <span class="dec-badge badge-unsigned">⏳ في انتظار التوقيع</span>
                </div>
                <p style="font-size:13px; color:#666; margin: 0;">
                    تم إرسال رابط الوثيقة للخبير — لم يوقع عليها بعد.
                    يمكنك إعادة الإرسال من الأعلى لو احتجت.
                </p>
                <div class="dec-actions" style="margin-top: 12px;">
                    <a href="{{ route('declaration.show', ['token' => $declaration->token]) }}"
                       class="btn btn-outline-secondary btn-sm" target="_blank">
                        👁️ عرض رابط الوثيقة
                    </a>
                </div>
            </div>
            @endif

        @else
        {{-- لم ترسل بعد --}}
        <div class="declaration-card" style="border-color: #dc2626; background: #fff5f5;">
            <div class="dec-header">
                <div class="dec-title">📋 وثيقة الشروط والأحكام وإقرار السرية</div>
                <span class="dec-badge badge-not-sent">❌ لم ترسل بعد</span>
            </div>
            <p style="font-size:13px; color:#666; margin:0;">
                اضغط على زر <strong>"إرسال وثيقة الإقرار"</strong> في الأعلى لإرسال الوثيقة للخبير.
            </p>
        </div>
        @endif

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
