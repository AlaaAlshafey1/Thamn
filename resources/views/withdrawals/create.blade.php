@extends('layouts.master')
@section('title', 'طلب سحب رصيد')

@section('css')
<style>
    .user-form-card {
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
    input.form-control, select.form-select, textarea.form-control {
        border-radius: 10px;
        padding: 10px 14px;
        min-height: 45px;
        width: 100%;
    }
    select.form-select {
        background-color: #fff;
        border: 1px solid #ced4da;
        font-size: 15px;
    }
    .wide-select {
        width: 100%;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-wallet"></i> طلب سحب رصيد</h4>
        <small class="text-muted">قم بتقديم طلب سحب الرصيد الخاص بك</small>
    </div>
    <div>
        <a href="{{ route('withdrawals.my') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="user-form-card">

    {{-- alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary-gradient text-white shadow-sm border-0 rounded-3">
                <div class="card-body p-4 text-center">
                    <div class="mb-2 op-7">رصيدك الحالي القابل للسحب</div>
                    <h2 class="fw-bold mb-0">{{ number_format(auth()->user()->balance, 2) }} <small class="fs-14">SAR</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center">
                <i class="bx bx-info-circle fs-30 ml-3"></i>
                <div>
                    <strong>تنبيه:</strong>
                    سيتم مراجعة طلبك من قبل الإدارة، وتأكد من تحديث بياناتك البنكية في ملفك الشخصي لتجنب أي تأخير.
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('withdrawals.store') }}" method="POST">
        @csrf

        <div class="form-section mb-4">
            <h6 class="form-section-title">💰 تفاصيل طلب السحب</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">المبلغ المطلوب سحبه</label>
                    <div class="input-group">
                        <input type="number" name="amount" class="form-control" min="1" max="{{ auth()->user()->balance }}" placeholder="أدخل المبلغ" required>
                        <span class="input-group-text bg-light text-muted">SAR</span>
                    </div>
                    <small class="text-muted mt-1 d-block">الحد الأقصى المتاح لك هو {{ number_format(auth()->user()->balance, 2) }} ريال</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">طريقة السحب المفضلة</label>
                    <select name="method" class="form-select wide-select" required>
                        <option value="bank" selected>حساب بنكي (الافتراضي)</option>
                        <option value="wallet">محفظة إلكترونية</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">ملاحظات إضافية للأدمن (اختياري)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="إذا كان لديك أي تعليمات خاصة..."></textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                <i class="bx bx-send ml-1"></i> إرسال الطلب الآن
            </button>
            <a href="{{ route('withdrawals.my') }}" class="btn btn-light border px-4">إلغاء</a>
        </div>
    </form>
</div>
@endsection
