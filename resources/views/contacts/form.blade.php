@extends('layouts.master')
@section('title', isset($contact) ? 'تعديل جهة الاتصال' : 'إضافة جهة اتصال')

@section('content')

<div class="contact-form-card">

    {{-- رسائل --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- تحديد الـ action --}}
    <form action="{{ isset($contact) ? route('contacts.update', $contact->id) : route('contacts.store') }}" method="POST">
        @csrf
        @if(isset($contact)) @method('PUT') @endif


        {{-- ================= معلومات أساسية ================= --}}
        <div class="form-section mb-4">
            <h6 class="form-section-title">📞 معلومات الاتصال الأساسية</h6>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف</label>
                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone', $contact->phone ?? '') }}"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $contact->email ?? '') }}"
                        required
                    >
                </div>

            </div>
        </div>


        {{-- ================= Social Media ================= --}}
        <div class="form-section mb-4">
            <h6 class="form-section-title">🌐 وسائل التواصل الاجتماعي</h6>

            @php
                $socials = [];

                if(isset($contact)) {
                    if(is_string($contact->social_media)) {
                        $socials = json_decode($contact->social_media, true) ?? [];
                    } elseif(is_array($contact->social_media)) {
                        $socials = $contact->social_media;
                    }
                }
            @endphp

            <div id="social-media-wrapper">

                @foreach($socials as $index => $social)
                    <div class="social-media-item">
                        <span class="remove-social">✖</span>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="social_media[{{ $index }}][name]" class="form-control"
                                       value="{{ $social['name'] ?? '' }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الأيقونة</label>
                                <input type="text" name="social_media[{{ $index }}][icon]" class="form-control"
                                       value="{{ $social['icon'] ?? '' }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الرابط</label>
                                <input type="text" name="social_media[{{ $index }}][url]" class="form-control"
                                       value="{{ $social['url'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <button type="button" id="add-social" class="btn btn-success btn-sm mt-2">
                ➕ إضافة وسيلة جديدة
            </button>
        </div>


        {{-- ================= Buttons ================= --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($contact) ? 'تحديث' : 'حفظ' }}
            </button>

            <a href="{{ route('contacts.index') }}" class="btn btn-light border">
                إلغاء
            </a>
        </div>

    </form>
</div>

@endsection



@section('js')
<script>
let socialIndex = {{ count($socials ?? []) }};

document.getElementById('add-social').addEventListener('click', function() {

    let wrapper = document.getElementById('social-media-wrapper');

    let item = document.createElement('div');
    item.classList.add('social-media-item');

    item.innerHTML = `
        <span class="remove-social">✖</span>
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="social_media[${socialIndex}][name]" class="form-control" placeholder="الاسم">
            </div>
            <div class="col-md-4">
                <input type="text" name="social_media[${socialIndex}][icon]" class="form-control" placeholder="الأيقونة">
            </div>
            <div class="col-md-4">
                <input type="text" name="social_media[${socialIndex}][url]" class="form-control" placeholder="الرابط">
            </div>
        </div>
    `;

    wrapper.appendChild(item);
    socialIndex++;
});


document.addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-social')) {
        e.target.closest('.social-media-item').remove();
    }
});
</script>
@endsection
