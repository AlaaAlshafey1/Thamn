@extends('layouts.master')
@section('title', 'طلب سحب رصيد جديد')

@section('css')
<style>
    .form-wrapper {
        max-width: 650px;
        margin: 0 auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 40px;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 2px solid #f1f1f1;
        font-size: 1.05rem;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.1);
    }
    .input-group-text {
        background: #f8f9fa;
        border: 2px solid #f1f1f1;
        border-left: none;
        border-radius: 0 12px 12px 0;
        font-weight: bold;
        color: #555;
    }
    .form-control.amount-input {
        border-right: none;
        font-size: 1.5rem;
        font-weight: bold;
        color: #1e3c72;
    }
    .submit-btn {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 1.1rem;
        font-weight: 600;
        width: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
    }
    .balance-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        border: 1px solid #eef0f2;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pt-5 pb-5">

    <div class="form-wrapper">
        <div class="text-center mb-4">
            <div class="avatar avatar-lg bg-primary-transparent text-primary rounded-circle mb-3 mx-auto" style="width:70px;height:70px;display:flex;align-items:center;justify-content:center;">
                <i class="bx bx-money" style="font-size:2rem;"></i>
            </div>
            <h3 class="fw-bold text-dark">سحب رصيد</h3>
            <p class="text-muted">قم بتحديد المبلغ الذي ترغب بسحبه لحسابك البنكي</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 10px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="balance-info">
            <div>
                <small class="text-muted d-block mb-1">رصيدك المتاح للسحب</small>
                <h5 class="mb-0 fw-bold text-success">{{ number_format(auth()->user()->balance, 2) }} <small>SAR</small></h5>
            </div>
            <a href="{{ route('withdrawals.my') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="bx bx-list-ul"></i> سجل السحوبات
            </a>
        </div>

        <form action="{{ route('withdrawals.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">المبلغ المطلوب <span class="text-danger">*</span></label>
                <div class="input-group" style="flex-direction: row-reverse;">
                    <span class="input-group-text" style="border-radius: 12px 0 0 12px; border-right: none; border-left: 2px solid #f1f1f1;">SAR</span>
                    <input type="number" name="amount" class="form-control amount-input text-start" style="border-radius: 0 12px 12px 0; border-left: none;" min="1" max="{{ auth()->user()->balance }}" step="0.01" placeholder="0.00" required>
                </div>
                <small class="text-muted mt-2 d-block"><i class="bx bx-info-circle"></i> يمكنك سحب أي مبلغ بحد أقصى للرصيد المتاح.</small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">طريقة الاستلام <span class="text-danger">*</span></label>
                <select name="method" class="form-select" required>
                    <option value="bank" selected>حساب بنكي (الافتراضي المضاف في ملفك)</option>
                    <option value="wallet">محفظة إلكترونية</option>
                    <option value="other">أخرى</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">ملاحظات إضافية (اختياري)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية بخصوص التحويل البنكي..."></textarea>
            </div>

            <button type="submit" class="submit-btn mt-2">
                تأكيد طلب السحب <i class="bx bx-check-shield ml-1"></i>
            </button>
            <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.85rem;">
                <i class="bx bx-lock-alt"></i> يتم مراجعة وتحويل الطلب بأمان عبر إدارة منصة ثمن.
            </p>
        </form>
    </div>

</div>
@endsection
