@extends('layouts.master')
@section('title', 'تعديل جهات الاتصال')

@section('css')
<style>
    .contact-form-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 25px;
    }

    .form-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 5px;
    }

    label.form-label {
        font-weight: 500;
        color: #333;
    }

    input.form-control, textarea.form-control {
        border-radius: 10px;
        padding: 10px 14px;
        min-height: 45px;
        width: 100%;
    }

    .social-media-item {
        border: 1px solid #e9ecef;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        position: relative;
    }

    .remove-social {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        color: red;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-edit"></i> تعديل جهات الاتصال</h4>
        <small class="text-muted">قم بتحديث معلومات الاتصال</small>
    </div>
    <div>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="contact-form-card">
    <form action="{{ route('contacts.update', $contact->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-section mb-4">
            <h6 class="form-section-title">📞 معلومات الاتصال الأساسية</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ $contact->phone }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ $contact->email }}" required>
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">🌐 وسائل التواصل الاجتماعي</h6>

            <div id="social-media-wrapper">
            @php
                // إذا كان already array، خليها زي ما هي
                $socials = [];
                if(is_string($contact->social_media)) {
                    $socials = json_decode($contact->social_media, true) ?? [];
                } elseif(is_array($contact->social_media)) {
                    $socials = $contact->social_media;
                }
            @endphp


                @foreach($socials as $index => $social)
                    <div class="social-media-item">
                        <span class="remove-social">✖</span>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="social_media[{{ $index }}][name]" class="form-control" value="{{ $social['name'] }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الأيقونة (رابط الصورة)</label>
                                <input type="text" name="social_media[{{ $index }}][icon]" class="form-control" value="{{ $social['icon'] }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الرابط</label>
                                <input type="text" name="social_media[{{ $index }}][url]" class="form-control" value="{{ $social['url'] }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-social" class="btn btn-sm btn-success mt-2">
                <i class="bx bx-plus"></i> إضافة وسيلة تواصل جديدة
            </button>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary" style="background-color:#c1953e; border:none;">
                <i class="bx bx-save"></i> حفظ التعديلات
            </button>
            <a href="{{ route('contacts.index') }}" class="btn btn-light border">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    let socialIndex = {{ count($socials) }};

    document.getElementById('add-social').addEventListener('click', function() {
        let wrapper = document.getElementById('social-media-wrapper');

        let item = document.createElement('div');
        item.classList.add('social-media-item');
        item.innerHTML = `
            <span class="remove-social">✖</span>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">الاسم</label>
                    <input type="text" name="social_media[${socialIndex}][name]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الأيقونة (رابط الصورة)</label>
                    <input type="text" name="social_media[${socialIndex}][icon]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الرابط</label>
                    <input type="text" name="social_media[${socialIndex}][url]" class="form-control">
                </div>
            </div>
        `;
        wrapper.appendChild(item);

        socialIndex++;
    });

    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-social')) {
            e.target.closest('.social-media-item').remove();
        }
    });
</script>
@endsection
