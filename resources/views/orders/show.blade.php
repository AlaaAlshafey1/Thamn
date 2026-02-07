@extends('layouts.master')
@section('title', 'تفاصيل الطلب')

@section('css')
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background-color: #f5f5f5;
        }

        /* ====================== Invoice Container ====================== */
        .invoice {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        /* ====================== Header ====================== */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #c1953e;
            padding-bottom: 15px;
        }

        .invoice-header img {
            max-height: 80px;
        }

        .invoice-header .company-details h2 {
            color: #c1953e;
            margin: 0 0 5px 0;
        }

        .invoice-header .company-details p {
            margin: 2px 0;
            font-size: 14px;
        }

        /* ====================== Customer Info ====================== */
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #fdf6e3;
            padding: 15px;
            border-radius: 8px;
        }

        .invoice-info p {
            margin: 3px 0;
            font-size: 14px;
        }

        /* ====================== Table ====================== */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        .invoice-table th {
            background-color: #c1953e;
            color: #fff;
        }

        .invoice-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* ====================== Total ====================== */
        .invoice-total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            padding: 10px;
            background-color: #fdf6e3;
            border-radius: 8px;
        }

        /* ====================== Print Styles ====================== */
        @media print {
            body * {
                visibility: hidden;
            }

            #printableInvoice,
            #printableInvoice * {
                visibility: visible;
            }

            #printableInvoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        /* ====================== Evaluation Section ====================== */
        .evaluation-box {
            margin-top: 25px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .eval-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .eval-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .eval-card {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #e5e7eb;
        }

        .eval-card h6 {
            margin-bottom: 10px;
            font-weight: bold;
            color: #111827;
        }

        .eval-price {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
        }

        .eval-meta {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .eval-reason {
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.7;
            color: #374151;
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
        }

        .eval-empty {
            color: #9ca3af;
            font-style: italic;
            font-size: 14px;
        }

        /* ====================== Expert Form ====================== */
        .expert-form {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #d1d5db;
        }
    </style>
@endsection

@section('content')
    <div class="container py-3">

        <div class="mb-3 text-end">
            <button class="btn btn-warning" onclick="window.print()"
                style="background-color:#c1953e; border-color:#c1953e;">
                🖨️ طباعة الفاتورة
            </button>
        </div>

        <div id="printableInvoice" class="invoice">

            <!-- Header -->
            <div class="invoice-header">
                <img src="{{ URL::asset('assets/img/Logo.png') }}" alt="Logo">
                <div class="company-details">
                    <h2>ثمن</h2>
                    <p>الرياض</p>
                    <p>الهاتف: 0123456789</p>
                    <p>البريد الإلكتروني: info@thamn.sa</p>
                </div>
            </div>

            <!-- Customer & Order Info -->
            <div class="invoice-info">
                <div>
                    <p><strong>رقم الطلب:</strong> {{ $order->id }}</p>
                    <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d') }}</p>
                </div>
                <div>
                    <p><strong>المستخدم:</strong> {{ $order->user->first_name . ' ' . $order->user->last_name }}</p>
                    <p><strong>حالة الطلب:</strong> {{ $order->status }}</p>
                    <p><strong>الدفع:</strong> {{ $order->status ?? '-' }}</p>
                </div>
            </div>

            <!-- Product Images -->
            <div class="mb-4">
                <h5 class="eval-title">🖼️ صور المنتج</h5>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($order->files->where('type', 'image') as $image)
                        <div style="width: 150px; height: 150px; overflow: hidden; border-radius: 8px; border: 1px solid #ddd;">
                            <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $image->file_path) }}"
                                    style="width: 100%; height: 100%; object-fit: cover;" alt="Product Image">
                            </a>
                        </div>
                    @empty
                        <div class="eval-empty">لا توجد صور مرفقة لهذا الطلب</div>
                    @endforelse
                </div>
            </div>

            <!-- Order Items Table -->
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>السؤال</th>
                        <th>الإجابة</th>
                        {{-- <th>السعر (SAR)</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1;
                    $total = 0; @endphp
                    @foreach($order->details as $detail)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $detail->question->question_ar ?? '-' }}</td>
                            <td>{{ $detail->option->option_ar ?? $detail->value ?? '-' }}</td>
                            {{-- <td>{{ number_format($detail->price ?? 0,2) }}</td>
                            @php $total += $detail->price ?? 0; @endphp --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="evaluation-box">
                <div class="eval-title">📊 نتائج التقييم</div>

                <div class="eval-grid">

                    {{-- تقييم AI --}}
                    <div class="eval-card">
                        <h6>🤖 تقييم الذكاء الاصطناعي</h6>

                        @if($order->ai_price)
                            <div class="eval-price">
                                {{ number_format($order->ai_price, 2) }} SAR
                            </div>

                            <div class="eval-meta">
                                نطاق السعر:
                                {{ number_format($order->ai_min_price, 2) }} -
                                {{ number_format($order->ai_max_price, 2) }} SAR
                            </div>

                            <div class="eval-meta">
                                نسبة الثقة: {{ $order->ai_confidence }}%
                            </div>

                            <div class="eval-reason">
                                {{ $order->ai_reasoning }}
                            </div>
                        @else
                            <div class="eval-empty">لم يتم إجراء تقييم بالذكاء الاصطناعي بعد</div>
                            @if(auth()->user()->hasAnyRole(['superadmin', 'admin']) || auth()->user()->hasRole('expert'))
                                <form method="POST" action="{{ route('orders.ai.evaluate', $order->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 mt-2">
                                        🤖 تشغيل تقييم AI الآن
                                    </button>
                                </form>
                            @endif
                        @endif


                    </div>


                    {{-- تقييم الخبير --}}
                    <div class="eval-card">
                        <h6>🧑‍💼 تقييم الخبير</h6>

                        @if($order->expert_price)
                            <div class="eval-price">
                                {{ number_format($order->expert_price, 2) }} SAR
                            </div>

                            <div class="eval-meta">
                                نطاق السعر:
                                {{ number_format($order->expert_min_price, 2) }} -
                                {{ number_format($order->expert_max_price, 2) }} SAR
                            </div>

                            <div class="eval-reason">
                                {{ $order->expert_reasoning }}
                            </div>
                        @else
                            <div class="eval-empty">لم يتم تقييم الطلب من قبل الخبير بعد</div>
                        @endif

                        {{-- فورم الخبير --}}
                        @if(auth()->user()->hasRole('expert'))
                            <form method="POST" action="{{ route('orders.expert.evaluate', $order->id) }}" class="expert-form">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="fw-bold">السعر الأدنى</label>
                                        <input type="number" name="expert_min_price" class="form-control" step="0.01"
                                            value="{{ old('expert_min_price', $order->expert_min_price) }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="fw-bold">السعر الأعلى</label>
                                        <input type="number" name="expert_max_price" class="form-control" step="0.01"
                                            value="{{ old('expert_max_price', $order->expert_max_price) }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="fw-bold">السعر المقترح</label>
                                        <input type="number" name="expert_price" class="form-control" step="0.01"
                                            value="{{ old('expert_price', $order->expert_price ?? $order->ai_price) }}">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="fw-bold">سبب التقييم</label>
                                    <textarea name="expert_reasoning" class="form-control" rows="3"
                                        required>{{ old('expert_reasoning', $order->expert_reasoning) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    💾 اعتماد تقييم الخبير
                                </button>
                            </form>
                        @endif
                    </div>


                    @if(auth()->user()->hasAnyRole(['superadmin', 'admin']) || $order->thamn_price)
                        <div class="evaluation-box mt-4">
                            <div class="eval-title">🏷️ تقييم ثمن المعتمد</div>

                            @if($order->thamn_price)
                                <div class="eval-card">
                                    <div class="eval-price">
                                        {{ number_format($order->thamn_price, 2) }} SAR
                                    </div>

                                    <div class="eval-meta">
                                        (متوسط تقييم AI وتقييم الخبير)
                                    </div>

                                    @if($order->thamn_reasoning)
                                        <div class="eval-reason">
                                            {{ $order->thamn_reasoning }}
                                        </div>
                                    @endif

                                    <div class="eval-meta mt-2">
                                        تم الاعتماد بواسطة:
                                        <strong>{{ $order->thamnUser->first_name ?? '-' }}</strong>
                                        <br>
                                        بتاريخ: {{ $order->thamn_at }}
                                    </div>
                                </div>
                            @else
                                <div class="eval-empty mb-2">
                                    لم يتم اعتماد تقييم ثمن بعد
                                </div>

                                <form method="POST" action="{{ route('orders.thamn.evaluate', $order->id) }}">
                                    @csrf

                                    <textarea name="thamn_reasoning" class="form-control mb-2" rows="3"
                                        placeholder="ملاحظة أو سبب اعتماد التقييم (اختياري)"></textarea>

                                    <button class="btn btn-warning w-100">
                                        ✔ اعتماد تقييم ثمن
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            <div class="invoice-total">
                @if(auth()->user()->hasRole('expert'))

                    <form method="POST" action="{{ route('orders.expert.evaluate', $order->id) }}"
                        class="d-flex flex-column align-items-end gap-2">
                        @csrf

                        <div class="d-flex align-items-center gap-2">
                            <label class="fw-bold">السعر (SAR):</label>

                            <input type="number" name="expert_price" class="form-control" style="width:150px" step="0.01"
                                min="0" value="{{ $order->expert_price ?? $order->total_price }}">
                        </div>

                        <textarea name="expert_reasoning" class="form-control mt-2" rows="3"
                            placeholder="سبب التقييم (إجباري)">{{ old('expert_reasoning', $order->expert_reasoning) }}</textarea>

                        <button type="submit" class="btn btn-success mt-2">
                            💾 حفظ التقييم
                        </button>
                    </form>

                @else
                    الإجمالي: {{ number_format($order->total_price, 2) }} SAR
                @endif
            </div>


        </div>
    </div>
@endsection