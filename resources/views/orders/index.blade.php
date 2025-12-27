@extends('layouts.master')
@section('title','إدارة الطلبات')

@section('css')
<!-- DataTables CSS -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 30px;
}

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

#colvisList {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

#colvisList .form-check {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

#colvisList .form-check-input {
    margin: 0;
    transform: scale(1.1);
    cursor: pointer;
}

#colvisList .form-check-label {
    margin: 0;
    line-height: 1;
    font-size: 14px;
    color: #333;
    cursor: pointer;
}
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary">إدارة الطلبات</h4>
        <small class="text-muted">عرض جميع الطلبات والتحكم بها</small>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">قائمة الطلبات</h5>
        <small class="text-muted">عرض جميع الطلبات المسجلة</small>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table id="ordersTable" class="table table-hover table-striped text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>الحالة</th>
                        <th>الدفع</th>
                        <th>الإجمالي</th>
                        <th>تاريخ</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $key => $order)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $order->user->first_name  ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $order->status }}</span>
                        </td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">مدفوع</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge bg-danger">فشل</span>
                            @else
                                <span class="badge bg-warning">لم يتم</span>
                            @endif
                        </td>
                        <td>{{ number_format($order->total_price,2) }} SAR</td>
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('orders.show',$order->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="bx bx-show"></i>
                            </a>
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

<!-- Buttons Extension -->
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
    let table = $('#ordersTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
        pageLength: 10,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
        buttons: [
            { extend: 'copy', text: '📋 نسخ', className: 'btn-sm mx-1' },
            { extend: 'excel', text: '📊 Excel', className: 'btn-sm mx-1' },
            { extend: 'pdf', text: '📄 PDF', className: 'btn-sm mx-1' },
            { extend: 'print', text: '🖨️ طباعة', className: 'btn-sm mx-1' }
        ]
    });

    // تنسيق الأزرار
    $('.dt-buttons').addClass('d-flex flex-wrap gap-2 align-items-center');
    $('.dt-buttons .btn').addClass('btn-primary').css({
        'background-color': '#c1953e',
        'border-color': '#c1953e',
        'color': '#fff'
    });
});
</script>
@endsection
