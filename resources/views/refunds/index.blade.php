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

    .dt-buttons .btn:hover {
        background-color: #a67f31 !important;
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
                        <th class="text-center">التاريخ</th>
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
                                    <span class="badge bg-warning-transparent text-warning px-3 py-2"><i class="bx bx-time-five ml-1"></i> قيد الانتظار</span>
                                @elseif($refund->status == 'processed')
                                    <span class="badge bg-success-transparent text-success px-3 py-2"><i class="bx bx-check-circle ml-1"></i> تم الاسترداد</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger px-3 py-2"><i class="bx bx-x-circle ml-1"></i> مرفوض</span>
                                @endif
                            </td>
                            <td class="text-center text-muted small">{{ $refund->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                @if($refund->status == 'pending')
                                    <button class="btn btn-sm btn-primary-light" data-bs-toggle="modal" data-bs-target="#processModal{{ $refund->id }}">
                                        معالجة
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="processModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('refunds.process', $refund->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">معالجة طلب الاسترداد #{{ $refund->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start" style="direction: rtl;">
                                                        <div class="mb-3">
                                                            <label class="form-label">الحالة</label>
                                                            <select name="status" class="form-select">
                                                                <option value="processed">تم التحويل (موافقة)</option>
                                                                <option value="rejected">رفض الطلب</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">ملاحظات الأدمن</label>
                                                            <textarea name="admin_notes" class="form-control" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">لا يوجد إجراء</span>
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
<!-- DataTables Scripts -->
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
