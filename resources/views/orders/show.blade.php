@extends('layouts.master')
@section('title', 'تفاصيل الطلب #' . $order->id)

@section('css')
<style>
    /* ===================================================
       CRITICAL: Prevent content from bleeding under sidebar
    =================================================== */
    /* Ensure all elements inside the grid respect column widths */
    .col-12 .expert-welcome-banner {
        max-width: 100%;
        box-sizing: border-box;
    }
    /* Fix flex overflow on Bootstrap columns in RTL */
    .row > [class*="col"] {
        min-width: 0;
    }
    /* Force right padding on desktop to avoid sidebar overlap if template fails */
    @media (min-width: 768px) {
        .expert-desktop-padding {
            padding-right: 250px !important;
        }
    }
    /* Force full-width tab panels */
    .expert-tab-panel,
    .expert-tabs-nav {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* ================================================
       BASE TYPOGRAPHY & DIRECTION
    ================================================ */
    body, h1, h2, h3, h4, h5, h6, .btn, .alert, input, textarea, label, span, div, p {
        font-family: 'Cairo', sans-serif !important;
    }

    /* ================================================
       ENTRANCE ANIMATIONS
    ================================================ */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
        50%       { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    }
    @keyframes shimmer {
        0%   { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    @keyframes floatIcon {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-8px); }
    }

    .anim-1 { animation: fadeInUp 0.6s ease both; animation-delay: 0.05s; }
    .anim-2 { animation: fadeInUp 0.6s ease both; animation-delay: 0.15s; }
    .anim-3 { animation: fadeInUp 0.6s ease both; animation-delay: 0.25s; }
    .anim-4 { animation: fadeInUp 0.6s ease both; animation-delay: 0.35s; }

    /* ================================================
       EXPERT WELCOME BANNER
    ================================================ */
    .expert-welcome-banner {
        background: linear-gradient(135deg, #1a3a6b 0%, #1565C0 50%, #0d47a1 100%);
        border-radius: 18px;
        padding: 30px 35px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(21, 101, 192, 0.3);
        direction: rtl;
        text-align: right;
    }
    .expert-welcome-banner::before {
        content: '⚖️';
        position: absolute;
        left: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 6rem;
        opacity: 0.08;
        animation: floatIcon 4s ease-in-out infinite;
    }
    .expert-welcome-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .expert-welcome-banner .welcome-title {
        color: #fff;
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .expert-welcome-banner .welcome-subtitle {
        color: rgba(255,255,255,0.80);
        font-size: 0.98rem;
        font-weight: 500;
        margin-bottom: 18px;
        line-height: 1.7;
    }
    .banner-order-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        border-radius: 50px;
        padding: 6px 18px;
        font-size: 0.9rem;
        font-weight: 700;
        backdrop-filter: blur(8px);
    }
    .banner-step {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 8px 14px;
        color: rgba(255,255,255,0.9);
        font-size: 0.85rem;
        margin-left: 8px;
        margin-top: 10px;
        border: 1px solid rgba(255,255,255,0.15);
    }

    /* ================================================
       ORDER CARD (BASE)
    ================================================ */
    .order-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 24px;
        background: #fff;
    }
    .order-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px 14px;
        border-radius: 16px 16px 0 0;
    }
    .order-card .card-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1a1a1a;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* ================================================
       SPEC CARDS (مواصفات المنتج)
    ================================================ */
    .spec-card {
        background: #fff;
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid #ebebeb;
        border-right: 4px solid #c1953e;
        transition: all 0.3s ease;
        height: 100%;
        direction: rtl;
        text-align: right;
    }
    .spec-card:hover {
        box-shadow: 0 6px 20px rgba(193,149,62,0.1);
        transform: translateY(-2px);
        border-right-color: #a67f31;
    }
    .spec-card .spec-label {
        color: #999;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .spec-card .spec-value {
        font-weight: 700;
        color: #1a1a1a;
        font-size: 1rem;
        line-height: 1.3;
    }

    /* ================================================
       PRODUCT IMAGES
    ================================================ */
    .product-img-container {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #eee;
        transition: all 0.3s ease;
        display: block;
    }
    .product-img-container:hover {
        transform: scale(1.04);
        border-color: #c1953e;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .product-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* ================================================
       STATUS BADGES
    ================================================ */
    .status-badge {
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-block;
    }

    /* ================================================
       AI GLASS CARD
    ================================================ */
    .ai-glass-card {
        background: linear-gradient(135deg, #eef6ff 0%, #f8fbff 100%);
        border-right: 5px solid #1565C0;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 8px 30px rgba(21, 101, 192, 0.1);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        direction: rtl;
        text-align: right;
    }
    .ai-glass-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #1565C0, #42A5F5, #1565C0);
        background-size: 200% auto;
        animation: shimmer 3s linear infinite;
        border-radius: 14px 14px 0 0;
    }
    .ai-glass-card .ai-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1565C0;
        margin-bottom: 4px;
    }
    .ai-glass-card .ai-subtitle {
        font-size: 0.82rem;
        color: #888;
        margin-bottom: 16px;
    }
    .ai-price-block {
        text-align: center;
        padding: 10px 5px;
    }
    .ai-price-block .price-label {
        font-size: 0.75rem;
        color: #888;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .ai-price-block .price-value {
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .ai-price-block .price-currency {
        font-size: 0.7rem;
        font-weight: 600;
        opacity: 0.7;
        display: block;
        margin-top: 2px;
    }

    /* ================================================
       EXPERT FORM CARD
    ================================================ */
    .expert-form-card .card-header {
        background: linear-gradient(135deg, #f0fcf4 0%, #ffffff 100%);
        border-bottom: 1px solid #e0f0e8;
    }
    .premium-input {
        border-radius: 12px;
        border: 2px solid #d8eee1;
        padding: 14px 20px;
        font-size: 1.1rem;
        transition: all 0.3s;
        text-align: center;
        font-weight: 700;
        direction: rtl;
    }
    .premium-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.12);
        outline: none;
    }
    .price-main-input {
        font-size: 2.2rem !important;
        font-weight: 800 !important;
        color: #28a745 !important;
        border-radius: 14px !important;
        border: 3px solid #d8eee1 !important;
        text-align: center !important;
        background: linear-gradient(180deg, #f6fef8, #ffffff) !important;
        max-width: 280px;
        margin: 0 auto;
        display: block;
    }
    .price-main-input:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 5px rgba(40, 167, 69, 0.15) !important;
    }
    .premium-btn {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
        border-radius: 14px;
        color: white;
        font-weight: 800;
        font-size: 1.15rem;
        padding: 16px;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        animation: pulseGlow 2.5s infinite;
    }
    .premium-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.4);
        color: white;
        animation: none;
    }
    .premium-btn:active {
        transform: translateY(-1px);
    }

    /* ================================================
       EVALUATED STATE (read-only)
    ================================================ */
    .evaluated-price-box {
        background: linear-gradient(135deg, #f0fcf4, #ffffff);
        border-radius: 14px;
        padding: 24px;
        text-align: center;
        border: 2px solid #d4edda;
    }
    .evaluated-price-box .big-price {
        font-size: 2.8rem;
        font-weight: 900;
        color: #1e7e34;
        line-height: 1.1;
    }

    /* ================================================
       MISC
    ================================================ */
    .reasoning-html-content {
        background: #fafafa;
        padding: 18px 22px;
        border-radius: 12px;
        border-right: 5px solid #c1953e;
        line-height: 1.85;
        font-size: 0.93rem;
        direction: rtl;
        text-align: right;
    }
    .reasoning-html-content ul { padding-right: 25px; }
    .reasoning-html-content li { margin-bottom: 6px; color: #444; }
    .reasoning-html-content p { margin-bottom: 10px; }
    .info-label { color: #888; font-size: 0.82rem; margin-bottom: 2px; }
    .info-value { font-weight: 600; color: #333; }
    .evaluation-result { background: #fcf9f2; border-radius: 12px; padding: 20px; border: 1px solid #e9dfc6; }
    .btn-gold { background-color: #c1953e; border-color: #c1953e; color: white; font-weight: 600; padding: 10px 25px; border-radius: 10px; }
    .btn-gold:hover { background-color: #a67f31; border-color: #a67f31; color: white; }
    .table-custom th { background-color: #f8f9fa; font-weight: 700; color: #555; }
    .table-custom td { vertical-align: middle; }
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
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
            style="border-radius: 10px; font-size: 1.1rem;">
            <i class="bx bx-check-circle fs-20 align-middle ml-2"></i>
            <strong class="ml-1">نجاح!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
            style="border-radius: 10px; font-size: 1.1rem;">
            <i class="bx bx-error-circle fs-20 align-middle ml-2"></i>
            <strong class="ml-1">خطأ!</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row expert-desktop-padding">
        @if(auth()->user()->hasRole('expert'))
            {{-- ==========================================
                 EXPERT WELCOME BANNER (Full Width)
            ============================================ --}}
            <div class="col-12 anim-1 mb-4">
                <div class="expert-welcome-banner">
                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                        <div style="flex: 1; min-width: 0;">
                            <div class="welcome-title">🌟 أهلاً خبيرنا المميز!</div>
                            <div class="welcome-subtitle">
                                وصلك طلب تثمين — راجع المواصفات والصور ثم اعتمد سعرك.
                                <br>تقييمك سيُرسل فوراً وسيكون الأساس في تقرير ثمن الرسمي.
                            </div>
                            <div class="mt-3">
                                <span class="banner-order-badge">
                                    <i class="bx bx-receipt"></i> طلب رقم: #{{ $order->id }}
                                </span>
                            </div>
                        </div>
                        <div class="d-none d-md-flex flex-column align-items-end ml-3" style="gap:8px; flex-shrink:0;">
                            <span class="banner-step"><i class="bx bx-search-alt"></i> ١ — المواصفات</span>
                            <span class="banner-step"><i class="bx bx-images"></i> ٢ — الصور</span>
                            <span class="banner-step"><i class="bx bx-badge-check"></i> ٣ — الاعتماد</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 EXPERT TABBED INTERFACE (Single Column)
            ============================================ --}}
            <div class="col-12 anim-2">
                <div class="card order-card" style="border-radius: 18px; overflow: hidden;">

                    {{-- Tab Navigation --}}
                    <div class="expert-tabs-nav" style="
                        display: flex;
                        background: #f8f9fa;
                        border-bottom: 2px solid #e9ecef;
                        direction: rtl;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    ">
                        <button class="expert-tab-btn active" onclick="switchTab('specs', this)" style="
                            flex: 1; min-width: 120px; padding: 14px 10px;
                            background: none; border: none; border-bottom: 3px solid transparent;
                            font-family: 'Cairo', sans-serif; font-weight: 700; font-size: 0.9rem;
                            color: #888; cursor: pointer; transition: all 0.3s; white-space: nowrap;
                        ">
                            <i class="bx bx-list-check" style="font-size: 1.1rem; vertical-align: middle;"></i>
                            المواصفات
                        </button>
                        <button class="expert-tab-btn" onclick="switchTab('photos', this)" style="
                            flex: 1; min-width: 120px; padding: 14px 10px;
                            background: none; border: none; border-bottom: 3px solid transparent;
                            font-family: 'Cairo', sans-serif; font-weight: 700; font-size: 0.9rem;
                            color: #888; cursor: pointer; transition: all 0.3s; white-space: nowrap;
                        ">
                            <i class="bx bx-camera" style="font-size: 1.1rem; vertical-align: middle;"></i>
                            الصور
                            @php $imgCount = $order->files->where('type','image')->count(); @endphp
                            @if($imgCount > 0)
                                <span style="background:#1565C0; color:#fff; border-radius:50px; padding: 1px 8px; font-size:0.75rem; margin-right:4px;">{{ $imgCount }}</span>
                            @endif
                        </button>
                        <button class="expert-tab-btn" onclick="switchTab('evaluate', this)" style="
                            flex: 1; min-width: 120px; padding: 14px 10px;
                            background: none; border: none; border-bottom: 3px solid transparent;
                            font-family: 'Cairo', sans-serif; font-weight: 700; font-size: 0.9rem;
                            color: #888; cursor: pointer; transition: all 0.3s; white-space: nowrap;
                        ">
                            <i class="bx bx-badge-check" style="font-size: 1.1rem; vertical-align: middle;"></i>
                            @if(in_array($order->status, ['estimated', 'evaluated', 'finished', 'completed']) && $order->status !== 'beingReEstimated')
                                التقييم المعتمد ✅
                            @else
                                اعتماد التقييم
                            @endif
                        </button>
                    </div>

                    {{-- TAB 1: Specifications --}}
                    <div id="tab-specs" class="expert-tab-panel" style="display: block; direction: rtl; padding: 20px;">
                        <div class="row">
                            {{-- Static specs --}}
                            <div class="col-6 col-md-4 mb-3">
                                <div class="spec-card">
                                    <div class="spec-label">رقم الطلب</div>
                                    <div class="spec-value text-primary">#{{ $order->id }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <div class="spec-card">
                                    <div class="spec-label">تاريخ الطلب</div>
                                    <div class="spec-value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <div class="spec-card">
                                    <div class="spec-label">إمكانية البيع</div>
                                    <div class="spec-value">
                                        @if($order->can_send_to_market)
                                            <span class="text-success"><i class="bx bx-check-circle"></i> نعم، تثمين وبيع</span>
                                        @else
                                            <span class="text-secondary"><i class="bx bx-info-circle"></i> تثمين فقط</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Dynamic specs --}}
                            @foreach($order->details as $detail)
                                <div class="col-6 col-md-4 mb-3">
                                    <div class="spec-card">
                                        <div class="spec-label">{{ $detail->question->question_ar ?? '-' }}</div>
                                        <div class="spec-value">{{ $detail->option->option_ar ?? $detail->value ?? '-' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Quick action button to go to evaluate --}}
                        <div class="text-center mt-2">
                            <button class="btn btn-outline-primary" onclick="switchTab('photos', document.querySelectorAll('.expert-tab-btn')[1])" style="border-radius:10px; font-weight:700; padding: 10px 30px;">
                                <i class="bx bx-images"></i> التالي: مراجعة الصور
                            </button>
                        </div>
                    </div>

                    {{-- TAB 2: Photos --}}
                    <div id="tab-photos" class="expert-tab-panel" style="display: none; direction: rtl; padding: 20px;">
                        <div class="row">
                            @forelse($order->files->where('type', 'image') as $image)
                                <div class="col-6 col-sm-4 col-md-3 mb-3">
                                    <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank" class="product-img-container">
                                        <img src="{{ asset('storage/' . $image->file_path) }}" alt="صورة المنتج">
                                    </a>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5" style="background:#f9f9f9; border-radius:12px; border: 2px dashed #ddd;">
                                    <i class="bx bx-image-alt text-muted" style="font-size: 3.5rem;"></i>
                                    <p class="text-muted mt-2 font-weight-bold mb-0">لم يرفق العميل صور للمنتج</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="text-center mt-2">
                            <button class="btn btn-success" onclick="switchTab('evaluate', document.querySelectorAll('.expert-tab-btn')[2])" style="border-radius:10px; font-weight:700; padding: 10px 30px;">
                                <i class="bx bx-badge-check"></i> التالي: اعتماد التقييم
                            </button>
                        </div>
                    </div>

                    {{-- TAB 3: Evaluation --}}
                    <div id="tab-evaluate" class="expert-tab-panel" style="display: none; direction: rtl; padding: 20px;">

                        @if(in_array($order->status, ['estimated', 'evaluated', 'finished', 'completed']) && $order->status !== 'beingReEstimated')
                            {{-- Evaluated: Read-only View --}}
                            <div class="text-center mb-4">
                                <i class="bx bxs-check-shield text-success" style="font-size: 3rem;"></i>
                                <h5 class="font-weight-bold text-success mt-2">تم اعتماد التقييم بنجاح</h5>
                            </div>
                            <div class="evaluated-price-box mb-4">
                                <div class="text-muted mb-1" style="font-size: 0.85rem; font-weight: 600;">السعر المعتمد</div>
                                <div class="big-price">{{ number_format($order->expert_price, 0) }}</div>
                                <div class="text-muted" style="font-size: 0.9rem; font-weight: 600;">ريال سعودي (SAR)</div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="spec-card" style="border-right: 4px solid #28a745;">
                                        <div class="spec-label">الحد الأدنى</div>
                                        <div class="spec-value text-success">{{ number_format($order->expert_min_price, 0) }} <small>SAR</small></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="spec-card" style="border-right: 4px solid #ffc107;">
                                        <div class="spec-label">الحد الأعلى</div>
                                        <div class="spec-value text-warning">{{ number_format($order->expert_max_price, 0) }} <small>SAR</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label font-weight-bold text-dark mb-2">تقرير التقييم:</label>
                                <div class="reasoning-html-content">{!! $order->expert_reasoning !!}</div>
                            </div>

                        @else
                            {{-- Form: Evaluate --}}

                            {{-- AI Glass Card (only if AI evaluation) --}}
                            @if($order->evaluation_type === 'ai' && $order->ai_price)
                                <div class="ai-glass-card">
                                    <div class="ai-title">
                                        <i class="bx bx-bot" style="font-size: 1.2rem; vertical-align: middle;"></i>
                                        تقرير الذكاء الاصطناعي
                                    </div>
                                    <div class="ai-subtitle">تمت التعبئة تلقائياً — يمكنك الاعتماد أو التعديل</div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="ai-price-block" style="border-left: 1px solid #d0e0f5;">
                                                <div class="price-label">المقترح</div>
                                                <div class="price-value text-primary">{{ number_format($order->ai_price, 0) }}</div>
                                                <span class="price-currency">SAR</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="ai-price-block">
                                                <div class="price-label">الأدنى</div>
                                                <div class="price-value text-success">{{ number_format($order->ai_min_price, 0) }}</div>
                                                <span class="price-currency">SAR</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="ai-price-block" style="border-right: 1px solid #d0e0f5;">
                                                <div class="price-label">الأعلى</div>
                                                <div class="price-value text-warning">{{ number_format($order->ai_max_price, 0) }}</div>
                                                <span class="price-currency">SAR</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Expert Evaluation Form --}}
                            <form method="POST" action="{{ route('orders.expert.evaluate', $order->id) }}">
                                @csrf

                                @if($order->evaluation_type !== 'ai')
                                    {{-- Main Price --}}
                                    <div class="mb-4 text-center">
                                        <label class="form-label font-weight-bold text-dark d-block mb-2">السعر الموصى به <span class="text-danger">*</span></label>
                                        @php
                                            $formPrice = old('expert_price', $order->expert_price ?: ($order->ai_price ?: $order->total_price));
                                        @endphp
                                        <input type="number" name="expert_price"
                                            class="form-control price-main-input"
                                            step="0.01" min="0"
                                            value="{{ $formPrice }}" required>
                                        <small class="text-muted d-block mt-1">ريال سعودي (SAR)</small>
                                    </div>

                                    {{-- Min / Max --}}
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <label class="form-label small text-muted font-weight-bold">الحد الأدنى</label>
                                            @php $formMin = old('expert_min_price', $order->expert_min_price ?: ($order->ai_min_price ?: '')); @endphp
                                            <input type="number" name="expert_min_price" class="form-control premium-input" step="0.01" min="0" value="{{ $formMin }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small text-muted font-weight-bold">الحد الأعلى</label>
                                            @php $formMax = old('expert_max_price', $order->expert_max_price ?: ($order->ai_max_price ?: '')); @endphp
                                            <input type="number" name="expert_max_price" class="form-control premium-input" step="0.01" min="0" value="{{ $formMax }}">
                                        </div>
                                    </div>
                                @else
                                    {{-- AI Valuation Ratings --}}
                                    <div class="mb-4 text-center">
                                        <label class="form-label font-weight-bold text-dark d-block mb-3">تقييم التثمين الذكي <span class="text-danger">*</span></label>
                                        <div class="d-flex justify-content-center flex-wrap gap-2" style="gap: 15px;">
                                            @foreach($aiRatings as $rating)
                                                <label class="rating-radio-label" style="cursor: pointer; text-align: center; margin: 0;">
                                                    <input type="radio" name="ai_valuation_rating_id" value="{{ $rating->id }}" class="d-none" required>
                                                    <div class="rating-card" style="border: 2px solid #ddd; border-radius: 12px; padding: 15px 25px; transition: all 0.2s;">
                                                        @if($rating->icon)
                                                            <img src="{{ asset('storage/' . $rating->icon) }}" style="width:40px; height:40px; object-fit:contain; margin-bottom: 8px;">
                                                        @endif
                                                        <div style="font-weight: 700; color: {{ $rating->color ?? '#333' }};">
                                                            {{ $rating->name_ar }}
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <style>
                                            .rating-radio-label input:checked + .rating-card {
                                                border-color: #1565C0 !important;
                                                background-color: #f0f7ff;
                                                box-shadow: 0 4px 12px rgba(21, 101, 192, 0.15);
                                                transform: translateY(-2px);
                                            }
                                            .rating-card:hover {
                                                background-color: #f8f9fa;
                                            }
                                        </style>
                                    </div>
                                @endif

                                {{-- Reasoning --}}
                                <div class="mb-4">
                                    <label class="form-label font-weight-bold text-dark">تقرير التقييم والملاحظات <span class="text-danger">*</span></label>
                                    @php $existingReasoning = old('expert_reasoning', $order->expert_reasoning ?? ''); @endphp
                                    <textarea name="expert_reasoning" id="expert_reasoning_editor" class="form-control" rows="5"
                                        placeholder="اكتب الأسباب التي بنيت عليها تقييمك..." required>{!! $existingReasoning !!}</textarea>
                                </div>

                                {{-- Submit --}}
                                <button type="submit" class="btn premium-btn btn-block" style="font-size: 1.15rem; padding: 16px;">
                                    <i class="bx bx-badge-check" style="font-size: 1.3rem; vertical-align: middle;"></i>
                                    اعتماد التقييم وإرساله للعميل
                                </button>
                            </form>
                        @endif

                    </div>{{-- end tab-evaluate --}}

                </div>{{-- end card --}}
            </div>{{-- end col-12 --}}
        @else
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
                                    @if(!in_array($order->status, ['pending', 'failed', 'waitingPayment', 'notPaid']))
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
                            <div class="col-md-4">
                                <div class="info-label">نوع التثمين</div>
                                <div class="info-value">
                                    @if(($order->pricing_mode ?? 'valuation_only') === 'valuation_and_sale')
                                        <span class="status-badge"
                                            style="background: rgba(248,180,0,0.15); color: #92620a; border: 1px solid rgba(248,180,0,0.4);">
                                            <i class="bx bx-store-alt"></i> تثمين والبيع
                                        </span>
                                    @else
                                        <span class="status-badge"
                                            style="background: rgba(59,130,246,0.12); color: #1d4ed8; border: 1px solid rgba(59,130,246,0.3);">
                                            <i class="bx bx-calculator"></i> تثمين فقط
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">الموافقة على شروط البيع</div>
                                <div class="info-value">
                                    @if($order->sale_terms_accepted)
                                        <span class="status-badge bg-success-transparent text-success">
                                            <i class="bx bx-check-circle"></i> وافق على الشروط
                                        </span>
                                    @else
                                        <span class="status-badge bg-light text-muted">
                                            <i class="bx bx-minus-circle"></i> لم يوافق
                                        </span>
                                    @endif
                                </div>
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
                                    <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank"
                                        class="product-img-container d-block">
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
                                            <td class="text-right font-weight-bold">
                                                {{ $detail->option->option_ar ?? $detail->value ?? '-' }}
                                            </td>
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
                                    <p class="mb-2 small font-weight-bold">نعتذر منك، لم يتم قبول طلبك خلال 24 ساعة. يمكنك طلب استرداد
                                        المبلغ الآن.</p>
                                    <a href="{{ route('refunds.create', $order->id) }}"
                                        class="btn btn-warning btn-sm btn-block fw-bold">
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
                        {{-- تقييم AI (ظاهر للجميع ومن ضمنهم الخبير للاعتماد) --}}
                        <div class="mb-4">
                            <h6 class="font-weight-bold d-flex align-items-center gap-2 mb-3">
                                <span class="avatar avatar-sm br-7 bg-primary-transparent text-primary"><i
                                        class="fas fa-brain"></i> AI</span>
                                تقييم الذكاء الاصطناعي
                            </h6>
                            @if($order->ai_price)
                                <div class="evaluation-result">
                                    <div class="h4 font-weight-bold text-primary mb-1">{{ number_format($order->ai_price, 2) }} SAR
                                    </div>
                                    <div class="small text-muted">نطاق السعر: {{ number_format($order->ai_min_price, 2) }} -
                                        {{ number_format($order->ai_max_price, 2) }}
                                    </div>
                                    @php
                                        $confPct = round(($order->ai_confidence ?? 0) * 100);
                                        $confColor = $confPct >= 70 ? 'success' : ($confPct >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="badge bg-{{ $confColor }}-transparent text-{{ $confColor }} mt-2">ثقة:
                                        {{ $confPct }}%
                                    </div>
                                    <hr class="my-2 border-top-0 border-light">
                                    <div class="reasoning-html-content text-dark">
                                        @if(!preg_match('/<[a-z][\s\S]*>/i', $order->ai_reasoning))
                                            {!! \Illuminate\Support\Str::markdown((string) $order->ai_reasoning) !!}
                                        @else
                                            {!! $order->ai_reasoning !!}
                                        @endif
                                    </div>

                                    @if(is_array($order->ai_features) && count($order->ai_features) > 0)
                                        <hr class="my-2 border-top-0 border-light">
                                        <h6 class="font-weight-bold text-primary mb-3">
                                            <i class="bx bx-grid-alt"></i> التحليل التفصيلي للسلعة:
                                        </h6>
                                        @php
                                            $isCards = isset($order->ai_features[0]['icon']);
                                        @endphp
                                        @if($isCards)
                                            <div class="row g-2">
                                                @foreach($order->ai_features as $card)
                                                    <div class="col-6">
                                                        <div
                                                            style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:12px;padding:12px 10px;text-align:center;height:100%;">
                                                            <div style="font-size:1.8rem;margin-bottom:6px;">{{ $card['icon'] ?? '📌' }}</div>
                                                            <div style="font-size:0.72rem;color:#888;font-weight:600;margin-bottom:4px;">
                                                                {{ $card['title'] ?? '' }}
                                                            </div>
                                                            <div style="font-size:0.95rem;font-weight:800;color:#1a1a1a;margin-bottom:4px;">
                                                                {{ $card['value'] ?? '-' }}
                                                            </div>
                                                            @if(!empty($card['description']))
                                                                <div style="font-size:0.7rem;color:#aaa;line-height:1.3;">{{ $card['description'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            {{-- عرض قديم كـ tags للطلبات القديمة --}}
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($order->ai_features as $feature)
                                                    <span class="badge bg-light text-dark border p-2 mb-1 mr-1" style="font-size: 0.9rem;">
                                                        <i class="bx bx-check-circle text-success align-middle"></i> {{ $feature }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-3 bg-light rounded">
                                    <p class="text-muted mb-0 small italic">لم يتم إجراء تقييم AI بعد</p>
                                    @hasanyrole('admin|superadmin')
                                    <form method="POST" action="{{ route('orders.ai.evaluate', $order->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-play"></i> تشغيل تقييم AI الآن
                                        </button>
                                    </form>
                                    @endhasanyrole
                                </div>
                            @endif

                            {{-- زرار إعادة تقييم AI دايماً ظاهر للأدمن --}}
                            @hasanyrole('admin|superadmin')
                            @if($order->ai_price)
                                <div class="mt-3 text-center">
                                    <form method="POST" action="{{ route('orders.ai.evaluate', $order->id) }}"
                                        onsubmit="return confirm('سيتم إعادة تقييم الطلب بالذكاء الاصطناعي وستُحدَّث النتيجة. هل أنت متأكد؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-sync-alt"></i>
                                            إعادة تقييم AI
                                            @if($order->re_evaluation_count > 0)
                                                <span class="badge bg-warning text-dark ms-1">{{ $order->re_evaluation_count }}x</span>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            @endif
                            @endhasanyrole
                        </div>

                        {{-- تقييم الخبير (عرض) --}}
                        <div>
                            <h6 class="font-weight-bold d-flex align-items-center gap-2 mb-3">
                                <span class="avatar avatar-sm br-7 bg-warning-transparent text-warning"><i
                                        class="bx bx-user"></i></span>
                                تقييم الخبير الحالي
                            </h6>
                            @if($order->expert_price)
                                <div class="evaluation-result border-warning-transparent bg-warning-transparent">
                                    <div class="h4 font-weight-bold text-warning mb-1">{{ number_format($order->expert_price, 2) }}
                                        SAR</div>
                                    <div class="small text-muted">نطاق السعر: {{ number_format($order->expert_min_price, 2) }} -
                                        {{ number_format($order->expert_max_price, 2) }}
                                    </div>
                                    <hr class="my-2 border-top-0 border-light">
                                    <div class="reasoning-html-content text-dark">
                                        @if(!preg_match('/<[a-z][\s\S]*>/i', $order->expert_reasoning))
                                            {!! \Illuminate\Support\Str::markdown((string) $order->expert_reasoning) !!}
                                        @else
                                            {!! $order->expert_reasoning !!}
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-3 bg-light rounded">
                                    <p class="text-muted mb-0 small italic">بانتظار تقييم الخبير</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>



                {{-- اعتماد تقييم ثمن (للأدمن فقط) --}}
                @if(auth()->user()->hasAnyRole(['superadmin', 'admin']))
                    <div class="card order-card border-primary">
                        <div class="card-header bg-primary-transparent">
                            <h5 class="text-primary"><i class="bx bx-badge-check text-primary"></i> اعتماد السعر النهائي (ثمن)</h5>
                        </div>
                        <div class="card-body">
                            @if($order->thamn_price)
                                <div class="evaluation-result bg-primary-transparent border-primary">
                                    <div class="h3 font-weight-bold text-primary mb-1">{{ number_format($order->thamn_price, 2) }} SAR
                                    </div>
                                    <div class="small text-muted mb-2">السعر النهائي المعتمد للمستخدم</div>
                                    @if($order->thamn_reasoning)
                                        <div class="reasoning-html-content text-dark">
                                            @if(!preg_match('/<[a-z][\s\S]*>/i', $order->thamn_reasoning))
                                                {!! \Illuminate\Support\Str::markdown((string) $order->thamn_reasoning) !!}
                                            @else
                                                {!! $order->thamn_reasoning !!}
                                            @endif
                                        </div>
                                    @endif
                                    <div class="mt-2 small text-muted">بواسطة: {{ $order->thamnUser->first_name ?? '-' }}</div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('orders.thamn.evaluate', $order->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">ملاحظات الاعتماد (اختياري)</label>
                                        <textarea name="thamn_reasoning" class="form-control" rows="2"
                                            placeholder="ملاحظة تظهر في التقرير النهائي..."></textarea>
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
        @endif
    </div>

@section('js')
    <!-- CKEditor Initialization -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        $(document).ready(function () {
            var editorEl = document.getElementById('expert_reasoning_editor');
            if (!editorEl) return;
            
            var existingContent = editorEl.value.trim();

            @if(auth()->user()->hasRole('expert') && empty($order->expert_reasoning) && !empty($order->ai_reasoning))
                try {
                    var aiRaw = {!! json_encode((string) $order->ai_reasoning, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) !!};
                    var tmp = document.createElement('div');
                    tmp.innerHTML = aiRaw;
                    tmp.querySelectorAll('table').forEach(function(t) { t.remove(); });
                    var cleaned = tmp.innerHTML.trim();
                    if (cleaned && cleaned.length > 30) {
                        existingContent = cleaned;
                        editorEl.value = cleaned;
                    }
                } catch(e) { console.warn('AI reasoning parse error:', e); }
            @endif

            CKEDITOR.replace('expert_reasoning_editor', {
                language: 'ar',
                height: 350,
                versionCheck: false,
                removePlugins: 'elementspath',
                resize_enabled: false
            });
        });

        /* Expert Tab Switching */
        function switchTab(tabName, clickedBtn) {
            // Hide all panels
            document.querySelectorAll('.expert-tab-panel').forEach(function(panel) {
                panel.style.display = 'none';
            });
            // Deactivate all buttons
            document.querySelectorAll('.expert-tab-btn').forEach(function(btn) {
                btn.style.borderBottomColor = 'transparent';
                btn.style.color = '#888';
                btn.style.background = 'none';
            });
            // Show selected panel with animation
            var panel = document.getElementById('tab-' + tabName);
            if (panel) {
                panel.style.display = 'block';
                panel.style.animation = 'fadeInUp 0.4s ease both';
            }
            // Activate clicked button
            if (clickedBtn) {
                clickedBtn.style.borderBottomColor = '#1565C0';
                clickedBtn.style.color = '#1565C0';
                clickedBtn.style.background = '#f0f5ff';
            }
            // If switching to evaluate tab, reinit CKEditor if needed
            if (tabName === 'evaluate' && typeof CKEDITOR !== 'undefined') {
                setTimeout(function() {
                    var inst = CKEDITOR.instances['expert_reasoning_editor'];
                    if (inst) inst.resize('100%', 350);
                }, 100);
            }
        }

        // Activate the first tab button on page load
        $(document).ready(function() {
            var firstBtn = document.querySelector('.expert-tab-btn');
            if (firstBtn) {
                firstBtn.style.borderBottomColor = '#1565C0';
                firstBtn.style.color = '#1565C0';
                firstBtn.style.background = '#f0f5ff';
            }
        });
    </script>
@endsection