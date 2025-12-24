@extends('layouts.master')
@section('title', 'مراحل الأسئلة')

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
        <h4 class="content-title mb-1 fw-bold text-primary">
            <i class="bx bx-layer"></i> مراحل الأسئلة
        </h4>
        <small class="text-muted">إدارة مراحل الأسئلة داخل التقييم</small>
    </div>

    @can('question_steps_create')
        <div>
            <a href="{{ route('question_steps.create') }}"
               class="btn btn-primary btn-sm d-flex align-items-center gap-1"
               style="background-color:#c1953e; border-color:#c1953e;">
                <i class="bx bx-plus-circle fs-5"></i>
                <span>إضافة مرحلة جديدة</span>
            </a>
        </div>
    @endcan
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">قائمة المراحل</h5>
        <small class="text-muted">جميع مراحل الأسئلة</small>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table id="stepsTable" class="table table-hover table-striped text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم (عربي)</th>
                        <th>الاسم (إنجليزي)</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steps as $key => $step)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $step->name_ar }}</td>
                            <td>{{ $step->name_en }}</td>
                            <td>{{ $step->sort_order }}</td>
                            <td>
                                @if($step->is_active)
                                    <span class="badge bg-success">مفعّل</span>
                                @else
                                    <span class="badge bg-danger">غير مفعّل</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">

                                    @can('question_steps_edit')
                                        <a href="{{ route('question_steps.edit',$step->id) }}"
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                    @endcan

                                    @can('question_steps_delete')
                                        <form action="{{ route('question_steps.destroy',$step->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @endcan

                                </div>
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

<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    let table = $('#stepsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json'
        },
        pageLength: 10,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
        buttons: [
            { extend: 'copy', text: '📋 نسخ', className: 'btn-sm mx-1' },
            { extend: 'excel', text: '📊 Excel', className: 'btn-sm mx-1' },
            { extend: 'pdf', text: '📄 PDF', className: 'btn-sm mx-1' },
            { extend: 'print', text: '🖨️ طباعة', className: 'btn-sm mx-1' }
        ]
    });

    $('.dt-buttons').addClass('d-flex flex-wrap gap-2 align-items-center');
});
</script>
@endsection
