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

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>رصيدك الحالي:</strong>
                {{ number_format(auth()->user()->balance, 2) }} ريال
            </div>
        </div>
    </div>

    <form action="{{ route('withdrawals.store') }}" method="POST">
        @csrf

        <div class="form-section mb-4">
            <h6 class="form-section-title">💰 بيانات السحب</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">المبلغ المطلوب</label>
                    <input type="number" name="amount" class="form-control" min="1" max="{{ auth()->user()->balance }}" required>
                    <small class="text-muted">لا يمكن طلب مبلغ أكبر من رصيدك الحالي.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">طريقة السحب</label>
                    <select name="method" class="form-select wide-select" required>
                        <option value="">اختر الطريقة</option>
                        <option value="bank">حساب بنكي</option>
                        <option value="wallet">محفظة إلكترونية</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">ملاحظات (اختياري)</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">إرسال الطلب</button>
            <a href="{{ route('withdrawals.my') }}" class="btn btn-light border">إلغاء</a>
        </div>
    </form>
</div>
@endsection
