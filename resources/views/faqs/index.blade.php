@extends('layouts.master')
@section('title','الأسئلة الشائعة')

@section('css')
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
    .dt-buttons .btn:hover { background-color: #a67f31 !important; }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="mb-1 fw-bold text-primary">إدارة الأسئلة الشائعة</h4>
        <small class="text-muted">عرض جميع الأسئلة والتحكم بها</small>
    </div>
    <a href="{{ route('faqs.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
        <i class="bx bx-plus-circle fs-5"></i> إضافة سؤال جديد
    </a>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table id="faqsTable" class="table table-hover table-striped text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الفئة</th>
                        <th>السؤال (AR)</th>
                        <th>السؤال (EN)</th>
                        <th>الإجابة (AR)</th>
                        <th>الإجابة (EN)</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faqs as $key => $faq)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $faq->category }}</td>
                            <td>{!! Str::limit($faq->question_ar, 50) !!}</td>
                            <td>{!! Str::limit($faq->question_en, 50) !!}</td>
                            <td>{!! Str::limit($faq->answer_ar, 50) !!}</td>
                            <td>{!! Str::limit($faq->answer_en, 50) !!}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('faqs.edit', $faq->id) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">
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
    $('#faqsTable').DataTable({
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
});
</script>
@endsection
