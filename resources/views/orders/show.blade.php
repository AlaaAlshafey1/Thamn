@extends('layouts.master')
@section('title', 'تفاصيل الطلب #' . $order->id)

@section('css')
    <style>
        .order-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            background: #fff;
        }
        .order-card .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 18px 25px;
            border-radius: 15px 15px 0 0;
        }
        .order-card .card-header h5 {
            margin: 0;
            font-weight: 700;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-label {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 3px;
        }
        .info-value {
            font-weight: 600;
            color: #333;
        }
        .product-img-container {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eee;
            transition: transform 0.3s ease;
        }
        .product-img-container:hover {
            transform: scale(1.02);
        }
        .product-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .evaluation-result {
            background: #fcf9f2;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e9dfc6;
        }
        .expert-form-container {
            background: #f8fbf9;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e0ede5;
        }
        .btn-gold {
            background-color: #c1953e;
            border-color: #c1953e;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 10px;
        }
        .btn-gold:hover {
            background-color: #a67f31;
            border-color: #a67f31;
            color: white;
        }
        .table-custom th {
            background-color: #f8f9fa;
            font-weight: 700;
            color: #555;
            text-align: center;
        }
        .table-custom td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto text-primary">إدارة الطلبات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تفاصيل الطلب #{{ $order->id }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <button type="button" class="btn btn-gold d-flex align-items-center gap-2" onclick="window.print()">
                <i class="bx bx-printer"></i> طباعة الفاتورة
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- الجانب الأيمن: بيانات العميل والمنتج --}}
        <div class="col-lg-8">
            {{-- كرت بيانات العميل --}}
            <div class="card order-card">
                <div class="card-header">
                    <h5><i class="bx bx-user text-warning"></i> بيانات العميل والطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">اسم العميل</div>
                            <div class="info-value">{{ $order->user->first_name . ' ' . $order->user->last_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">رقم الهاتف</div>
                            <div class="info-value text-ltr">{{ $order->user->phone ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">تاريخ الطلب</div>
                            <div class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">حالة الطلب</div>
                            <div class="info-value">
                                @if($order->status == 'expired')
                                    <span class="status-badge bg-danger-transparent text-danger">منتهي (لم يتم القبول)</span>
                                @elseif($order->status == 'refunded')
                                    <span class="status-badge bg-success-transparent text-success">تم الاسترداد</span>
                                @else
                                    <span class="status-badge bg-info-transparent text-info">{{ $order->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">حالة الدفع</div>
                            <div class="info-value">
                                @if($order->status !== 'pending' && $order->status !== 'failed')
                                    <span class="status-badge bg-success-transparent text-success">مدفوع</span>
                                @else
                                    <span class="status-badge bg-danger-transparent text-danger">غير مدفوع</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">نوع التقييم</div>
                            <div class="info-value">{{ $order->evaluation_type ?? 'عادي' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- كرت صور المنتج --}}
            <div class="card order-card">
                <div class="card-header">
                    <h5><i class="bx bx-images text-warning"></i> صور المنتج المرفقة</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($order->files->where('type', 'image') as $image)
                            <div class="col-md-3 col-6">
                                <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank" class="product-img-container d-block">
                                    <img src="{{ asset('storage/' . $image->file_path) }}" alt="Product Image">
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <img src="{{ URL::asset('assets/img/empty.png') }}" width="60" class="mb-2 opacity-50">
                                <p class="text-muted italic">لا توجد صور مرفقة لهذا الطلب</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- كرت تفاصيل الإجابات --}}
            <div class="card order-card">
                <div class="card-header">
                    <h5><i class="bx bx-list-check text-warning"></i> تفاصيل إجابات المستخدم</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>السؤال</th>
                                    <th>الإجابة المختارة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-right">{{ $detail->question->question_ar ?? '-' }}</td>
                                        <td class="text-right font-weight-bold">{{ $detail->option->option_ar ?? $detail->value ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- الجانب الأيسر: نتائج التقييم وفورم الخبير --}}
        <div class="col-lg-4">
            {{-- كرت نتائج التقييم الحالية --}}
            <div class="card order-card">
                <div class="card-header">
                    <h5><i class="bx bx-bar-chart-alt-2 text-warning"></i> نتائج التقييم</h5>
                </div>
                <div class="card-body">
                    {{-- Refund Section --}}
                    @if($order->status == 'expired' && $order->user_id == auth()->id())
                        @if(!$order->refundRequest)
                            <div class="alert alert-warning border-0 shadow-sm mb-4">
                                <p class="mb-2 small font-weight-bold">نعتذر منك، لم يتم قبول طلبك خلال 24 ساعة. يمكنك طلب استرداد المبلغ الآن.</p>
                                <a href="{{ route('refunds.create', $order->id) }}" class="btn btn-warning btn-sm btn-block fw-bold">
                                    <i class="bx bx-refresh"></i> طلب استرداد المبلغ
                                </a>
                            </div>
                        @else
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                <p class="mb-0 small font-weight-bold">لقد أرسلت طلب استرداد. حالة الطلب: 
                                    <strong>
                                        @if($order->refundRequest->status == 'pending') قيد المراجعة
                                        @elseif($order->refundRequest->status == 'processed') تم التحويل
                                        @else مرفوض @endif
                                    </strong>
                                </p>
                            </div>
                        @endif
                    @endif
                    {{-- تقييم AI --}}
                    <div class="mb-4">
                        <h6 class="font-weight-bold d-flex align-items-center gap-2 mb-3">
                            <span class="avatar avatar-sm br-7 bg-primary-transparent text-primary">AI</span>
                            تقييم الذكاء الاصطناعي
                        </h6>
                        @if($order->ai_price)
                            <div class="evaluation-result">
                                <div class="h4 font-weight-bold text-primary mb-1">{{ number_format($order->ai_price, 2) }} SAR</div>
                                <div class="small text-muted">نطاق السعر: {{ number_format($order->ai_min_price, 2) }} - {{ number_format($order->ai_max_price, 2) }}</div>
                                <div class="badge bg-success-transparent text-success mt-2">ثقة: {{ $order->ai_confidence }}%</div>
                                <hr class="my-2 border-top-0 border-light">
                                <div class="small text-dark">{{ $order->ai_reasoning }}</div>
                            </div>
                        @else
                            <div class="text-center py-3 bg-light rounded">
                                <p class="text-muted mb-0 small italic">لم يتم إجراء تقييم AI بعد</p>
                                @if(auth()->user()->hasAnyRole(['superadmin', 'admin', 'expert']))
                                    <form method="POST" action="{{ route('orders.ai.evaluate', $order->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2">تشغيل تقييم AI الآن</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- تقييم الخبير (عرض) --}}
                    <div>
                        <h6 class="font-weight-bold d-flex align-items-center gap-2 mb-3">
                            <span class="avatar avatar-sm br-7 bg-warning-transparent text-warning"><i class="bx bx-user"></i></span>
                            تقييم الخبير الحالي
                        </h6>
                        @if($order->expert_price)
                            <div class="evaluation-result border-warning-transparent bg-warning-transparent">
                                <div class="h4 font-weight-bold text-warning mb-1">{{ number_format($order->expert_price, 2) }} SAR</div>
                                <div class="small text-muted">نطاق السعر: {{ number_format($order->expert_min_price, 2) }} - {{ number_format($order->expert_max_price, 2) }}</div>
                                <hr class="my-2 border-top-0 border-light">
                                <div class="small text-dark">{{ $order->expert_reasoning }}</div>
                            </div>
                        @else
                            <div class="text-center py-3 bg-light rounded">
                                <p class="text-muted mb-0 small italic">بانتظار تقييم الخبير</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- فورم الخبير (فقط إذا كان المستخدم خبيراً) --}}
            @if(auth()->user()->hasRole('expert'))
                <div class="card order-card border-success">
                    <div class="card-header bg-success-transparent">
                        <h5 class="text-success"><i class="bx bx-edit text-success"></i> ضع تقييمك كخبير</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('orders.expert.evaluate', $order->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">السعر الموصى به (SAR)</label>
                                <input type="number" name="expert_price" class="form-control form-control-lg border-success text-success font-weight-bold" 
                                    step="0.01" min="0" value="{{ old('expert_price', $order->expert_price ?? $order->total_price) }}" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label small">السعر الأدنى</label>
                                    <input type="number" name="expert_min_price" class="form-control" 
                                        step="0.01" min="0" value="{{ old('expert_min_price', $order->expert_min_price ?? $order->expert_price * 0.8) }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">السعر الأعلى</label>
                                    <input type="number" name="expert_max_price" class="form-control" 
                                        step="0.01" min="0" value="{{ old('expert_max_price', $order->expert_max_price ?? $order->expert_price * 1.2) }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">سبب التقييم والملاحظات</label>
                                <textarea name="expert_reasoning" class="form-control" rows="4" 
                                    placeholder="اكتب بالتفصيل الأسباب التي بنيت عليها تقييمك..." required>{{ old('expert_reasoning', $order->expert_reasoning) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-block btn-lg shadow">
                                <i class="bx bx-check-circle"></i> اعتماد وحفظ التقييم
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- اعتماد تقييم ثمن (للأدمن فقط) --}}
            @if(auth()->user()->hasAnyRole(['superadmin', 'admin']))
                <div class="card order-card border-primary">
                    <div class="card-header bg-primary-transparent">
                        <h5 class="text-primary"><i class="bx bx-badge-check text-primary"></i> اعتماد السعر النهائي (ثمن)</h5>
                    </div>
                    <div class="card-body">
                        @if($order->thamn_price)
                            <div class="evaluation-result bg-primary-transparent border-primary">
                                <div class="h3 font-weight-bold text-primary mb-1">{{ number_format($order->thamn_price, 2) }} SAR</div>
                                <div class="small text-muted mb-2">السعر النهائي المعتمد للمستخدم</div>
                                @if($order->thamn_reasoning)
                                    <div class="small text-dark p-2 bg-white rounded border">{{ $order->thamn_reasoning }}</div>
                                @endif
                                <div class="mt-2 small text-muted">بواسطة: {{ $order->thamnUser->first_name ?? '-' }}</div>
                            </div>
                        @else
                            <form method="POST" action="{{ route('orders.thamn.evaluate', $order->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-muted">ملاحظات الاعتماد (اختياري)</label>
                                    <textarea name="thamn_reasoning" class="form-control" rows="2" placeholder="ملاحظة تظهر في التقرير النهائي..."></textarea>
                                </div>
                                <button class="btn btn-primary btn-block">
                                    ✔ اعتماد تقييم ثمن النهائي
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection