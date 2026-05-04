@extends('layouts.master')
@section('title', 'إدارة طلبات الاسترداد')

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />

<style>
    .dt-buttons .btn {
        background-color: #c1953e !important;
        border: none !important;
        color: #fff !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-refresh"></i> إدارة طلبات الاسترداد</h4>
        <small class="text-muted">معالجة طلبات استرجاع الأموال للطلبات الملغاة</small>
    </div>
</div>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">سجلات عمليات الاسترداد</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-3">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table id="refundsTable" class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>العميل</th>
                        <th class="text-center">رقم الطلب</th>
                        <th class="text-center">المبلغ</th>
                        <th>بيانات الدفع</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($refunds as $key => $refund)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $refund->user->first_name }} {{ $refund->user->last_name }}</div>
                                <small class="text-muted">{{ $refund->user->phone }}</small>
                            </td>
                            <td class="text-center">#{{ $refund->order_id }}</td>
                            <td class="text-center">
                                <span class="fw-bold text-primary">{{ number_format($refund->amount, 2) }}</span>
                                <small class="text-muted">SAR</small>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>البنك:</strong> {{ $refund->bank_name }}<br>
                                    <strong>الآيبان:</strong> {{ $refund->iban }}<br>
                                    <strong>الاسم:</strong> {{ $refund->account_holder_name }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($refund->status == 'pending')
                                    <span class="badge bg-warning-transparent text-warning px-3 py-2">قيد الانتظار</span>
                                @elseif($refund->status == 'processed')
                                    <span class="badge bg-success-transparent text-success px-3 py-2">تم الاسترداد</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger px-3 py-2">مرفوض</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($refund->status == 'pending')
                                    <div class="d-flex justify-content-center gap-2">
                                        <form action="{{ route('refunds.process', $refund->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إتمام عملية التحويل للعميل؟')">
                                            @csrf
                                            <input type="hidden" name="status" value="processed">
                                            <button type="submit" class="btn btn-sm btn-success">تأكيد التحويل</button>
                                        </form>
                                        <form action="{{ route('refunds.process', $refund->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger">رفض</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small">تمت المعالجة</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#refundsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
        pageLength: 10,
    });
});
</script>
@endsection
