@extends('layouts.master')
@section('title', 'إدارة طلبات الاسترداد')

@section('css')
@include('partials.modern-table-css')
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
    .dataTables-wrapper {
        overflow-x: auto;
        width: 100%;
    }
    #refundsTable {
        min-width: 1200px;
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
        <div class="table-responsive dataTables-wrapper">
            <table id="refundsTable" class="table modern-table text-center align-middle w-100">
                <thead class="bg-light text-center">
                    <tr>
                        <th>#</th>
                        <th>العميل</th>
                        <th>رقم الطلب</th>
                        <th>المبلغ</th>
                        <th>بيانات الدفع</th>
                        <th>الحالة</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($refunds as $key => $refund)
                        <tr>
                            <td class="fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $refund->user->first_name }} {{ $refund->user->last_name }}</div>
                                <small class="text-muted">{{ $refund->user->phone }}</small>
                            </td>
                            <td class="fw-bold text-dark">#{{ $refund->order_id }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ number_format($refund->amount, 2) }}</span>
                                <small class="text-muted">SAR</small>
                            </td>
                            <td>
                                <div class="small text-start d-inline-block">
                                    <strong>البنك:</strong> {{ $refund->bank_name }}<br>
                                    <strong>الآيبان:</strong> {{ $refund->iban }}<br>
                                    <strong>الاسم:</strong> {{ $refund->account_holder_name }}
                                </div>
                            </td>
                            <td>
                                @if($refund->status == 'pending')
                                    <span class="badge bg-warning-transparent text-warning px-3 py-2"><i class="bx bx-time-five ml-1"></i> قيد الانتظار</span>
                                @elseif($refund->status == 'processed')
                                    <span class="badge bg-success-transparent text-success px-3 py-2"><i class="bx bx-check-circle ml-1"></i> تم الاسترداد</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger px-3 py-2"><i class="bx bx-x-circle ml-1"></i> مرفوض</span>
                                @endif
                            </td>
                            <td>
                                @if($refund->status == 'pending')
                                    <div class="d-flex justify-content-center gap-2">
                                        <form action="{{ route('refunds.process', $refund->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إتمام عملية التحويل للعميل؟')">
                                            @csrf
                                            <input type="hidden" name="status" value="processed">
                                            <button type="submit" class="btn btn-sm btn-success-light fw-bold px-3"><i class="bx bx-check ml-1"></i> تأكيد التحويل</button>
                                        </form>
                                        <form action="{{ route('refunds.process', $refund->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger-light fw-bold px-3"><i class="bx bx-x ml-1"></i> رفض</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small">تمت المعالجة</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light text-center">
                    <tr>
                        <th>#</th>
                        <th>العميل</th>
                        <th>رقم الطلب</th>
                        <th>المبلغ</th>
                        <th>بيانات الدفع</th>
                        <th>الحالة</th>
                        <th>التحكم</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#refundsTable tfoot th').each(function () {
        var title = $(this).text();
        if(title !== 'التحكم' && title !== '#' && title !== 'بيانات الدفع') {
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="بحث ' + title + '" />');
        } else {
            $(this).html('');
        }
    });

    $('#refundsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
        pageLength: 10,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
        buttons: [
            { extend: 'copy', text: '📋 نسخ', className: 'btn-sm mx-1' },
            { extend: 'excel', text: '📊 Excel', className: 'btn-sm mx-1' },
            { extend: 'pdf', text: '📄 PDF', className: 'btn-sm mx-1' },
            { extend: 'print', text: '🖨️ طباعة', className: 'btn-sm mx-1' }
        ],
        initComplete: function () {
            // Apply the search
            this.api().columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }
    });
});
</script>
@endsection
