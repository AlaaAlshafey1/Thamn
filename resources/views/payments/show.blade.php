@extends('layouts.master')
@section('title','تفاصيل الدفع')

@section('content')
<div class="container py-3">
    <div class="card p-3">
        <h4>تفاصيل الدفع #{{ $payment->id }}</h4>
        <hr>
        <p><strong>رقم الطلب:</strong> {{ $payment->order->id ?? '-' }}</p>
        <p><strong>المستخدم:</strong> {{ $payment->order->user->first_name ?? '-' }}</p>
        <p><strong>المبلغ:</strong> {{ number_format($payment->amount,2) }} SAR</p>
        <p><strong>الحالة:</strong> {{ $payment->status }}</p>
        <p><strong>تاريخ الدفع:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}</p>

        <h5 class="mt-3">تفاصيل الطلب المرتبط</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>السؤال</th>
                    <th>الإجابة</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach($payment->order->details as $detail)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $detail->question->question_ar ?? '-' }}</td>
                    <td>{{ $detail->option->option_ar ?? $detail->value ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('payments.index') }}" class="btn btn-secondary mt-2">العودة للقائمة</a>
    </div>
</div>
@endsection
