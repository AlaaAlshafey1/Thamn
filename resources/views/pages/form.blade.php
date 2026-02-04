@extends('layouts.master')
@section('title', isset($page) ? 'تعديل المحتوى' : 'إنشاء محتوى')

@section('css')
<style>
    .page-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 30px;
        max-width: 900px;
        margin: auto;
    }

    .page-card h4 {
        font-weight: 700;
        color: #c1953e;
        margin-bottom: 25px;
    }

    label.form-label {
        font-weight: 600;
        color: #333;
    }

    .btn-submit {
        background-color: #c1953e;
        border: none;
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
    }

    .alert {
        border-radius: 6px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="page-card">
    <h4>{{ isset($page) ? 'تعديل محتوى ' . ucfirst($page->type) : 'إضافة محتوى جديد' }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <form id="pageForm" action="{{ isset($page) ? route('pages.update', $page->id) : route('pages.store') }}" method="POST">
        @csrf
        @if(isset($page)) @method('PUT') @endif

        <input type="hidden" name="type" value="{{ $type ?? $page->type ?? 'about' }}">

        {{-- CKEditor سيأخذ مكان هذه الـ textarea --}}
        <div class="mb-3">
            <label class="form-label">المحتوى بالعربية</label>
            <textarea name="content_ar" id="editor_ar" style="display:none;">{{ old('content_ar', $page->content_ar ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">المحتوى بالإنجليزية</label>
            <textarea name="content_en" id="editor_en" style="display:none;">{{ old('content_en', $page->content_en ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn-submit">
            <i class="bx bx-save"></i> {{ isset($page) ? 'تحديث' : 'حفظ' }}
        </button>
    </form>
</div>
@endsection

@section('js')
<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
let editorAr, editorEn;

document.addEventListener('DOMContentLoaded', function() {

    ClassicEditor.create(document.querySelector('#editor_ar'), {
        language: 'ar',
        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
    }).then(editor => { editorAr = editor; }).catch(error => { console.error(error); });

    ClassicEditor.create(document.querySelector('#editor_en'), {
        language: 'en',
        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
    }).then(editor => { editorEn = editor; }).catch(error => { console.error(error); });

    // قبل إرسال الفورم ضع المحتوى داخل textarea
    document.querySelector('#pageForm').addEventListener('submit', function(e){
        if(editorAr.getData().trim() === '') {
            alert('يرجى إدخال المحتوى بالعربية');
            e.preventDefault();
            return false;
        }
        if(editorEn.getData().trim() === '') {
            alert('يرجى إدخال المحتوى بالإنجليزية');
            e.preventDefault();
            return false;
        }

        document.querySelector('#editor_ar').value = editorAr.getData();
        document.querySelector('#editor_en').value = editorEn.getData();
    });

});
</script>
@endsection
