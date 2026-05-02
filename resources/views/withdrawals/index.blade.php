@extends('layouts.master')
@section('title', 'إدارة طلبات السحب')

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
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-wallet"></i> إدارة طلبات السحب</h4>
        <small class="text-muted">قبول أو رفض طلبات السحب</small>
    </div>
</div>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">سجلات عمليات السحب</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="withdrawalsTable" class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>الخبير</th>
                        <th class="text-center">المبلغ</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">التاريخ</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $key => $req)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-primary-transparent text-primary rounded-circle ml-2">
                                        {{ substr($req->user->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $req->user->first_name }} {{ $req->user->last_name }}</div>
                                        <small class="text-muted">{{ $req->user->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary">{{ number_format($req->amount, 2) }}</span>
                                <small class="text-muted">SAR</small>
                            </td>
                            <td class="text-center">
                                @if($req->status == 'pending')
                                    <span class="badge bg-warning-transparent text-warning px-3 py-2"><i class="bx bx-time-five ml-1"></i> قيد الانتظار</span>
                                @elseif($req->status == 'approved')
                                    <span class="badge bg-success-transparent text-success px-3 py-2"><i class="bx bx-check-circle ml-1"></i> تمت الموافقة</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger px-3 py-2"><i class="bx bx-x-circle ml-1"></i> مرفوض</span>
                                @endif
                            </td>
                            <td class="text-center text-muted small">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                @if($req->status == 'pending')
                                    <div class="btn-group">
                                        <form action="{{ route('withdrawals.approve', $req->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success-light btn-icon" title="موافقة">
                                                <i class="bx bx-check fs-18"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('withdrawals.reject', $req->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-danger-light btn-icon" title="رفض">
                                                <i class="bx bx-x fs-18"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted">تمت المعالجة</span>
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
    $('#withdrawalsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
        pageLength: 10,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
        buttons: [
            { extend: 'copy', text: '📋 نسخ', className: 'btn-sm mx-1' },
            { extend: 'excel', text: '📊 Excel', className: 'btn-sm mx-1' },
            { extend: 'pdf', text: '📄 PDF', className: 'btn-sm mx-1' },
            { extend: 'print', text: '🖨️ طباعة', className: 'btn-sm mx-1' }
        ],
    });
});
</script>
@endsection
