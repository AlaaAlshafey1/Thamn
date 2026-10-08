@extends(auth()->check() && auth()->user()->hasRole('expert') ? 'layouts.expert' : 'layouts.master')
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

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
            style="border-radius: 10px; font-size: 1.1rem;">
            <i class="bx bx-check-circle fs-20 align-middle ml-2"></i>
            <strong class="ml-1">نجاح!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
            style="border-radius: 10px; font-size: 1.1rem;">
            <i class="bx bx-error-circle fs-20 align-middle ml-2"></i>
            <strong class="ml-1">خطأ!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row expert-desktop-padding">
        @if(auth()->user()->hasRole('expert'))
            {{-- ==========================================
                 EXPERT NEW LAYOUT (Match Image)
            ============================================ --}}
            <style>
                .ex-stepper { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 25px 50px; border-radius: 12px; position: relative; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
                .ex-stepper::before { content: ''; position: absolute; top: 35px; left: 80px; right: 80px; height: 4px; background: #f39c12; z-index: 1; }
                .step-item { position: relative; z-index: 2; text-align: center; background: #fff; padding: 0 10px; cursor: pointer; }
                .step-circle { width: 24px; height: 24px; border-radius: 50%; background: #f39c12; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; margin: 0 auto 10px; box-shadow: 0 0 0 4px #fff; }
                .step-label { font-weight: 700; color: #333; font-size: 0.9rem; }
                
                .ex-main-card { background: #fff; border-radius: 16px; padding: 40px 30px; text-align: center; box-shadow: 0 2px 15px rgba(0,0,0,0.03); }
                .success-badge { width: 70px; height: 70px; margin: 0 auto 20px; background: url('{{ asset("assets/img/gold-badge.png") }}') center/contain no-repeat; display: flex; align-items: center; justify-content: center; font-size: 30px; color: #333; }
                .success-title { font-size: 1.6rem; font-weight: 800; color: #333; margin-bottom: 10px; }
                .success-subtitle { font-size: 0.95rem; color: #666; font-weight: 600; margin-bottom: 40px; }
                
                /* Podium Pricing */
                .podium-container { display: flex; align-items: flex-end; justify-content: center; gap: 0; margin-bottom: 30px; margin-top: 20px; border-radius: 16px; overflow: hidden; max-width: 600px; margin-left: auto; margin-right: auto; }
                .podium-block { flex: 1; padding: 20px 10px; color: #fff; position: relative; }
                .podium-block .p-icon { width: 20px; height: 20px; background: rgba(255,255,255,0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 10px; }
                .podium-block .p-val { font-size: 1.4rem; font-weight: 800; line-height: 1.2; direction: ltr; }
                .podium-block .p-lbl { font-size: 0.8rem; font-weight: 600; margin-top: 5px; }
                
                .podium-min { background: #f39c12; height: 120px; border-radius: 0 16px 16px 0; }
                .podium-main { background: #009688; height: 150px; z-index: 2; box-shadow: 0 -5px 15px rgba(0,0,0,0.1); border-radius: 12px 12px 0 0; }
                .podium-main .p-val { font-size: 1.8rem; margin-top: 10px; }
                .podium-max { background: #f44336; height: 120px; border-radius: 16px 0 0 16px; }

                /* Report Box */
                .ex-report-box { background: linear-gradient(180deg, #fff9f2, #ffffff); border: 1px solid #f9ebd8; border-radius: 16px; padding: 25px; text-align: right; margin-top: 40px; }
                .ex-report-title { color: #f39c12; font-weight: 800; font-size: 1.1rem; margin-bottom: 15px; }
                .ex-report-content { color: #555; font-size: 0.95rem; line-height: 1.8; font-weight: 600; }
                
                /* Sidebar Card */
                .ex-side-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 20px; }
                .ex-side-header { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; font-weight: 800; }
                .ex-side-body { padding: 0; }
                .ex-side-row { padding: 15px 20px; border-bottom: 1px solid #f8f8f8; display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; }
                .ex-side-row:last-child { border-bottom: none; }
                .ex-side-lbl { color: #888; font-weight: 600; }
                .ex-side-val { color: #333; font-weight: 700; }
                
                /* User profile in side */
                .ex-side-user { display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 5px 15px 5px 5px; border-radius: 50px; }
                .ex-side-user img { width: 30px; height: 30px; border-radius: 50%; }
                
                /* Map */
                .ex-map { width: 100%; height: 200px; background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Saudi_Arabia_location_map.svg/1200px-Saudi_Arabia_location_map.svg.png') center/cover; position: relative; }
                .ex-map-pin { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #009688; font-size: 24px; }

                /* App-like Mobile View */
                @media (max-width: 768px) {
                    .expert-desktop-padding { padding: 10px 5px !important; margin: 0 !important; }
                    .ex-stepper { flex-direction: column; padding: 20px 10px; border-radius: 20px; margin-bottom: 16px; gap: 10px; align-items: stretch; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
                    .ex-stepper::before { display: none; }
                    .step-item { display: flex; align-items: center; justify-content: flex-start; gap: 15px; background: #fdfdfd; border-radius: 14px; padding: 14px 16px; border: 1px solid #f0f0f0; }
                    .step-circle { margin: 0; box-shadow: none; width: 40px; height: 40px; font-size: 1.2rem; flex-shrink: 0; }
                    .step-label { font-size: 1.05rem; }
                    .ex-main-card { padding: 24px 16px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
                    
                    /* Vertical Podium for Mobile */
                    .podium-container { flex-direction: column; align-items: stretch; border-radius: 20px; overflow: hidden; margin-bottom: 24px; }
                    .podium-block { height: auto !important; border-radius: 0 !important; padding: 20px 15px; display: flex; justify-content: space-between; align-items: center; text-align: right; }
                    .podium-block .p-icon { margin: 0; margin-left: 15px; flex-shrink: 0; width: 46px; height: 46px; font-size: 22px; }
                    .podium-block .p-val { margin: 0 !important; font-size: 1.6rem !important; }
                    .podium-block > div { display: flex; flex-direction: column; align-items: flex-start; }
                    .podium-block .p-lbl { font-size: 0.95rem; }
                    .podium-main { order: 1; } /* Highest price first maybe? Or keep HTML order */
                    
                    /* Sidebar on mobile */
                    .col-lg-3 { padding: 0 10px; }
                    .ex-side-card { border-radius: 20px; margin-top: 16px; border: 1px solid #eee; }
                    .ex-side-header { padding: 18px 20px; font-size: 1.1rem; }
                    .ex-side-row { padding: 16px 20px; }
                    
                    /* Forms & Buttons */
                    .form-control, .form-select { border-radius: 16px !important; padding: 16px 20px !important; font-size: 1.05rem !important; }
                    .btn-success { width: 100% !important; max-width: none !important; border-radius: 18px !important; padding: 18px !important; font-size: 1.2rem !important; margin-top: 15px; }
                    
                    /* Responsive Specs Table inside Tab */
                    #tab-specs table, #tab-specs thead, #tab-specs tbody, #tab-specs th, #tab-specs td, #tab-specs tr { display: block; width: 100%; text-align: right; }
                    #tab-specs thead { display: none; }
                    #tab-specs tr { border: 1px solid #eee; border-radius: 16px; margin-bottom: 12px; padding: 16px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
                    #tab-specs td:nth-child(1) { display: none; } /* hide ID */
                    #tab-specs td:nth-child(2) { font-size: 1.05rem !important; padding: 0 0 10px 0 !important; border-bottom: 1px dashed #eee; color: #111 !important; }
                    #tab-specs td:nth-child(3) { padding: 10px 0 0 0 !important; font-size: 1rem !important; color: #009688 !important; }
                    #tab-specs td:nth-child(3) span { padding: 6px 14px !important; font-size: 0.9rem !important; }
                }
            </style>

            <div class="row">
                <!-- Center/Main Content -->
                <div class="col-lg-9 order-lg-1">
                    
                    <!-- Breadcrumb is now inside the content area for expert -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div style="font-weight: 800; font-size: 1.8rem; color: #111;">التقييم</div>
                        <div style="color: #f39c12; font-weight: 600; font-size: 0.9rem;">
                            الطلبات <i class="bx bx-chevron-left" style="vertical-align: middle;"></i> التقييم
                        </div>
                    </div>

                    <!-- Stepper -->
                    <div class="ex-stepper">
                        <div class="step-item" onclick="switchTab('specs', this)" id="step-specs">
                            <div class="step-circle"><i class="bx bx-list-check"></i></div>
                            <div class="step-label">المواصفات</div>
                        </div>
                        <div class="step-item" onclick="switchTab('photos', this)" id="step-photos">
                            <div class="step-circle"><i class="bx bx-images"></i></div>
                            <div class="step-label">الصور</div>
                        </div>
                        <div class="step-item" onclick="switchTab('evaluate', this)" id="step-evaluate">
                            <div class="step-circle"><i class="bx bx-check"></i></div>
                            <div class="step-label">التقييم المعتمد</div>
                        </div>
                    </div>

                    <!-- TAB: Specifications -->
                    <div class="ex-main-card expert-tab-panel" id="tab-specs" style="display:none; text-align:right;">
                        <div style="font-weight:900;font-size:1.1rem;color:#111;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                            <i class="bx bx-list-check" style="color:#f39c12;"></i> مواصفات الطلب
                        </div>
                        <div class="table-responsive">
                            <table style="width:100%;border-collapse:collapse;">
                                <thead>
                                    <tr style="background:#f9f9f9;border-bottom:2px solid #eee;">
                                        <th style="padding:12px 16px;color:#888;font-size:0.8rem;font-weight:800;text-align:right;">#</th>
                                        <th style="padding:12px 16px;color:#888;font-size:0.8rem;font-weight:800;text-align:right;">السؤال</th>
                                        <th style="padding:12px 16px;color:#888;font-size:0.8rem;font-weight:800;text-align:right;">الإجابة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->details as $index => $detail)
                                    <tr style="border-bottom:1px solid #f5f5f5;">
                                        <td style="padding:12px 16px;color:#aaa;font-size:0.85rem;font-weight:700;">{{ $index + 1 }}</td>
                                        <td style="padding:12px 16px;font-weight:700;color:#333;font-size:0.9rem;">{{ $detail->question->question_ar ?? '-' }}</td>
                                        <td style="padding:12px 16px;font-weight:600;color:#555;font-size:0.9rem;">
                                            @if($detail->option)
                                                <span style="background:rgba(243,156,18,0.1);color:#e67e22;padding:4px 10px;border-radius:50px;font-size:0.8rem;font-weight:800;">{{ $detail->option->option_ar ?? $detail->value }}</span>
                                            @elseif($detail->value)
                                                {{ $detail->value }}
                                            @else
                                                <span style="color:#ccc;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" style="text-align:center;padding:40px;color:#aaa;"><i class="bx bx-info-circle" style="font-size:2rem;display:block;margin-bottom:8px;"></i> لا توجد مواصفات</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB: Photos -->
                    <div class="ex-main-card expert-tab-panel" id="tab-photos" style="display:none; text-align:right;">
                        <div style="font-weight:900;font-size:1.1rem;color:#111;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                            <i class="bx bx-images" style="color:#f39c12;"></i> صور المنتج
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;">
                            @forelse($order->files->where('type', 'image') as $image)
                                <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank"
                                   style="display:block;border-radius:12px;overflow:hidden;border:2px solid #eee;aspect-ratio:1;">
                                    <img src="{{ asset('storage/' . $image->file_path) }}"
                                         style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;"
                                         onmouseover="this.style.transform='scale(1.05)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>
                            @empty
                                <div style="grid-column:1/-1;text-align:center;padding:50px;color:#ccc;">
                                    <i class="bx bx-image-alt" style="font-size:3rem;display:block;margin-bottom:10px;"></i>
                                    لا توجد صور مرفقة لهذا الطلب
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB: Evaluate -->
                    <div class="ex-main-card expert-tab-panel" id="tab-evaluate">

                        @if(in_array($order->status, ['estimated', 'evaluated', 'finished', 'completed']) && $order->status !== 'beingReEstimated')
                            <!-- Evaluated State -->
                            <div class="success-badge">
                                <i class='bx bxs-badge-check' style="font-size: 70px; color: #f39c12; text-shadow: 0 4px 10px rgba(0,0,0,0.1);"></i>
                            </div>
                            <div class="success-title">تم اعتماد التقييم بنجاح</div>
                            <div class="success-subtitle">تم اعتماد تثمين المنتج ، وسيتم اشعار العميل بحالة التثمين</div>

                            <!-- Podium Pricing -->
                            <div class="podium-container">
                                <div class="podium-block podium-min">
                                    <div class="p-icon"><i class="bx bx-minus"></i></div>
                                    <div class="p-val">{{ number_format($order->expert_min_price, 0) }} <sub>ر.س</sub></div>
                                    <div class="p-lbl">الحد الأدنى</div>
                                </div>
                                <div class="podium-block podium-main">
                                    <div class="p-icon" style="background:rgba(255,255,255,0.4);"><i class="bx bx-check"></i></div>
                                    <div class="p-val">{{ number_format($order->expert_price, 0) }} <sub>ر.س</sub></div>
                                    <div class="p-lbl">السعر المعتمد</div>
                                </div>
                                <div class="podium-block podium-max">
                                    <div class="p-icon"><i class="bx bx-plus"></i></div>
                                    <div class="p-val">{{ number_format($order->expert_max_price, 0) }} <sub>ر.س</sub></div>
                                    <div class="p-lbl">الحد الأعلى</div>
                                </div>
                            </div>

                            @if($order->evaluation_type === 'ai' && $order->ai_valuation_rating_id)
                                <!-- AI Rating Result Cards -->
                                <div class="expert-opinions-container mt-4 mb-4" style="background:#f9fbfd; border-radius:16px; padding:20px; direction:rtl; border: 1px solid #f1f5f9;">
                                    <div class="text-center mb-3" style="font-weight:900; color:#475569; font-size:1.1rem;">
                                        رأي الخبير المعتمد : ( {{ $aiRatings->where('id', $order->ai_valuation_rating_id)->first()->name_ar ?? '' }} )
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-center" style="gap:12px;">
                                        @foreach($aiRatings as $rating)
                                            @php 
                                                $isSelected = $order->ai_valuation_rating_id == $rating->id; 
                                            @endphp
                                            <div class="rating-display-card {{ $isSelected ? 'selected' : '' }}" 
                                                 style="flex:1; min-width:110px; max-width:140px; background:{{ $rating->color ?? '#fff' }}; 
                                                        border-radius:16px; padding:15px 10px; text-align:center; position:relative;
                                                        border: {{ $isSelected ? '3px solid #009688' : '3px solid transparent' }};
                                                        opacity: {{ $isSelected ? '1' : '0.5' }};
                                                        transform: {{ $isSelected ? 'scale(1.05)' : 'scale(1)' }};
                                                        transition: all 0.3s ease;
                                                        box-shadow: {{ $isSelected ? '0 8px 20px rgba(0,150,136,0.15)' : 'none' }};
                                                        ">
                                                @if($isSelected)
                                                    <div style="position:absolute; top:-10px; right:-10px; background:#009688; color:#fff; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
                                                        <i class="bx bx-check"></i>
                                                    </div>
                                                @endif
                                                <div style="margin-bottom:10px; height:35px; display:flex; align-items:center; justify-content:center;">
                                                    @if($rating->icon)
                                                        <img src="{{ asset('storage/' . $rating->icon) }}" style="width:30px; height:30px; object-fit:contain; filter: {{ $isSelected ? 'none' : 'grayscale(100%)' }};">
                                                    @else
                                                        <i class="bx bx-bar-chart-alt-2" style="font-size:24px; color:{{ $isSelected ? '#009688' : '#777' }};"></i>
                                                    @endif
                                                </div>
                                                <div style="font-weight:900; font-size:0.85rem; color:{{ $isSelected ? '#0f172a' : '#64748b' }}; line-height:1.2;">
                                                    {{ $rating->name_ar }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Report -->
                            <div class="ex-report-box">
                                <div class="ex-report-title">تقرير التقييم:</div>
                                <div class="ex-report-content">
                                    @if(!preg_match('/<[a-z][\s\S]*>/i', $order->expert_reasoning))
                                        {!! \Illuminate\Support\Str::markdown((string) $order->expert_reasoning) !!}
                                    @else
                                        {!! $order->expert_reasoning !!}
                                    @endif
                                </div>
                            </div>

                        @else
                            <!-- Not Evaluated State (Form) -->
                            <div class="success-title mb-4 text-primary" style="font-size:1.3rem;">اعتماد تقييم الطلب</div>
                            <form method="POST" action="{{ route('orders.expert.evaluate', $order->id) }}" style="text-align:right;">
                                @csrf
                                @if($order->evaluation_type === 'ai')
                                    <!-- Display AI Prices for Expert Review -->
                                    <div class="row text-right mb-4" style="direction:rtl;">
                                        <div class="col-md-12 mb-3">
                                            <label class="font-weight-bold text-primary" style="font-size:0.95rem;">
                                                <i class="bx bx-check-shield"></i> الأسعار المقترحة من التقييم الذكي:
                                            </label>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div style="background:#f8f9fa; border:1px solid #eee; border-radius:12px; padding:15px; text-align:center;">
                                                <div style="color:#555; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">الحد الأدنى</div>
                                                <div style="font-size:1.2rem; font-weight:900; color:#333;">{{ number_format($order->ai_min_price ?? 0, 0) }} <small style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div style="background:rgba(0, 150, 136, 0.05); border:2px solid #009688; border-radius:12px; padding:15px; text-align:center;">
                                                <div style="color:#009688; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">السعر المعتمد</div>
                                                <div style="font-size:1.3rem; font-weight:900; color:#009688;">{{ number_format($order->ai_price ?? 0, 0) }} <small style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div style="background:#f8f9fa; border:1px solid #eee; border-radius:12px; padding:15px; text-align:center;">
                                                <div style="color:#555; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">الحد الأعلى</div>
                                                <div style="font-size:1.2rem; font-weight:900; color:#333;">{{ number_format($order->ai_max_price ?? 0, 0) }} <small style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row text-right mb-4" style="direction:rtl;">
                                        <div class="col-md-12">
                                            <label class="font-weight-bold" style="color:#555;font-size:0.95rem;">رأي الخبير المعتمد حول التقييم الذكي <span class="text-danger">*</span></label>
                                            <select name="ai_valuation_rating_id" class="form-select" style="border-radius:12px; padding:12px; border: 1px solid #ccc; font-size:1rem;" required>
                                                <option value="" disabled selected>اختر رأيك كخبير معتمد</option>
                                                @foreach($aiRatings as $rating)
                                                    <option value="{{ $rating->id }}">{{ $rating->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Hide prices since we use AI prices -->
                                    <input type="hidden" name="expert_price" value="{{ $order->ai_price ?? 0 }}">
                                    <input type="hidden" name="expert_min_price" value="{{ $order->ai_min_price ?? 0 }}">
                                    <input type="hidden" name="expert_max_price" value="{{ $order->ai_max_price ?? 0 }}">
                                @else
                                    <div class="row text-right mb-4" style="direction:rtl;">
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold" style="color:#555;">الحد الأدنى <span class="text-danger">*</span></label>
                                            <input type="number" name="expert_min_price" class="form-control" style="border-radius:12px; padding:12px; border: 1px solid #ccc;" required value="{{ old('expert_min_price', $order->ai_min_price) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold text-success" style="color:#009688 !important;">السعر المعتمد <span class="text-danger">*</span></label>
                                            <input type="number" name="expert_price" class="form-control" style="border-radius:12px; padding:12px; border:2px solid #009688; font-weight:bold; font-size:1.1rem; color:#009688;" required value="{{ old('expert_price', $order->ai_price) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold" style="color:#555;">الحد الأعلى <span class="text-danger">*</span></label>
                                            <input type="number" name="expert_max_price" class="form-control" style="border-radius:12px; padding:12px; border: 1px solid #ccc;" required value="{{ old('expert_max_price', $order->ai_max_price) }}">
                                        </div>
                                    </div>
                                @endif
                                <div class="text-right mb-4" style="direction:rtl;">
                                    <label class="font-weight-bold text-warning" style="color:#f39c12 !important;font-size:0.95rem;">تقرير التقييم: <span class="text-danger">*</span></label>
                                    @php $existingReasoning = old('expert_reasoning', $order->expert_reasoning ?? $order->ai_reasoning ?? ''); @endphp
                                    <textarea name="expert_reasoning" id="expert_reasoning_editor" class="form-control" rows="5" required style="border-radius:12px;">{!! $existingReasoning !!}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success" style="border-radius:50px; padding:14px 40px; font-weight:800; font-size:1.1rem; background:#009688; border:none; width: 100%; max-width: 300px; margin: 0 auto; display: block;">
                                    <i class="bx bx-check-circle"></i> اعتماد التقييم
                                </button>
                            </form>
                        @endif

                    </div>
                </div>

                <!-- Left Sidebar (Customer Info) -->
                <div class="col-lg-3 order-lg-2">
                    <!-- Customer Card -->
                    <div class="ex-side-card">
                        <div class="ex-side-header">
                            <div><i class="bx bx-user" style="color:#888;"></i> معلومات العميل</div>
                        </div>
                        <div class="ex-side-body">
                            <div class="ex-side-row" style="background:#fff;">
                                <span class="badge" style="background:#fdf2d0; color:#d4a017; border-radius:50px; padding:6px 12px;">
                                    <i class="bx bxs-circle" style="font-size:8px; vertical-align:middle;"></i> 
                                    @if(in_array($order->status, ['estimated', 'finished', 'completed'])) تم التقييم @else قيد الانتظار @endif
                                </span>
                                <div class="ex-side-user">
                                    <span style="font-weight:700; font-size:0.85rem;">{{ $order->user->first_name ?? 'العميل' }}</span>
                                    <img src="{{ $order->user->image ? asset('storage/'.$order->user->image) : asset('assets/img/faces/user.jpg') }}">
                                </div>
                            </div>
                            <div class="ex-side-row">
                                <span class="ex-side-lbl">رقم الطلب</span>
                                <span class="ex-side-val">#{{ $order->id }}</span>
                            </div>
                            <div class="ex-side-row">
                                <span class="ex-side-lbl">الفئة</span>
                                <span class="ex-side-val">{{ $order->category->name_ar ?? 'غير محدد' }}</span>
                            </div>
                            <div class="ex-side-row">
                                <span class="ex-side-lbl">السعر</span>
                                <span class="ex-side-val">{{ number_format($order->total_price, 2) }} ر.س</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map Card -->
                    <div class="ex-side-card">
                        <div class="ex-side-header">
                            <div><i class="bx bx-map" style="color:#888;"></i> الموقع</div>
                        </div>
                        <div class="ex-side-body">
                            <div class="ex-map">
                                <i class="bx bxs-map ex-map-pin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    <script>
        // ─── TABS LOGIC ───
        function switchTab(tabId, element) {
            document.querySelectorAll('.expert-tab-panel').forEach(panel => {
                panel.style.display = 'none';
            });
            const targetPanel = document.getElementById('tab-' + tabId);
            if(targetPanel) targetPanel.style.display = 'block';

            const allSteps = document.querySelectorAll('.step-item');
            let clickedIndex = -1;
            allSteps.forEach((step, index) => {
                if(step === element) clickedIndex = index;
            });

            allSteps.forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if(index === clickedIndex) step.classList.add('active');
            });
        }

        // Initialize active tab based on elements present
        document.addEventListener('DOMContentLoaded', function() {
            const defaultTab = document.getElementById('step-evaluate');
            if(defaultTab) {
                switchTab('evaluate', defaultTab);
            }
        });
        // ─── CKEditor Init (Vanilla JS, no jQuery needed) ───────────────
        document.addEventListener('DOMContentLoaded', function () {
            var editorEl = document.getElementById('expert_reasoning_editor');
            if (!editorEl) return;

            @if(auth()->user()->hasRole('expert') && empty($order->expert_reasoning) && !empty($order->ai_reasoning))
                try {
                    var aiRaw = {!! json_encode((string) $order->ai_reasoning, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) !!};
                    var tmp = document.createElement('div');
                    tmp.innerHTML = aiRaw;
                    tmp.querySelectorAll('table').forEach(function(t) { t.remove(); });
                    var cleaned = tmp.innerHTML.trim();
                    if (cleaned && cleaned.length > 30) {
                        editorEl.value = cleaned;
                    }
                } catch(e) { console.warn('AI reasoning parse error:', e); }
            @endif

            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('expert_reasoning_editor', {
                    language: 'ar',
                    height: 350,
                    versionCheck: false,
                    removePlugins: 'elementspath',
                    resize_enabled: false,
                    contentsDir: 'rtl',
                    bodyClass: 'cke-rtl',
                    extraPlugins: '',
                });
            }
        });

        // ─── Expert Tab Switching ───────────────────────────────────────
        function switchTab(tabName, clickedEl) {
            document.querySelectorAll('.expert-tab-panel').forEach(function(p) { p.style.display = 'none'; });
            document.querySelectorAll('.step-item').forEach(function(b) {
                b.querySelector('.step-circle').style.background = '#ddd';
                b.querySelector('.step-label').style.color = '#888';
            });
            var panel = document.getElementById('tab-' + tabName);
            if (panel) { panel.style.display = 'block'; }
            if (clickedEl) {
                var circ = clickedEl.querySelector('.step-circle');
                var lbl  = clickedEl.querySelector('.step-label');
                if (circ) circ.style.background = '#f39c12';
                if (lbl)  lbl.style.color = '#111';
            }
            if (tabName === 'evaluate' && typeof CKEDITOR !== 'undefined') {
                setTimeout(function() {
                    var inst = CKEDITOR.instances['expert_reasoning_editor'];
                    if (inst) inst.resize('100%', 350);
                }, 150);
            }
        }

        // ─── Activate first stepper item on load ───────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            var firstStep = document.querySelector('.step-item');
            if (firstStep) {
                var circ = firstStep.querySelector('.step-circle');
                var lbl  = firstStep.querySelector('.step-label');
                if (circ) circ.style.background = '#f39c12';
                if (lbl)  lbl.style.color = '#111';
            }
        });
    </script>
@endsection