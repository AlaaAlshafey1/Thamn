@extends('layouts.master')
@section('title','إدارة المدفوعات')

@section('css')
@include('partials.modern-table-css')
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
#paymentsTable {
    min-width: 1000px;
}
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-money"></i> إدارة المدفوعات</h4>
        <small class="text-muted">عرض جميع المدفوعات والتحكم بها</small>
    </div>
</div>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">قائمة المدفوعات</h5>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="table-responsive dataTables-wrapper">
            <table id="paymentsTable" class="table modern-table text-center align-middle w-100">
                <thead class="bg-light text-center">
                    <tr>
                        <th>#</th>
                        <th>رقم الطلب</th>
                        <th>المستخدم</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>تاريخ الدفع</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $key => $payment)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="fw-bold">#{{ $payment->order->id ?? '-' }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $payment->order->user->first_name ?? '-' }}</span>
                        </td>
                        <td><strong class="text-success">{{ number_format($payment->amount,2) }}</strong> <small>SAR</small></td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge bg-success-transparent text-success px-3 py-2"><i class="bx bx-check-circle ml-1"></i> مدفوع</span>
                            @elseif($payment->status === 'failed')
                                <span class="badge bg-danger-transparent text-danger px-3 py-2"><i class="bx bx-x-circle ml-1"></i> فشل</span>
                            @else
                                <span class="badge bg-warning-transparent text-warning px-3 py-2">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-info-light btn-icon" title="عرض التفاصيل">
                                    <i class="bx bx-show fs-18"></i>
                                </a>
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger-light btn-icon" onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف">
                                        <i class="bx bx-trash fs-18"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light text-center">
                    <tr>
                        <th>#</th>
                        <th>رقم الطلب</th>
                        <th>المستخدم</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>تاريخ الدفع</th>
                        <th>التحكم</th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3">
                {{ $payments->links() }}
            </div>
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
    $('#paymentsTable tfoot th').each(function () {
        var title = $(this).text();
        if(title !== 'التحكم' && title !== '#') {
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="بحث ' + title + '" />');
        } else {
            $(this).html('');
        }
    });

    $('#paymentsTable').DataTable({
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
