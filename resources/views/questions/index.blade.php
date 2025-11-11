@extends('layouts.master')
@section('title', 'إدارة الأسئلة')

@section('css')
<!-- DataTables -->
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
        <h4 class="content-title mb-1 fw-bold text-primary">إدارة الأسئلة</h4>
        <small class="text-muted">عرض جميع الأسئلة والتحكم بها</small>
    </div>

    <div class="d-flex flex-wrap justify-content-start gap-2">
        <a href="{{ route('questions.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="background-color:#c1953e; border-color:#c1953e;">
            <i class="bx bx-plus-circle fs-5"></i> <span>إضافة سؤال جديد</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">قائمة الأسئلة</h5>
        <small class="text-muted">عرض جميع الأسئلة مع بياناتها</small>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table id="questionsTable" class="table table-hover table-striped text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الفئة</th>
                        <th>السؤال بالعربية</th>
                        <th>السؤال بالإنجليزية</th>
                        <th>نوع السؤال</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $key => $question)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $question->category->name_ar ?? '-' }}</td>
                            <td>{{ $question->question_ar }}</td>
                            <td>{{ $question->question_en ?? '-' }}</td>
                            <td>{{ ucfirst($question->type) }}</td>
                            <td>
                                @if($question->is_active)
                                    <span class="badge bg-success">مفعّل</span>
                                @else
                                    <span class="badge bg-danger">غير مفعّل</span>
                                @endif
                            </td>
                            <td>{{ $question->order }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{ route('questions.show', $question->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bx bx-show-alt"></i>
                                    </a>

                                    <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
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
    let table = $('#questionsTable').DataTable({
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
