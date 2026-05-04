@extends('layouts.master')
@section('title', 'طلب استرداد مبلغ')

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border" style="direction: rtl;">
    <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-refresh"></i> طلب استرداد مبلغ</h4>
    <small class="text-muted">يرجى إدخال بياناتك البنكية ليتم تحويل المبلغ إليك</small>
</div>
@endsection

@section('content')
<div class="row justify-content-center" style="direction: rtl;">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="bx bx-info-circle ml-1"></i>
                    سيتم استرداد مبلغ <strong>{{ number_format($order->total_price, 2) }} ريال</strong> للطلب رقم #{{ $order->id }}
                </div>

                <form action="{{ route('refunds.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم البنك</label>
                        <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" placeholder="مثال: مصرف الراجحي" required>
                        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم الآيبان (IBAN)</label>
                        <input type="text" name="iban" class="form-control @error('iban') is-invalid @enderror" placeholder="SA..." required>
                        @error('iban') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم صاحب الحساب</label>
                        <input type="text" name="account_holder_name" class="form-control @error('account_holder_name') is-invalid @enderror" placeholder="الاسم كما يظهر في البنك" required>
                        @error('account_holder_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">
                            إرسال طلب الاسترداد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
