@extends('layouts.master')
@section('title', 'تفاصيل طلب السحب')

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<style>
    .expert-card {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 15px;
        color: white;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.2);
    }
    .info-label {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.7);
        margin-bottom: 2px;
    }
    .info-value {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .req-card {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        height: 100%;
        border: 1px solid #f1f1f1;
    }
    .amount-text {
        font-size: 2.5rem;
        font-weight: 900;
        color: #c1953e;
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f1f1;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-4 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-file"></i> تفاصيل طلب السحب رقم #{{ $request->id }}</h4>
        <small class="text-muted">مراجعة بيانات الخبير والطلبات التي قام بتقييمها</small>
    </div>
    <div>
        <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع للطلبات</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bx bx-check-circle ml-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bx bx-x-circle ml-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Expert Info -->
        <div class="col-lg-6">
            <div class="expert-card h-100">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-light" style="border-color: rgba(255,255,255,0.1) !important;">
                    <div class="avatar avatar-lg bg-white text-primary rounded-circle ml-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                        {{ substr($expert->first_name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">{{ $expert->first_name }} {{ $expert->last_name }}</h4>
                        <span class="badge bg-light text-primary">خبير معتمد</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">رقم الجوال</div>
                        <div class="info-value" dir="ltr" style="text-align: right;">{{ $expert->phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">البريد الإلكتروني</div>
                        <div class="info-value">{{ $expert->email ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">اسم البنك</div>
                        <div class="info-value">{{ $expert->bank_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">رقم الآيبان (IBAN)</div>
                        <div class="info-value" dir="ltr" style="text-align: right;">{{ $expert->iban ?? '-' }}</div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="info-label">الرصيد المتاح حالياً في المحفظة</div>
                        <div class="info-value text-warning" style="font-size: 1.5rem;">{{ number_format($expert->balance, 2) }} SAR</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Action -->
        <div class="col-lg-6">
            <div class="req-card text-center d-flex flex-column justify-content-center h-100">
                <h6 class="text-muted mb-2">المبلغ المطلوب سحبه</h6>
                <div class="amount-text mb-2">{{ number_format($request->amount, 2) }} <span style="font-size: 1.2rem;">SAR</span></div>
                
                <div class="mb-4">
                    @if($request->status == 'pending')
                        <span class="badge bg-warning-transparent text-warning px-3 py-2 fs-6"><i class="bx bx-time-five ml-1"></i> قيد المراجعة</span>
                    @elseif($request->status == 'approved')
                        <span class="badge bg-success-transparent text-success px-3 py-2 fs-6"><i class="bx bx-check-circle ml-1"></i> تمت الموافقة والتحويل</span>
                    @else
                        <span class="badge bg-danger-transparent text-danger px-3 py-2 fs-6"><i class="bx bx-x-circle ml-1"></i> مرفوض</span>
                    @endif
                </div>

                @if($request->notes)
                    <div class="alert alert-light text-start mx-auto mb-4" style="max-width: 80%;">
                        <strong>ملاحظات الخبير:</strong> <br>
                        {{ $request->notes }}
                    </div>
                @endif

                @if($request->status == 'pending')
                    <div class="d-flex justify-content-center gap-2 mt-auto flex-wrap">
                        <form action="{{ route('withdrawals.approve', $request->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="transfer_type" value="manual">
                            <button type="submit" class="btn btn-primary px-3 py-2 fw-bold shadow-sm" onclick="return confirm('هل أنت متأكد من الموافقة؟ (هذا الخيار يعني أنك ستقوم بتحويل المبلغ يدوياً من البنك)');">
                                <i class="bx bx-check ml-1"></i> موافقة (تحويل يدوي)
                            </button>
                        </form>

                        <form action="{{ route('withdrawals.approve', $request->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="transfer_type" value="auto">
                            <button type="submit" class="btn btn-success px-3 py-2 fw-bold shadow-sm" onclick="return confirm('هل أنت متأكد من الموافقة؟ سيتم محاولة تحويل المبلغ آلياً عبر Tap Payments.');">
                                <i class="bx bx-check-shield ml-1"></i> موافقة (تحويل آلي Tap)
                            </button>
                        </form>

                        <form action="{{ route('withdrawals.reject', $request->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger px-3 py-2 fw-bold shadow-sm" onclick="return confirm('هل أنت متأكد من رفض الطلب؟');">
                                <i class="bx bx-x-circle ml-1"></i> رفض
                            </button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-secondary mt-auto mx-auto" style="max-width: 80%;">
                        تمت معالجة هذا الطلب مسبقاً في {{ $request->updated_at->format('Y-m-d h:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Previous Withdrawal Requests History -->
    @if($previousWithdrawals->count() > 0)
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body p-4">
            <h5 class="section-title"><i class="bx bx-history text-primary"></i> سجل طلبات السحب السابقة للخبير</h5>
            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>رقم الطلب</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previousWithdrawals as $prevReq)
                        <tr>
                            <td class="fw-bold">#{{ $prevReq->id }}</td>
                            <td><span class="text-primary fw-bold">{{ number_format($prevReq->amount, 2) }}</span> SAR</td>
                            <td>
                                @if($prevReq->status == 'pending')
                                    <span class="badge bg-warning-transparent text-warning px-2 py-1">قيد المراجعة</span>
                                @elseif($prevReq->status == 'approved')
                                    <span class="badge bg-success-transparent text-success px-2 py-1">مقبول</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger px-2 py-1">مرفوض</span>
                                @endif
                            </td>
                            <td class="text-muted" dir="ltr">{{ $prevReq->created_at->format('Y-m-d h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Evaluated Orders Table -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body p-4">
            <h5 class="section-title"><i class="bx bx-list-check text-primary"></i> سجل الطلبات التي قام الخبير بتقييمها</h5>
            <div class="table-responsive mt-3">
                <table id="evaluatedOrdersTable" class="table table-hover table-striped align-middle text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>تاريخ التقييم</th>
                            <th>تقييم الخبير</th>
                            <th>التقييم الشامل المعتمد (ثمن)</th>
                            <th>تعديل السعر؟ (إعادة تقييم)</th>
                            <th>عرض الطلب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluatedOrders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->id }}</td>
                            <td>{{ $order->user->first_name ?? 'غير معروف' }}</td>
                            <td class="text-muted" dir="ltr">{{ $order->accepted_at ? \Carbon\Carbon::parse($order->accepted_at)->format('Y-m-d') : '-' }}</td>
                            <td><span class="text-primary fw-bold">{{ number_format($order->expert_price, 0) }}</span> SAR</td>
                            <td>
                                @if($order->thamn_price)
                                    <span class="text-success fw-bold">{{ number_format($order->thamn_price, 0) }}</span> SAR
                                @else
                                    <span class="badge bg-light text-muted">بانتظار تقييم الإدارة</span>
                                @endif
                            </td>
                            <td>
                                @if($order->thamn_price && $order->expert_price && $order->thamn_price != $order->expert_price)
                                    <span class="badge bg-danger-transparent text-danger px-2 py-1"><i class="bx bx-edit ml-1"></i> نعم، تم التعديل</span>
                                @elseif($order->thamn_price && $order->expert_price && $order->thamn_price == $order->expert_price)
                                    <span class="badge bg-success-transparent text-success px-2 py-1"><i class="bx bx-check ml-1"></i> مطابق</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info-light btn-icon" target="_blank">
                                    <i class="bx bx-link-external fs-16"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#evaluatedOrdersTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
            pageLength: 10,
            order: [[2, 'desc']]
        });
    });
</script>
@endsection
