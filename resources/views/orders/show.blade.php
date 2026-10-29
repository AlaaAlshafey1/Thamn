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
        .row>[class*="col"] {
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
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .btn,
        .alert,
        input,
        textarea,
        select,
        label,
        p {
            font-family: 'IBM Plex Sans Arabic', sans-serif !important;
        }

        /* ================================================
           ENTRANCE ANIMATIONS
        ================================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulseGlow {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        @keyframes floatIcon {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .anim-1 {
            animation: fadeInUp 0.6s ease both;
            animation-delay: 0.05s;
        }

        .anim-2 {
            animation: fadeInUp 0.6s ease both;
            animation-delay: 0.15s;
        }

        .anim-3 {
            animation: fadeInUp 0.6s ease both;
            animation-delay: 0.25s;
        }

        .anim-4 {
            animation: fadeInUp 0.6s ease both;
            animation-delay: 0.35s;
        }

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
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .expert-welcome-banner .welcome-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .expert-welcome-banner .welcome-subtitle {
            color: rgba(255, 255, 255, 0.80);
            font-size: 0.98rem;
            font-weight: 500;
            margin-bottom: 18px;
            line-height: 1.7;
        }

        .banner-order-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
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
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 8px 14px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            margin-left: 8px;
            margin-top: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* ================================================
           ORDER CARD (BASE)
        ================================================ */
        .order-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
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
            box-shadow: 0 6px 20px rgba(193, 149, 62, 0.1);
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
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
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
            top: 0;
            left: 0;
            right: 0;
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

        .reasoning-html-content ul {
            padding-right: 25px;
        }

        .reasoning-html-content li {
            margin-bottom: 6px;
            color: #444;
        }

        .reasoning-html-content p {
            margin-bottom: 10px;
        }

        .info-label {
            color: #888;
            font-size: 0.82rem;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 600;
            color: #333;
        }

        .evaluation-result {
            background: #fcf9f2;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e9dfc6;
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
        }

        .table-custom td {
            vertical-align: middle;
        }
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
            .ex-stepper {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #fff;
                padding: 25px 50px;
                border-radius: 16px;
                position: relative;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            }

            .ex-stepper::before {
                content: '';
                position: absolute;
                top: 38px;
                left: 80px;
                right: 80px;
                height: 3px;
                background: #e2e8f0;
                z-index: 1;
            }

            .step-item {
                position: relative;
                z-index: 2;
                text-align: center;
                background: #fff;
                padding: 0 10px;
                cursor: pointer;
                transition: all 0.25s;
            }

            .step-main-info {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .step-circle {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background: #f1f5f9;
                color: #94a3b8;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                margin: 0 auto 8px;
                box-shadow: 0 0 0 4px #fff;
                transition: all 0.25s;
            }

            .step-label {
                font-weight: 700;
                color: #64748b;
                font-size: 0.95rem;
                transition: color 0.25s;
            }

            .step-stage-circle {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: #ffffff;
                border: 2px solid #cbd5e1;
                color: #64748b;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.95rem;
                font-weight: 800;
                margin: 8px auto 0;
                transition: all 0.25s;
            }

            .step-item.active .step-circle {
                background: #f39c12;
                color: #fff;
            }

            .step-item.active .step-label {
                color: #111;
                font-weight: 800;
            }

            .step-item.active .step-stage-circle {
                background: #f39c12;
                border-color: #f39c12;
                color: #fff;
                box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.2);
            }

            .ex-main-card {
                background: #fff;
                border-radius: 16px;
                padding: 40px 30px;
                text-align: center;
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            }

            .success-badge {
                width: 70px;
                height: 70px;
                margin: 0 auto 20px;
                background: url('{{ asset("assets/img/gold-badge.png") }}') center/contain no-repeat;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 30px;
                color: #333;
            }

            .success-title {
                font-size: 1.6rem;
                font-weight: 800;
                color: #333;
                margin-bottom: 10px;
            }

            .success-subtitle {
                font-size: 0.95rem;
                color: #666;
                font-weight: 600;
                margin-bottom: 40px;
            }

            /* Podium Pricing */
            .podium-container {
                display: flex;
                align-items: flex-end;
                justify-content: center;
                gap: 0;
                margin-bottom: 30px;
                margin-top: 20px;
                border-radius: 18px;
                overflow: hidden;
                max-width: 620px;
                margin-left: auto;
                margin-right: auto;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            }

            .podium-block {
                flex: 1;
                padding: 18px 8px;
                color: #fff;
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: space-between;
                box-sizing: border-box;
                text-align: center;
            }

            .podium-block .p-icon {
                width: 24px;
                height: 24px;
                background: rgba(255, 255, 255, 0.25);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 8px;
                font-size: 12px;
                flex-shrink: 0;
            }

            .podium-block .p-val {
                display: flex;
                align-items: baseline;
                justify-content: center;
                gap: 4px;
                direction: rtl;
                white-space: nowrap;
                margin: 4px 0;
                line-height: 1.2;
            }

            .podium-block .p-val .p-num {
                font-size: 1.45rem;
                font-weight: 800;
                letter-spacing: -0.5px;
            }

            .podium-block .p-val .p-curr {
                font-size: 0.82rem;
                font-weight: 700;
                opacity: 0.92;
            }

            .podium-block .p-lbl {
                font-size: 0.82rem;
                font-weight: 700;
                margin-top: 6px;
                white-space: nowrap;
                opacity: 0.95;
            }

            .podium-min {
                background: #f39c12;
                min-height: 130px;
                border-radius: 0 18px 18px 0;
            }

            .podium-main {
                background: #009688;
                min-height: 165px;
                z-index: 2;
                box-shadow: 0 -6px 20px rgba(0, 0, 0, 0.12);
                border-radius: 14px 14px 0 0;
                padding: 22px 10px;
            }

            .podium-main .p-icon {
                background: rgba(255, 255, 255, 0.35);
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .podium-main .p-val .p-num {
                font-size: 1.9rem;
                font-weight: 900;
            }

            .podium-main .p-val .p-curr {
                font-size: 0.95rem;
            }

            .podium-main .p-lbl {
                font-size: 0.9rem;
                font-weight: 800;
            }

            .podium-max {
                background: #f44336;
                min-height: 130px;
                border-radius: 18px 0 0 18px;
            }

            /* Expert Opinion Container (Desktop) */
            .expert-opinions-container {
                background: #f9fbfd;
                border-radius: 20px;
                padding: 28px 24px 22px;
                direction: rtl;
                border: 1px solid #f1f5f9;
                margin-top: 28px;
                margin-bottom: 28px;
                position: relative;
            }

            .expert-opinions-title {
                font-weight: 800;
                color: #334155;
                font-size: 1.15rem;
                margin-bottom: 24px;
                text-align: center;
            }

            .opinions-scroll-wrap {
                padding: 16px 12px 18px;
            }

            /* Report Box */
            .ex-report-box {
                background: linear-gradient(180deg, #fff9f2, #ffffff);
                border: 1px solid #f9ebd8;
                border-radius: 16px;
                padding: 25px;
                text-align: right;
                margin-top: 40px;
            }

            .ex-report-title {
                color: #f39c12;
                font-weight: 800;
                font-size: 1.1rem;
                margin-bottom: 15px;
            }

            .ex-report-content {
                color: #555;
                font-size: 0.95rem;
                line-height: 1.8;
                font-weight: 600;
            }

            /* Sidebar Card */
            .ex-side-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
                overflow: hidden;
                margin-bottom: 20px;
            }

            .ex-side-header {
                padding: 15px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-weight: 800;
            }

            .ex-side-body {
                padding: 0;
            }

            .ex-side-row {
                padding: 15px 20px;
                border-bottom: 1px solid #f8f8f8;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.9rem;
            }

            .ex-side-row:last-child {
                border-bottom: none;
            }

            .ex-side-lbl {
                color: #888;
                font-weight: 600;
            }

            .ex-side-val {
                color: #333;
                font-weight: 700;
            }

            /* User profile in side */
            .ex-side-user {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #f9f9f9;
                padding: 5px 15px 5px 5px;
                border-radius: 50px;
            }

            .ex-side-user img {
                width: 30px;
                height: 30px;
                border-radius: 50%;
            }

            /* Map */
            .ex-map {
                width: 100%;
                height: 200px;
                background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Saudi_Arabia_location_map.svg/1200px-Saudi_Arabia_location_map.svg.png') center/cover;
                position: relative;
            }

            .ex-map-pin {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: #009688;
                font-size: 24px;
            }

            /* App-like Mobile View */
            @media (max-width: 768px) {
                .expert-desktop-padding {
                    padding: 10px 5px !important;
                    margin: 0 !important;
                }

                .ex-stepper {
                    position: relative;
                    flex-direction: column;
                    padding: 16px 14px;
                    border-radius: 20px;
                    margin-bottom: 16px;
                    gap: 12px;
                    align-items: stretch;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
                    background: #fff;
                }

                .ex-stepper::before {
                    display: none;
                }

                /* خط المراحل الرأسي الواصل بين الدوائر ١ ٢ ٣ على اليسار */
                .ex-stepper::after {
                    content: '';
                    position: absolute;
                    left: 33px;
                    top: 42px;
                    bottom: 42px;
                    width: 3px;
                    background: #e2e8f0;
                    z-index: 1;
                    border-radius: 3px;
                }

                .step-item {
                    position: relative;
                    z-index: 2;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    background: #ffffff;
                    border-radius: 14px;
                    padding: 14px 16px;
                    border: 1px solid #eef2f6;
                    cursor: pointer;
                    transition: all 0.25s ease;
                }

                .step-main-info {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    gap: 14px;
                }

                .step-circle {
                    margin: 0;
                    box-shadow: none;
                    width: 38px;
                    height: 38px;
                    border-radius: 50%;
                    background: #f1f5f9;
                    color: #94a3b8;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.25rem;
                    flex-shrink: 0;
                    transition: all 0.25s;
                }

                .step-label {
                    font-size: 1.05rem;
                    font-weight: 800;
                    color: #475569;
                    transition: color 0.25s;
                }

                /* دوائر مراحل مرقمة ١ ٢ ٣ على اليسار بالضبط كما في الصورة 2 */
                .step-stage-circle {
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background: #ffffff;
                    border: 2px solid #cbd5e1;
                    color: #64748b;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.15rem;
                    font-weight: 900;
                    flex-shrink: 0;
                    margin: 0;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
                    position: relative;
                    z-index: 3;
                    transition: all 0.25s ease;
                }

                /* الحالة النشطة للمرحلة */
                .step-item.active {
                    background: #ffffff;
                    border-color: #f39c12;
                    box-shadow: 0 4px 14px rgba(243, 156, 18, 0.12);
                }

                .step-item.active .step-circle {
                    background: #f39c12;
                    color: #ffffff;
                }

                .step-item.active .step-label {
                    color: #0f172a;
                }

                .step-item.active .step-stage-circle {
                    background: #f39c12;
                    border-color: #f39c12;
                    color: #ffffff;
                    box-shadow: 0 0 0 4px rgba(243, 156, 18, 0.2);
                }

                .ex-main-card {
                    padding: 24px 16px;
                    border-radius: 20px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
                }

                /* Keep Podium Horizontal on Mobile but scale down gracefully without overlapping */
                .podium-container {
                    flex-direction: row;
                    align-items: flex-end;
                    margin-bottom: 24px;
                    border-radius: 14px;
                    gap: 0;
                }

                .podium-block {
                    padding: 14px 4px !important;
                    border-radius: 0 !important;
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    text-align: center;
                    height: auto !important;
                }

                .podium-min {
                    min-height: 110px !important;
                    height: auto !important;
                    border-radius: 0 14px 14px 0 !important;
                }

                .podium-max {
                    min-height: 110px !important;
                    height: auto !important;
                    border-radius: 14px 0 0 14px !important;
                }

                .podium-main {
                    min-height: 130px !important;
                    height: auto !important;
                    z-index: 2;
                    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.12);
                    border-radius: 10px 10px 0 0 !important;
                }

                .podium-block .p-icon {
                    margin: 0 auto 4px !important;
                    width: 20px !important;
                    height: 20px !important;
                    font-size: 11px !important;
                }

                .podium-block .p-val {
                    margin: 3px 0 !important;
                    display: flex !important;
                    align-items: baseline !important;
                    justify-content: center !important;
                    gap: 3px !important;
                    line-height: 1.2 !important;
                }

                .podium-block .p-val .p-num {
                    font-size: 1.05rem !important;
                    font-weight: 800 !important;
                }

                .podium-block .p-val .p-curr {
                    font-size: 0.68rem !important;
                    font-weight: 700 !important;
                }

                .podium-main .p-val .p-num {
                    font-size: 1.25rem !important;
                    font-weight: 900 !important;
                }

                .podium-main .p-val .p-curr {
                    font-size: 0.75rem !important;
                    font-weight: 700 !important;
                }

                .podium-block .p-lbl {
                    font-size: 0.72rem !important;
                    margin-top: 4px !important;
                    line-height: 1.2 !important;
                    font-weight: 700 !important;
                }

                .podium-main {
                    order: 0;
                }

                /* Expert Opinion Options horizontally scrollable on mobile with generous padding */
                .expert-opinions-container {
                    padding: 22px 14px 18px !important;
                    margin: 22px 0 !important;
                    border-radius: 16px !important;
                }

                .expert-opinions-title {
                    font-size: 1.05rem !important;
                    margin-bottom: 20px !important;
                }

                .opinions-scroll-wrap {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    scrollbar-width: none;
                    padding: 16px 6px 16px !important;
                }

                .opinions-scroll-wrap::-webkit-scrollbar {
                    display: none;
                }

                .opinions-scroll-wrap .d-flex {
                    flex-wrap: nowrap !important;
                    justify-content: flex-start !important;
                    gap: 12px !important;
                    padding: 8px 4px !important;
                }

                .rating-display-card {
                    flex: 0 0 auto !important;
                    min-width: 110px !important;
                    max-width: 122px !important;
                    padding: 16px 8px 12px !important;
                }

                /* Sidebar on mobile */
                .col-lg-3 {
                    padding: 0 10px;
                }

                .ex-side-card {
                    border-radius: 20px;
                    margin-top: 16px;
                    border: 1px solid #eee;
                }

                .ex-side-header {
                    padding: 18px 20px;
                    font-size: 1.1rem;
                }

                .ex-side-row {
                    padding: 16px 20px;
                }

                /* Forms & Buttons */
                .form-control,
                .form-select {
                    border-radius: 16px !important;
                    padding: 16px 20px !important;
                    font-size: 1.05rem !important;
                }

                .btn-success {
                    width: 100% !important;
                    max-width: none !important;
                    border-radius: 18px !important;
                    padding: 18px !important;
                    font-size: 1.2rem !important;
                    margin-top: 15px;
                }

                /* Responsive Specs Table inside Tab */
                #tab-specs table,
                #tab-specs thead,
                #tab-specs tbody,
                #tab-specs th,
                #tab-specs td,
                #tab-specs tr {
                    display: block;
                    width: 100%;
                    text-align: right;
                }

                #tab-specs thead {
                    display: none;
                }

                #tab-specs tr {
                    border: 1px solid #eee;
                    border-radius: 16px;
                    margin-bottom: 12px;
                    padding: 16px;
                    background: #fff;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
                }

                #tab-specs td:nth-child(1) {
                    display: none;
                }

                /* hide ID */
                #tab-specs td:nth-child(2) {
                    font-size: 1.05rem !important;
                    padding: 0 0 10px 0 !important;
                    border-bottom: 1px dashed #eee;
                    color: #111 !important;
                }

                #tab-specs td:nth-child(3) {
                    padding: 10px 0 0 0 !important;
                    font-size: 1rem !important;
                    color: #009688 !important;
                }

                #tab-specs td:nth-child(3) span {
                    padding: 6px 14px !important;
                    font-size: 0.9rem !important;
                }
            }

            /* =========================================================
               1. IN-DASHBOARD ALBUM LIGHTBOX (الصورة 1)
               ========================================================= */
            .expert-gallery-thumb {
                position: relative;
                border-radius: 14px;
                overflow: hidden;
                border: 2.5px solid #f1f5f9;
                aspect-ratio: 1;
                cursor: pointer;
                background: #f8fafc;
                box-shadow: 0 3px 10px rgba(0,0,0,0.04);
                transition: all 0.25s ease;
            }
            .expert-gallery-thumb:hover {
                border-color: #C1953E;
                transform: translateY(-3px);
                box-shadow: 0 8px 22px rgba(193, 149, 62, 0.2);
            }
            .expert-gallery-thumb .thumb-hover-overlay {
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(15, 23, 42, 0.35);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.2s ease;
            }
            .expert-gallery-thumb:hover .thumb-hover-overlay {
                opacity: 1;
            }
            .expert-gallery-thumb .thumb-hover-overlay span {
                background: rgba(255, 255, 255, 0.95);
                color: #0f172a;
                padding: 6px 14px;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 800;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            .expert-gallery-thumb .thumb-counter-badge {
                position: absolute;
                bottom: 8px;
                left: 8px;
                background: rgba(15, 23, 42, 0.75);
                color: #fff;
                padding: 3px 9px;
                border-radius: 8px;
                font-size: 0.72rem;
                font-weight: 700;
                backdrop-filter: blur(4px);
            }

            /* Lightbox Modal */
            .expert-lightbox-modal {
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                z-index: 999999;
                background: rgba(15, 23, 42, 0.95);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                display: none;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                padding: 16px;
                direction: rtl;
                animation: lbFadeIn 0.25s ease-out;
            }
            .expert-lightbox-modal.active {
                display: flex;
            }
            @keyframes lbFadeIn {
                from { opacity: 0; transform: scale(0.98); }
                to { opacity: 1; transform: scale(1); }
            }
            .lb-toolbar {
                width: 100%;
                max-width: 940px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #fff;
                padding: 8px 16px;
                z-index: 2;
            }
            .lb-counter {
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.25);
                padding: 6px 16px;
                border-radius: 50px;
                font-size: 0.9rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .lb-btn-action {
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.25);
                color: #fff;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .lb-btn-action:hover {
                background: #C1953E;
                border-color: #C1953E;
                transform: scale(1.08);
            }
            .lb-close-btn {
                background: rgba(255, 255, 255, 0.18);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: #fff;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .lb-close-btn:hover {
                background: #ef4444;
                border-color: #ef4444;
                transform: rotate(90deg) scale(1.08);
            }
            .lb-stage {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
                max-width: 960px;
                max-height: 72vh;
                padding: 10px;
            }
            .lb-main-img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                border-radius: 14px;
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
                transition: transform 0.25s ease, opacity 0.2s ease;
                cursor: zoom-in;
            }
            .lb-main-img.zoomed {
                transform: scale(1.6);
                cursor: zoom-out;
            }
            .lb-nav-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: #fff;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                cursor: pointer;
                transition: all 0.2s;
                z-index: 5;
                user-select: none;
            }
            .lb-nav-btn:hover {
                background: #C1953E;
                border-color: #C1953E;
                transform: translateY(-50%) scale(1.1);
            }
            .lb-prev { right: 12px; }
            .lb-next { left: 12px; }
            .lb-thumbs-strip {
                display: flex;
                gap: 10px;
                padding: 12px;
                max-width: 900px;
                overflow-x: auto;
                z-index: 2;
            }
            .lb-thumb-item {
                width: 60px;
                height: 60px;
                border-radius: 10px;
                overflow: hidden;
                border: 2px solid transparent;
                opacity: 0.5;
                cursor: pointer;
                flex-shrink: 0;
                transition: all 0.2s;
            }
            .lb-thumb-item.active {
                border-color: #C1953E;
                opacity: 1;
                transform: scale(1.08);
                box-shadow: 0 0 12px rgba(193, 149, 62, 0.6);
            }
            .lb-thumb-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* =========================================================
               2. INTERACTIVE RATING CARDS & APPROVAL EFFECT (الصورة 2)
               ========================================================= */
            .rating-choice-card {
                flex: 1;
                min-width: 110px;
                max-width: 145px;
                background: #ffffff;
                border-radius: 18px;
                padding: 18px 10px 14px;
                text-align: center;
                position: relative;
                border: 2.5px solid #e2e8f0;
                cursor: pointer;
                user-select: none;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            }
            .rating-choice-card:hover {
                border-color: #009688;
                transform: translateY(-3px);
                box-shadow: 0 6px 18px rgba(0, 150, 136, 0.15);
            }
            .rating-choice-card.active {
                border: 3.5px solid #009688 !important;
                background: rgba(0, 150, 136, 0.08) !important;
                transform: translateY(-4px) scale(1.04) !important;
                box-shadow: 0 10px 25px rgba(0, 150, 136, 0.28) !important;
                animation: cardSelectPulse 0.35s ease-out;
            }
            @keyframes cardSelectPulse {
                0% { transform: scale(0.95); }
                50% { transform: scale(1.06); }
                100% { transform: scale(1.04) translateY(-4px); }
            }
            .rating-choice-card .choice-check {
                position: absolute;
                top: -10px;
                right: -10px;
                background: #009688;
                color: #fff;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                box-shadow: 0 4px 10px rgba(0,150,136,0.4);
                border: 2.5px solid #fff;
                z-index: 5;
                animation: badgePop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            .rating-choice-card.active .choice-check {
                display: flex;
            }
            @keyframes badgePop {
                from { transform: scale(0); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            .btn-evaluate-submit {
                border-radius: 50px;
                padding: 16px 42px;
                font-weight: 800;
                font-size: 1.15rem;
                background: linear-gradient(135deg, #009688 0%, #00796b 100%);
                border: none;
                color: #fff;
                width: 100%;
                max-width: 320px;
                margin: 0 auto;
                display: block;
                box-shadow: 0 6px 20px rgba(0, 150, 136, 0.35);
                transition: all 0.3s ease;
                position: relative;
            }
            .btn-evaluate-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 28px rgba(0, 150, 136, 0.45);
                color: #fff;
            }
            .btn-evaluate-submit:active {
                transform: scale(0.98);
            }
            .btn-evaluate-submit.submitting {
                opacity: 0.92;
                pointer-events: none;
                background: #00796b !important;
                box-shadow: 0 0 0 5px rgba(0, 150, 136, 0.35) !important;
            }

            /* =========================================================
               3. EXIT BUTTON AFTER VALUATION (الصورة 3)
               ========================================================= */
            .expert-exit-wrap {
                margin: 22px 0;
                text-align: center;
            }
            .btn-expert-exit {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                padding: 14px 40px;
                border-radius: 50px;
                background: #ffffff;
                border: 2px solid #e2e8f0;
                color: #334155;
                font-weight: 800;
                font-size: 1.05rem;
                text-decoration: none;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
                transition: all 0.25s ease;
            }
            .btn-expert-exit i {
                font-size: 1.45rem;
                color: #ef4444;
                transition: transform 0.25s;
            }
            .btn-expert-exit:hover {
                background: #fff1f2;
                border-color: #fecdd3;
                color: #e11d48;
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(225, 29, 72, 0.16);
                text-decoration: none;
            }
            .btn-expert-exit:hover i {
                transform: translateX(-4px);
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

                <!-- Stepper with numbered stage circles ١ ٢ ٣ -->
                <div class="ex-stepper">
                    <div class="step-item" onclick="switchTab('specs', this)" id="step-specs">
                        <div class="step-main-info">
                            <div class="step-circle"><i class="bx bx-list-check"></i></div>
                            <div class="step-label">مواصفات السلعة</div>
                        </div>
                        <div class="step-stage-circle">١</div>
                    </div>
                    <div class="step-item" onclick="switchTab('photos', this)" id="step-photos">
                        <div class="step-main-info">
                            <div class="step-circle"><i class="bx bx-images"></i></div>
                            <div class="step-label">صور السلعة</div>
                        </div>
                        <div class="step-stage-circle">٢</div>
                    </div>
                    <div class="step-item active" onclick="switchTab('evaluate', this)" id="step-evaluate">
                        <div class="step-main-info">
                            <div class="step-circle"><i class="bx bx-check"></i></div>
                            <div class="step-label">السعر التقديري</div>
                        </div>
                        <div class="step-stage-circle">٣</div>
                    </div>
                </div>

                <!-- TAB: Specifications (الخطوة رقم ١) -->
                <div class="ex-main-card expert-tab-panel" id="tab-specs" style="display:none; text-align:right;">
                    
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div style="font-weight:900;font-size:1.15rem;color:#111;display:flex;align-items:center;gap:10px;">
                            <i class="bx bx-file text-warning" style="font-size:1.45rem;"></i> تقرير مواصفات السلعة والتقييم الذكي
                        </div>
                        <span class="badge" style="background: rgba(0, 150, 136, 0.1); color: #009688; border-radius: 20px; font-weight: 700; padding: 6px 14px; font-size: 0.8rem;">
                            <i class="bx bx-bot"></i> تقرير AI المعتمد
                        </span>
                    </div>

                    <!-- تقرير التقييم الذكي (البطاقات الانفوجرافيك والتحليل) -->
                    @if(!empty($order->ai_reasoning))
                        <div class="ai-report-presentation mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.03);">
                            @if(!preg_match('/<[a-z][\s\S]*>/i', $order->ai_reasoning))
                                {!! \Illuminate\Support\Str::markdown((string) $order->ai_reasoning) !!}
                            @else
                                {!! $order->ai_reasoning !!}
                            @endif
                        </div>
                    @else
                        <div class="p-4 text-center rounded-3 mb-4" style="background: #fbfbfb; border: 1.5px dashed #d1d5db; color: #6b7280;">
                            <i class="bx bx-info-circle fs-3 text-warning mb-2 d-block"></i>
                            لا يتوفر تقرير ذكي تم توليده مسبقاً لهذه السلعة، يرجى مراجعة تفاصيل ومدخلات السلعة بالأسفل.
                        </div>
                    @endif

                    <!-- تفاصيل ومدخلات السلعة الكاملة (عند الرغبة في المزيد يعمل له انسدال) -->
                    <div class="specs-accordion-wrapper mt-3 pt-2">
                        <div class="d-flex align-items-center justify-content-between p-3" 
                             onclick="toggleSpecsDetails()" 
                             role="button" 
                             id="btnToggleSpecsDetails"
                             tabindex="0"
                             style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 12px; cursor: pointer; transition: all 0.2s ease;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bx-list-ul text-primary" style="font-size:1.3rem;"></i>
                                <span style="font-weight: 800; color: #334155; font-size: 0.95rem;">
                                    عرض كافة تفاصيل ومدخلات السلعة (الأسئلة والإجابات التفصيلية)
                                </span>
                                <span class="badge" style="background: #e2e8f0; color: #475569; font-size: 0.78rem; font-weight: 700; border-radius: 8px;">
                                    {{ $order->details->count() }} بند
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.85rem; font-weight: 700;">
                                <span id="toggleSpecsText">انقر لعرض المزيد</span>
                                <i class="bx bx-chevron-down" id="toggleSpecsChevron" style="font-size:1.25rem; transition: transform 0.3s ease;"></i>
                            </div>
                        </div>

                        <div id="specsDetailsWrapper" style="display: {{ empty($order->ai_reasoning) ? 'block' : 'none' }}; margin-top: 14px;">
                            <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                                <table style="width:100%;border-collapse:collapse;">
                                    <thead>
                                        <tr style="background:#f1f5f9;border-bottom:2px solid #e2e8f0;">
                                            <th style="padding:12px 16px;color:#475569;font-size:0.82rem;font-weight:800;text-align:right;">#</th>
                                            <th style="padding:12px 16px;color:#475569;font-size:0.82rem;font-weight:800;text-align:right;">السؤال</th>
                                            <th style="padding:12px 16px;color:#475569;font-size:0.82rem;font-weight:800;text-align:right;">الإجابة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->details as $index => $detail)
                                            <tr style="border-bottom:1px solid #f1f5f9; background: {{ $index % 2 == 0 ? '#ffffff' : '#fafafa' }};">
                                                <td style="padding:12px 16px;color:#94a3b8;font-size:0.85rem;font-weight:700;">{{ $index + 1 }}</td>
                                                <td style="padding:12px 16px;font-weight:700;color:#1e293b;font-size:0.9rem;">{{ $detail->question->question_ar ?? '-' }}</td>
                                                <td style="padding:12px 16px;font-weight:600;color:#475569;font-size:0.9rem;">
                                                    @if($detail->option)
                                                        <span style="background:rgba(243,156,18,0.12);color:#d97706;padding:4px 10px;border-radius:50px;font-size:0.82rem;font-weight:800;">{{ $detail->option->option_ar ?? $detail->value }}</span>
                                                    @elseif($detail->value)
                                                        {{ $detail->value }}
                                                    @else
                                                        <span style="color:#cbd5e1;">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" style="text-align:center;padding:40px;color:#94a3b8;">
                                                    <i class="bx bx-info-circle" style="font-size:2rem;display:block;margin-bottom:8px;"></i> لا توجد مواصفات
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- التنقل إلى الخطوة التالية -->
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-warning px-4 py-2" onclick="switchTab('photos', document.getElementById('step-photos'))" style="border-radius: 12px; font-weight: 800; color: #fff; background: #f39c12; border: none; box-shadow: 0 4px 12px rgba(243,156,18,0.25); font-size: 0.95rem;">
                            الخطوة التالية: صور السلعة <i class="bx bx-left-arrow-alt align-middle ms-1" style="font-size: 1.15rem;"></i>
                        </button>
                    </div>
                </div>

                <!-- TAB: Photos -->
                <div class="ex-main-card expert-tab-panel" id="tab-photos" style="display:none; text-align:right;">
                    <div
                        style="font-weight:900;font-size:1.1rem;color:#111;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                        <i class="bx bx-images" style="color:#f39c12;"></i> صور السلعة
                        <span style="font-size:0.8rem; font-weight:normal; color:#888;">(انقر على أي صورة لفتح الألبوم المكبر والتنقل بين الصور)</span>
                    </div>
                    @php
                        $expertImages = $order->files->where('type', 'image')->values();
                    @endphp
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;">
                        @forelse($expertImages as $imgIndex => $image)
                            <div class="expert-gallery-thumb" onclick="openAlbumLightbox({{ $imgIndex }})" role="button" tabindex="0" title="انقر لفتح الألبوم المكبر">
                                <img src="{{ asset('storage/' . $image->file_path) }}" alt="صورة السلعة {{ $imgIndex + 1 }}" style="width:100%;height:100%;object-fit:cover;">
                                <div class="thumb-hover-overlay">
                                    <span><i class="bx bx-zoom-in"></i> تكبير الصورة</span>
                                </div>
                                <span class="thumb-counter-badge">{{ $imgIndex + 1 }} / {{ $expertImages->count() }}</span>
                            </div>
                        @empty
                            <div style="grid-column:1/-1;text-align:center;padding:50px;color:#ccc;">
                                <i class="bx bx-image-alt" style="font-size:3rem;display:block;margin-bottom:10px;"></i>
                                لا توجد صور مرفقة لهذا الطلب
                            </div>
                        @endforelse
                    </div>

                    <!-- أزرار التنقل للصور -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary px-3 py-2" onclick="switchTab('specs', document.getElementById('step-specs'))" style="border-radius: 12px; font-weight: 700; font-size: 0.9rem;">
                            <i class="bx bx-right-arrow-alt align-middle me-1" style="font-size: 1.15rem;"></i> مواصفات السلعة
                        </button>
                        <button type="button" class="btn btn-warning px-4 py-2" onclick="switchTab('evaluate', document.getElementById('step-evaluate'))" style="border-radius: 12px; font-weight: 800; color: #fff; background: #f39c12; border: none; box-shadow: 0 4px 12px rgba(243,156,18,0.25); font-size: 0.95rem;">
                            الخطوة التالية: السعر التقديري <i class="bx bx-left-arrow-alt align-middle ms-1" style="font-size: 1.15rem;"></i>
                        </button>
                    </div>
                </div>

                <!-- TAB: Evaluate -->
                <div class="ex-main-card expert-tab-panel" id="tab-evaluate">

                    @if(in_array($order->status, ['estimated', 'evaluated', 'finished', 'completed', 'reEstimated']) && $order->status !== 'beingReEstimated')
                        <!-- Evaluated State -->
                        <div class="success-badge">
                            <i class='bx bxs-badge-check'
                                style="font-size: 70px; color: #f39c12; text-shadow: 0 4px 10px rgba(0,0,0,0.1);"></i>
                        </div>
                        <div class="success-title">تم اعتماد التقييم بنجاح</div>
                        <div class="success-subtitle">تم اعتماد تثمين المنتج ، وسيتم اشعار العميل بحالة التثمين</div>

                        <!-- Alert: Wallet Balance Added (Responsive & Well-arranged) -->
                        <div class="wallet-credit-alert mb-4 shadow-sm"
                            style="border-radius: 16px; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 1.5px solid #81c784; color: #1b5e20; text-align: right; max-width: 620px; margin-left: auto; margin-right: auto; padding: 16px 20px;">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3 flex-grow-1">
                                    <div
                                        style="width: 46px; height: 46px; border-radius: 12px; background: #2e7d32; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(46,125,50,0.25);">
                                        <i class="bx bx-wallet"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; font-size: 1.05rem; color: #1b5e20; margin-bottom: 4px; line-height: 1.4;">
                                            راجع محفظتك تم إضافة رصيدك بنجاح
                                        </div>
                                        <div style="font-size: 0.84rem; color: #2e7d32; font-weight: 600; line-height: 1.5;">
                                            تم إيداع أتعاب التقييم في محفظتك بنجاح، يمكنك متابعة رصيدك وسحب أرباحك في أي وقت.
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-1 pt-sm-0 text-center text-sm-start flex-shrink-0">
                                    <a href="{{ route('withdrawals.my') }}" class="btn btn-success w-100 w-sm-auto"
                                        style="border-radius: 10px; font-weight: 800; white-space: nowrap; background: #2e7d32; border: none; padding: 9px 20px; box-shadow: 0 2px 6px rgba(46,125,50,0.3); font-size: 0.88rem; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                        <i class="bx bx-wallet"></i> المحفظة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Podium Pricing -->
                        <div class="podium-container">
                            <div class="podium-block podium-min">
                                <div class="p-icon"><i class="bx bx-minus"></i></div>
                                <div class="p-val">
                                    <span class="p-num">{{ number_format($order->expert_min_price, 0) }}</span>
                                    <span class="p-curr">ر.س</span>
                                </div>
                                <div class="p-lbl">الحد الأدنى</div>
                            </div>
                            <div class="podium-block podium-main">
                                <div class="p-icon"><i class="bx bx-check"></i></div>
                                <div class="p-val">
                                    <span class="p-num">{{ number_format($order->expert_price, 0) }}</span>
                                    <span class="p-curr">ر.س</span>
                                </div>
                                <div class="p-lbl">السعر المعتمد</div>
                            </div>
                            <div class="podium-block podium-max">
                                <div class="p-icon"><i class="bx bx-plus"></i></div>
                                <div class="p-val">
                                    <span class="p-num">{{ number_format($order->expert_max_price, 0) }}</span>
                                    <span class="p-curr">ر.س</span>
                                </div>
                                <div class="p-lbl">الحد الأعلى</div>
                            </div>
                        </div>

                        <!-- Action Button: View & Download Invoice PDF -->
                        <div class="text-center my-4">
                            <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('valuation-order.pdf', ['order' => $order->id]) }}"
                                target="_blank" class="btn btn-lg px-4 py-3"
                                style="background: linear-gradient(135deg, #162221 0%, #1c2e2c 100%); color: #C1953E; font-weight: 800; border: 1.5px solid #C1953E; border-radius: 14px; box-shadow: 0 4px 15px rgba(193, 149, 62, 0.25); display: inline-flex; align-items: center; gap: 10px; text-decoration: none;">
                                <i class="bx bxs-file-pdf" style="font-size: 1.5rem; color: #C1953E;"></i>
                                <span>عرض وتحميل نسخة فاتورة التثمين المعتمدة (PDF)</span>
                            </a>
                        </div>

                        @if($order->evaluation_type === 'ai' && $order->ai_valuation_rating_id)
                            <!-- AI Rating Result Cards -->
                            <div class="expert-opinions-container">
                                <div class="expert-opinions-title">
                                    رأي الخبير المعتمد : ( <span
                                        style="color:#009688;">{{ $aiRatings->where('id', $order->ai_valuation_rating_id)->first()->name_ar ?? '' }}</span>
                                    )
                                </div>
                                <div class="opinions-scroll-wrap">
                                    <div class="d-flex flex-wrap justify-content-center" style="gap:14px;">
                                        @foreach($aiRatings as $rating)
                                            @php 
                                                $isSelected = $order->ai_valuation_rating_id == $rating->id; 
                                            @endphp
                                            <div class="rating-display-card {{ $isSelected ? 'selected' : '' }}" style="flex:1; min-width:115px; max-width:145px; background:{{ $rating->color ?? '#fff' }}; 
                                                                            border-radius:18px; padding:18px 10px 14px; text-align:center; position:relative;
                                                                            border: {{ $isSelected ? '2.5px solid #009688' : '2px solid transparent' }};
                                                                            opacity: {{ $isSelected ? '1' : '0.55' }};
                                                                            transform: {{ $isSelected ? 'translateY(-2px)' : 'none' }};
                                                                            transition: all 0.25s ease;
                                                                            box-shadow: {{ $isSelected ? '0 8px 22px rgba(0,150,136,0.18)' : '0 2px 6px rgba(0,0,0,0.02)' }};
                                                                            margin: 4px;
                                                                            ">
                                                @if($isSelected)
                                                    <div
                                                        style="position:absolute; top:-9px; right:-9px; background:#009688; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:15px; box-shadow:0 3px 8px rgba(0,150,136,0.35); z-index:10; border:2px solid #fff;">
                                                        <i class="bx bx-check"></i>
                                                    </div>
                                                @endif
                                                <div
                                                    style="margin-bottom:10px; height:36px; display:flex; align-items:center; justify-content:center;">
                                                    @if($rating->icon)
                                                        <img src="{{ asset('storage/' . $rating->icon) }}"
                                                            style="width:32px; height:32px; object-fit:contain; filter: {{ $isSelected ? 'none' : 'grayscale(100%)' }};">
                                                    @else
                                                        <i class="bx bx-bar-chart-alt-2"
                                                            style="font-size:26px; color:{{ $isSelected ? '#009688' : '#777' }};"></i>
                                                    @endif
                                                </div>
                                                <div
                                                    style="font-weight:800; font-size:0.88rem; color:{{ $isSelected ? '#0f172a' : '#64748b' }}; line-height:1.3;">
                                                    {{ $rating->name_ar }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- زر مغادرة بعد الاعتماد (الصورة 3) -->
                        <div class="expert-exit-wrap">
                            <a href="{{ route('orders.index') }}" class="btn-expert-exit" title="مغادرة إلى قائمة الطلبات">
                                <i class="bx bx-log-out-circle"></i>
                                <span>مغادرة إلى قائمة الطلبات</span>
                            </a>
                        </div>

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
                        @if($order->objection_reason)
                            <div class="alert alert-warning mb-4 shadow-sm" style="border-radius: 12px; border-right: 5px solid #f39c12; background: #fffdf5;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bx bx-error-circle" style="font-size: 24px; color: #f39c12;"></i>
                                    <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 1.05rem;">تنبيه: هذا الطلب معروض نتيجة تقديم العميل لاحتجاج على التقييم السابق</h6>
                                </div>
                                <div class="p-2 rounded bg-white border" style="font-size: 0.95rem; color: #333;">
                                    <strong>سبب الاحتجاج:</strong> {{ $order->objection_reason }}
                                    @if($order->total_price)
                                        <div class="mt-2 pt-2 border-top">
                                            <strong>السعر السابق المعترض عليه:</strong>
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.95rem; font-weight: bold;">{{ number_format($order->total_price, 0) }} ريال</span>
                                        </div>
                                    @endif
                                    @if($order->objected_at)
                                        <div class="text-muted small mt-1"><i class="bx bx-time"></i> تاريخ الاحتجاج: {{ $order->objected_at->format('Y-m-d H:i') }} ({{ $order->objected_at->diffForHumans() }})</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="success-title mb-4 text-primary" style="font-size:1.3rem;">اعتماد تقييم الطلب</div>
                        <form method="POST" action="{{ route('orders.expert.evaluate', $order->id) }}"
                            id="expertEvaluationForm"
                            style="text-align:right;">
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
                                        <div
                                            style="background:#f8f9fa; border:1px solid #eee; border-radius:12px; padding:15px; text-align:center;">
                                            <div style="color:#555; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">الحد
                                                الأدنى</div>
                                            <div style="font-size:1.2rem; font-weight:900; color:#333;">
                                                {{ number_format($order->ai_min_price ?? 0, 0) }} <small
                                                    style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div
                                            style="background:rgba(0, 150, 136, 0.05); border:2px solid #009688; border-radius:12px; padding:15px; text-align:center;">
                                            <div style="color:#009688; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">
                                                السعر المعتمد</div>
                                            <div style="font-size:1.3rem; font-weight:900; color:#009688;">
                                                {{ number_format($order->ai_price ?? 0, 0) }} <small
                                                    style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div
                                            style="background:#f8f9fa; border:1px solid #eee; border-radius:12px; padding:15px; text-align:center;">
                                            <div style="color:#555; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">الحد
                                                الأعلى</div>
                                            <div style="font-size:1.2rem; font-weight:900; color:#333;">
                                                {{ number_format($order->ai_max_price ?? 0, 0) }} <small
                                                    style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row text-right mb-4" style="direction:rtl;">
                                    <div class="col-md-12">
                                        <label class="font-weight-bold d-block mb-3" style="color:#2c3e50;font-size:1rem;">
                                            <i class="bx bx-check-shield text-success"></i> رأي الخبير المعتمد حول التقييم الذكي <span class="text-danger">*</span>
                                            <span style="font-size:0.82rem; font-weight:normal; color:#64748b; margin-right:6px;">(انقر على أي خيار لتحديده وتثبيته)</span>
                                        </label>

                                        <!-- Hidden input to store chosen rating ID -->
                                        <input type="hidden" name="ai_valuation_rating_id" id="aiRatingSelectedInput" value="{{ old('ai_valuation_rating_id') }}" required>

                                        <!-- Interactive Choice Cards with Active Highlight & Check Badge -->
                                        <div class="d-flex flex-wrap justify-content-center" style="gap:14px; margin-bottom:12px;">
                                            @foreach($aiRatings as $rating)
                                                @php
                                                    $isCardActive = old('ai_valuation_rating_id') == $rating->id;
                                                @endphp
                                                <div class="rating-choice-card {{ $isCardActive ? 'active' : '' }}" 
                                                     onclick="selectExpertRating({{ $rating->id }}, '{{ addslashes($rating->name_ar) }}', this)"
                                                     role="button"
                                                     tabindex="0"
                                                     data-rating-id="{{ $rating->id }}"
                                                     title="{{ $rating->name_ar }}">
                                                    
                                                    <!-- Checkmark badge upon selection -->
                                                    <div class="choice-check">
                                                        <i class="bx bx-check"></i>
                                                    </div>

                                                    <div style="margin-bottom:8px; height:36px; display:flex; align-items:center; justify-content:center;">
                                                        @if($rating->icon)
                                                            <img src="{{ asset('storage/' . $rating->icon) }}" style="width:32px; height:32px; object-fit:contain;">
                                                        @else
                                                            <i class="bx bx-bar-chart-alt-2" style="font-size:26px; color:#009688;"></i>
                                                        @endif
                                                    </div>
                                                    <div style="font-weight:800; font-size:0.92rem; color:#1e293b; line-height:1.3;">
                                                        {{ $rating->name_ar }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Clear Visual Confirmation of Selected Opinion -->
                                        <div id="selectedRatingFeedback" style="display: {{ old('ai_valuation_rating_id') ? 'block' : 'none' }}; text-align:center; padding:10px 18px; border-radius:14px; background:rgba(0,150,136,0.08); border:1.5px solid rgba(0,150,136,0.3); font-weight:800; font-size:0.92rem; color:#00796b; margin-top:8px;">
                                            @if(old('ai_valuation_rating_id'))
                                                <i class="bx bx-check-circle" style="color:#009688; font-size:1.1rem; vertical-align:middle;"></i> تم تثبيت واختيار: <strong style="color:#009688;">{{ $aiRatings->where('id', old('ai_valuation_rating_id'))->first()->name_ar ?? '' }}</strong> بنجاح
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Hide prices since we use AI prices -->
                                <input type="hidden" name="expert_price" value="{{ $order->ai_price ?? $order->total_price ?? 0 }}">
                                <input type="hidden" name="expert_min_price" value="{{ $order->ai_min_price ?? 0 }}">
                                <input type="hidden" name="expert_max_price" value="{{ $order->ai_max_price ?? 0 }}">
                            @elseif($order->evaluation_type === 'expert')
                                <!-- باقة الخبير: الخبير يغير السعر فقط بدون كتابة كلام -->
                                <div class="row text-right mb-4" style="direction:rtl;">
                                    <div class="col-md-12 mb-2">
                                        <label class="font-weight-bold text-primary" style="font-size:0.95rem;">
                                            <i class="bx bx-edit-alt"></i> تحديد وتعديل السعر بواسطة الخبير:
                                        </label>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold" style="color:#555;">الحد الأدنى <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="expert_min_price" class="form-control"
                                            style="border-radius:12px; padding:12px; border: 1px solid #ccc;" required
                                            value="{{ old('expert_min_price', $order->expert_min_price ?? $order->ai_min_price ?? round(($order->total_price ?: 50000) * 0.8)) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-success" style="color:#009688 !important;">السعر المعتمد
                                            <span class="text-danger">*</span></label>
                                        <input type="number" name="expert_price" class="form-control"
                                            style="border-radius:12px; padding:12px; border:2px solid #009688; font-weight:bold; font-size:1.1rem; color:#009688;"
                                            required value="{{ old('expert_price', $order->expert_price ?? $order->ai_price ?? $order->total_price) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold" style="color:#555;">الحد الأعلى <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="expert_max_price" class="form-control"
                                            style="border-radius:12px; padding:12px; border: 1px solid #ccc;" required
                                            value="{{ old('expert_max_price', $order->expert_max_price ?? $order->ai_max_price ?? round(($order->total_price ?: 50000) * 1.2)) }}">
                                    </div>
                                </div>
                            @else
                                <!-- باقي الباقات (التقييم الشامل best وغيرها): عرض الأسعار المقترحة واعتمادها مباشرة -->
                                <div class="row text-right mb-4" style="direction:rtl;">
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold text-primary" style="font-size:0.95rem;">
                                            <i class="bx bx-check-shield"></i> الأسعار المعتمدة من التقييم الذكي:
                                        </label>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div
                                            style="background:#f8f9fa; border:1px solid #eee; border-radius:12px; padding:15px; text-align:center;">
                                            <div style="color:#555; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">الحد
                                                الأدنى</div>
                                            <div style="font-size:1.2rem; font-weight:900; color:#333;">
                                                {{ number_format($order->ai_min_price ?? round(($order->total_price ?: 50000) * 0.8), 0) }}
                                                <small style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div
                                            style="background:rgba(0, 150, 136, 0.05); border:2px solid #009688; border-radius:12px; padding:15px; text-align:center;">
                                            <div style="color:#009688; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">
                                                السعر المعتمد</div>
                                            <div style="font-size:1.3rem; font-weight:900; color:#009688;">
                                                {{ number_format($order->ai_price ?? $order->total_price, 0) }} <small
                                                    style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div
                                            style="background:#f8f9fa; border:1px solid #eee; border-radius:12px; padding:15px; text-align:center;">
                                            <div style="color:#555; font-size:0.85rem; font-weight:bold; margin-bottom:5px;">الحد
                                                الأعلى</div>
                                            <div style="font-size:1.2rem; font-weight:900; color:#333;">
                                                {{ number_format($order->ai_max_price ?? round(($order->total_price ?: 50000) * 1.2), 0) }}
                                                <small style="font-size:0.7rem; color:#888;">ر.س</small></div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="expert_price" value="{{ $order->ai_price ?? $order->total_price ?? 0 }}">
                                <input type="hidden" name="expert_min_price" value="{{ $order->ai_min_price ?? 0 }}">
                                <input type="hidden" name="expert_max_price" value="{{ $order->ai_max_price ?? 0 }}">
                            @endif

                            <!-- تقرير التقييم الذكي: شكل متهندل وأنيق دون حاجة لـ CKEditor أو كتابة نصوص -->
                            <div class="text-right mb-4" style="direction:rtl;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="font-weight-bold mb-0" style="color:#2c3e50; font-size:0.95rem;">
                                        <i class="bx bx-file text-warning ml-1"></i> تقرير التقييم الذكي:
                                    </label>
                                    <span class="badge"
                                        style="background: rgba(0, 150, 136, 0.1); color: #009688; border-radius: 20px; font-weight: 700; padding: 4px 10px; font-size: 0.75rem;">
                                        <i class="bx bx-bot"></i> تقرير AI المعتمد
                                    </span>
                                </div>

                                <div class="p-3 p-md-4 rounded-3 border"
                                    style="background:#fcfdfd; border-color:#e8ebef !important; max-height:350px; overflow-y:auto; line-height:1.85; color:#374151; font-size:0.92rem; box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);">
                                    @if(!empty($order->ai_reasoning))
                                        @if(!preg_match('/<[a-z][\s\S]*>/i', $order->ai_reasoning))
                                            {!! \Illuminate\Support\Str::markdown((string) $order->ai_reasoning) !!}
                                        @else
                                            {!! $order->ai_reasoning !!}
                                        @endif
                                    @else
                                        <div class="text-muted text-center py-3">
                                            <i class="bx bx-info-circle fs-4 text-warning mb-1 d-block"></i>
                                            تم إعداد التقييم بناءً على خوارزميات الذكاء الاصطناعي ومؤشرات أسعار السوق.
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="expert_reasoning"
                                    value="{{ $order->ai_reasoning ?? 'تم اعتماد التقييم بناءً على تحليل الذكاء الاصطناعي ومؤشرات السوق.' }}">
                            </div>

                            <button type="submit" id="btnEvaluateSubmit" class="btn btn-evaluate-submit">
                                <i class="bx bx-check-circle" id="btnEvaluateIcon" style="font-size:1.35rem; vertical-align:middle; margin-left:6px;"></i>
                                <span id="btnEvaluateText">اعتماد التقييم</span>
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
                            <span class="badge"
                                style="background:#fdf2d0; color:#d4a017; border-radius:50px; padding:6px 12px;">
                                <i class="bx bxs-circle" style="font-size:8px; vertical-align:middle;"></i>
                                @if(in_array($order->status, ['estimated', 'finished', 'completed'])) تم التقييم @else قيد
                                الانتظار @endif
                            </span>
                            <div class="ex-side-user">
                                <span
                                    style="font-weight:700; font-size:0.85rem;">{{ $order->user->first_name ?? 'العميل' }}</span>
                                <img
                                    src="{{ $order->user->image ? asset('storage/' . $order->user->image) : asset('assets/img/faces/user.jpg') }}">
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
                                <a href="javascript:void(0)" onclick="openAlbumLightbox({{ $loop->index }})"
                                    class="product-img-container d-block" title="عرض الألبوم المكبر">
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
                    {{-- تنبيه الاحتجاج للعميل أو الأدمن --}}
                    @if($order->objection_reason)
                        <div class="alert alert-warning border-0 shadow-sm mb-4" style="background: #fff8e1; border-right: 4px solid #f39c12 !important;">
                            <div class="d-flex align-items-center gap-2 mb-2 font-weight-bold text-dark">
                                <i class="bx bx-error-circle text-warning font-size-18"></i>
                                <span>احتجاج على التقييم من العميل</span>
                                @if($order->re_evaluation_count > 0)
                                    <span class="badge bg-warning text-dark">{{ $order->re_evaluation_count }}x</span>
                                @endif
                            </div>
                            <div class="p-2 rounded bg-white border small text-dark">
                                <strong>سبب الاحتجاج:</strong> {{ $order->objection_reason }}
                                @if($order->objected_at)
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;"><i class="bx bx-time"></i> {{ $order->objected_at->format('Y-m-d H:i') }} ({{ $order->objected_at->diffForHumans() }})</div>
                                @endif
                            </div>

                            @if($order->objection_admin_reply)
                                <div class="mt-2 p-2 rounded small text-dark" style="background: rgba(40, 167, 69, 0.08); border: 1px solid rgba(40, 167, 69, 0.3);">
                                    <div class="font-weight-bold text-success mb-1">
                                        <i class="bx bx-check-double"></i> تم الرد على العميل {{ $order->objection_replied_at ? '(' . $order->objection_replied_at->diffForHumans() . ')' : '' }}:
                                    </div>
                                    <div style="white-space: pre-line; line-height: 1.5;">{{ $order->objection_admin_reply }}</div>
                                </div>
                            @endif

                            @hasanyrole('admin|superadmin')
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-success btn-block font-weight-bold d-flex align-items-center justify-content-center gap-1 shadow-sm" data-toggle="modal" data-target="#replyObjectionModal">
                                        <i class="bx bxl-whatsapp font-size-18"></i>
                                        <span>{{ $order->objection_admin_reply ? 'تعديل أو إرسال رد إضافي للعميل' : 'الرد على العميل برسالة واتساب منظمة' }}</span>
                                    </button>
                                </div>
                            @endhasanyrole
                        </div>
                    @endif

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

@php
    $albumOrderImages = $order->files->where('type', 'image')->values();
    $albumImageUrls = $albumOrderImages->map(function($f) {
        return asset('storage/' . $f->file_path);
    });
@endphp

<!-- =========================================================
     IN-DASHBOARD ALBUM LIGHTBOX MODAL (الصورة 1)
     ========================================================= -->
<div id="expertLightboxModal" class="expert-lightbox-modal" onclick="handleLightboxBackdrop(event)">
    <!-- Top Toolbar -->
    <div class="lb-toolbar">
        <div class="lb-counter">
            <i class="bx bx-images" style="color:#C1953E; font-size:1.25rem;"></i>
            <span id="lbCounterText">صورة 1 من {{ $albumOrderImages->count() }}</span>
        </div>
        <div class="d-flex align-items-center" style="gap:10px;">
            <button type="button" class="lb-btn-action" onclick="toggleZoomImage()" title="تكبير / تصغير">
                <i class="bx bx-zoom-in" id="lbZoomIcon"></i>
            </button>
            <button type="button" class="lb-btn-action" onclick="downloadCurrentImage()" title="تحميل الصورة">
                <i class="bx bx-download"></i>
            </button>
            <button type="button" class="lb-close-btn" onclick="closeAlbumLightbox()" title="إغلاق والعودة للتثمين">
                <i class="bx bx-x"></i>
            </button>
        </div>
    </div>

    <!-- Main Display Stage -->
    <div class="lb-stage">
        @if($albumOrderImages->count() > 1)
            <button type="button" class="lb-nav-btn lb-prev" onclick="prevAlbumImage()" title="السابق (←)">
                <i class="bx bx-chevron-right"></i>
            </button>
        @endif

        <img id="lbMainImage" src="" alt="صورة السلعة" class="lb-main-img" onclick="toggleZoomImage()">

        @if($albumOrderImages->count() > 1)
            <button type="button" class="lb-nav-btn lb-next" onclick="nextAlbumImage()" title="التالي (→)">
                <i class="bx bx-chevron-left"></i>
            </button>
        @endif
    </div>

    <!-- Thumbnails Strip -->
    @if($albumOrderImages->count() > 1)
        <div class="lb-thumbs-strip" id="lbThumbsStrip">
            @foreach($albumOrderImages as $idx => $img)
                <div class="lb-thumb-item {{ $idx === 0 ? 'active' : '' }}" onclick="showAlbumImageByIndex({{ $idx }})" data-index="{{ $idx }}" id="lb-thumb-{{ $idx }}">
                    <img src="{{ asset('storage/' . $img->file_path) }}" alt="مصغرة {{ $idx + 1 }}">
                </div>
            @endforeach
        </div>
    @endif
</div>

@section('js')
    <script>
        // ─── Expert Tab Switching ───────────────────────────────────────
        function switchTab(tabName, clickedEl) {
            document.querySelectorAll('.expert-tab-panel').forEach(function (p) { p.style.display = 'none'; });
            document.querySelectorAll('.step-item').forEach(function (b) {
                b.classList.remove('active');
            });
            var panel = document.getElementById('tab-' + tabName);
            if (panel) { panel.style.display = 'block'; }
            var targetStep = clickedEl || document.getElementById('step-' + tabName);
            if (targetStep) {
                targetStep.classList.add('active');
            }
        }

        // ─── Toggle Specs Details (الانسدال عند الرغبة في المزيد) ───────────────
        function toggleSpecsDetails() {
            var wrapper = document.getElementById('specsDetailsWrapper');
            var chevron = document.getElementById('toggleSpecsChevron');
            var text = document.getElementById('toggleSpecsText');
            if (!wrapper) return;
            if (wrapper.style.display === 'none' || wrapper.style.display === '') {
                wrapper.style.display = 'block';
                if (chevron) chevron.style.transform = 'rotate(180deg)';
                if (text) text.innerText = 'إخفاء التفاصيل';
            } else {
                wrapper.style.display = 'none';
                if (chevron) chevron.style.transform = 'rotate(0deg)';
                if (text) text.innerText = 'انقر لعرض المزيد';
            }
        }

        // ─── In-Dashboard Album Lightbox (الصورة 1) ──────────────────────
        var albumImages = @json($albumImageUrls);
        var currentAlbumIndex = 0;
        var isZoomed = false;

        function openAlbumLightbox(index) {
            if (!albumImages || albumImages.length === 0) return;
            currentAlbumIndex = (index >= 0 && index < albumImages.length) ? index : 0;
            isZoomed = false;
            updateLightboxView();
            var modal = document.getElementById('expertLightboxModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAlbumLightbox() {
            var modal = document.getElementById('expertLightboxModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
            isZoomed = false;
        }

        function handleLightboxBackdrop(e) {
            if (e.target && e.target.id === 'expertLightboxModal') {
                closeAlbumLightbox();
            }
        }

        function showAlbumImageByIndex(index) {
            if (index >= 0 && index < albumImages.length) {
                currentAlbumIndex = index;
                isZoomed = false;
                updateLightboxView();
            }
        }

        function prevAlbumImage() {
            if (albumImages.length <= 1) return;
            currentAlbumIndex = (currentAlbumIndex - 1 + albumImages.length) % albumImages.length;
            isZoomed = false;
            updateLightboxView();
        }

        function nextAlbumImage() {
            if (albumImages.length <= 1) return;
            currentAlbumIndex = (currentAlbumIndex + 1) % albumImages.length;
            isZoomed = false;
            updateLightboxView();
        }

        function updateLightboxView() {
            var mainImg = document.getElementById('lbMainImage');
            var counterText = document.getElementById('lbCounterText');
            var zoomIcon = document.getElementById('lbZoomIcon');
            
            if (mainImg) {
                mainImg.classList.remove('zoomed');
                mainImg.style.opacity = '0.35';
                mainImg.src = albumImages[currentAlbumIndex];
                mainImg.onload = function() {
                    mainImg.style.opacity = '1';
                };
            }
            if (zoomIcon) {
                zoomIcon.className = 'bx bx-zoom-in';
            }
            if (counterText) {
                counterText.textContent = 'صورة ' + (currentAlbumIndex + 1) + ' من ' + albumImages.length;
            }
            document.querySelectorAll('.lb-thumb-item').forEach(function(th, idx) {
                if (idx === currentAlbumIndex) {
                    th.classList.add('active');
                    th.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    th.classList.remove('active');
                }
            });
        }

        function toggleZoomImage() {
            var mainImg = document.getElementById('lbMainImage');
            var zoomIcon = document.getElementById('lbZoomIcon');
            if (!mainImg) return;
            isZoomed = !isZoomed;
            if (isZoomed) {
                mainImg.classList.add('zoomed');
                if (zoomIcon) zoomIcon.className = 'bx bx-zoom-out';
            } else {
                mainImg.classList.remove('zoomed');
                if (zoomIcon) zoomIcon.className = 'bx bx-zoom-in';
            }
        }

        function downloadCurrentImage() {
            if (!albumImages || !albumImages[currentAlbumIndex]) return;
            var a = document.createElement('a');
            a.href = albumImages[currentAlbumIndex];
            a.download = 'order-image-' + (currentAlbumIndex + 1) + '.jpg';
            a.target = '_blank';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            var modal = document.getElementById('expertLightboxModal');
            if (!modal || !modal.classList.contains('active')) return;
            if (e.key === 'Escape') {
                closeAlbumLightbox();
            } else if (e.key === 'ArrowRight') {
                prevAlbumImage();
            } else if (e.key === 'ArrowLeft') {
                nextAlbumImage();
            }
        });

        // Touch swipe navigation for mobile devices
        var touchStartX = 0;
        var touchEndX = 0;
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('expertLightboxModal');
            if (modal) {
                modal.addEventListener('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });
                modal.addEventListener('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    if (touchEndX - touchStartX > 50) {
                        prevAlbumImage();
                    } else if (touchStartX - touchEndX > 50) {
                        nextAlbumImage();
                    }
                }, { passive: true });
            }
        });

        // ─── Interactive Rating Choice (الصورة 2) ────────────────────────
        function selectExpertRating(id, name, el) {
            var hiddenInput = document.getElementById('aiRatingSelectedInput');
            if (hiddenInput) {
                hiddenInput.value = id;
            }
            document.querySelectorAll('.rating-choice-card').forEach(function(card) {
                card.classList.remove('active');
            });
            if (el) {
                el.classList.add('active');
            }
            var feedbackEl = document.getElementById('selectedRatingFeedback');
            if (feedbackEl) {
                feedbackEl.innerHTML = '<i class="bx bx-check-circle" style="color:#009688; font-size:1.15rem; vertical-align:middle;"></i> تم تثبيت واختيار: <strong style="color:#009688;">' + name + '</strong> بنجاح';
                feedbackEl.style.display = 'block';
                feedbackEl.style.background = 'rgba(0,150,136,0.08)';
                feedbackEl.style.borderColor = 'rgba(0,150,136,0.3)';
                feedbackEl.style.color = '#00796b';
            }
        }

        // ─── Evaluation Form Validation & Submitting Animation ───────────
        document.addEventListener('DOMContentLoaded', function () {
            @php
                $isOrderEvaluated = in_array($order->status, ['estimated', 'evaluated', 'finished', 'completed', 'reEstimated']) && $order->status !== 'beingReEstimated';
            @endphp
            var isEvaluated = {{ $isOrderEvaluated ? 'true' : 'false' }};
            if (isEvaluated) {
                var evalStep = document.getElementById('step-evaluate');
                if (evalStep) {
                    switchTab('evaluate', evalStep);
                }
            } else {
                var specsStep = document.getElementById('step-specs');
                if (specsStep) {
                    switchTab('specs', specsStep);
                }
            }

            var evalForm = document.getElementById('expertEvaluationForm');
            if (evalForm) {
                evalForm.addEventListener('submit', function(e) {
                    var hiddenInput = document.getElementById('aiRatingSelectedInput');
                    var ratingCards = document.querySelectorAll('.rating-choice-card');
                    if (hiddenInput && ratingCards.length > 0 && !hiddenInput.value) {
                        e.preventDefault();
                        var feedbackEl = document.getElementById('selectedRatingFeedback');
                        if (feedbackEl) {
                            feedbackEl.innerHTML = '<i class="bx bx-error-circle" style="color:#ef4444; font-size:1.15rem; vertical-align:middle;"></i> يُرجى النقر على أحد الخيارات لتثبيت واختيار رأيك أولاً!';
                            feedbackEl.style.display = 'block';
                            feedbackEl.style.background = 'rgba(239, 68, 68, 0.08)';
                            feedbackEl.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                            feedbackEl.style.color = '#b91c1c';
                            feedbackEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    }

                    var submitBtn = document.getElementById('btnEvaluateSubmit');
                    if (submitBtn) {
                        submitBtn.classList.add('submitting');
                        var icon = document.getElementById('btnEvaluateIcon');
                        var text = document.getElementById('btnEvaluateText');
                        if (icon) icon.className = 'bx bx-loader-alt bx-spin';
                        if (text) text.innerText = 'جاري اعتماد التقييم...';
                    }
                });
            }
        });
    </script>

    @if($order->objection_reason)
        @hasanyrole('admin|superadmin')
        @php
            $rawPhone = $order->user?->phone ?? '';
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            if (str_starts_with($cleanPhone, '05')) {
                $cleanPhone = '966' . substr($cleanPhone, 1);
            }
            $customerName = $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'عميلنا العزيز';
            
            $defaultMsg = "يا هلا بك يا {$customerName} .. بخصوص طلبك رقم #{$order->id} واحتجاجك المسجل لدينا ⚖️\n\n"
                        . "📌 سبب الاحتجاج: {$order->objection_reason}\n\n"
                        . "تمت مراجعة الطلب وتدقيق تفاصيل التثمين والسلعة من قبل الإدارة والخبير المعتمد بعناية فائقة.\n"
                        . "نود إفادتك بأنه: [اكتب تفاصيل الرد والتوضيح هنا]\n\n"
                        . "شاكرين ومقدرين ثقتك الدائمة في منصة ثمن 🤝\n"
                        . "- إدارة تطبيق ثمن";
        @endphp
        <div class="modal fade" id="replyObjectionModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content text-right" dir="rtl">
                    <form action="{{ route('orders.objection.reply', $order->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title font-weight-bold d-flex align-items-center gap-2">
                                <i class="bx bxl-whatsapp" style="font-size: 24px;"></i>
                                <span>الرد على احتجاج العميل عبر واتساب والبريد الإلكتروني</span>
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- بيانات العميل والطلب -->
                            <div class="bg-light p-3 rounded mb-3 border">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <span class="text-muted small d-block">العميل:</span>
                                        <strong class="text-dark">{{ $customerName }}</strong>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <span class="text-muted small d-block">رقم الجوال:</span>
                                        <strong class="text-dark" dir="ltr">{{ $order->user?->phone ?: 'غير متوفر' }}</strong>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <span class="text-muted small d-block">سبب احتجاج العميل:</span>
                                        <span class="badge bg-warning text-dark p-2 text-wrap text-right font-size-13" style="line-height: 1.5;">
                                            {{ $order->objection_reason }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold d-flex justify-content-between align-items-center">
                                    <span>نص الرسالة المنظمة للعميل:</span>
                                    <small class="text-muted">يمكنك تعديل وصياغة الرد كما تشاء</small>
                                </label>
                                <textarea name="reply_message" id="objectionReplyTextarea" class="form-control" rows="8" style="line-height: 1.7; font-size: 0.95rem;" required>{{ $order->objection_admin_reply ?: $defaultMsg }}</textarea>
                            </div>

                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="bx bx-info-circle ml-1"></i> عند الضغط على "إرسال الرد الرسمي"، سيقوم النظام تلقائياً بإرسال الرسالة إلى رقم واتساب العميل وإرسال نسخة عبر البريد الإلكتروني مع حفظ الرد في سجل الطلب.
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <div>
                                @if($cleanPhone)
                                    <a href="#" id="directWhatsAppBtn" target="_blank" class="btn btn-outline-success font-weight-bold">
                                        <i class="bx bxl-whatsapp"></i> فتح محادثة واتساب مباشرة
                                    </a>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-success font-weight-bold">
                                    <i class="bx bx-paper-plane ml-1"></i> إرسال الرد الرسمي للعميل
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var textarea = document.getElementById('objectionReplyTextarea');
                var directBtn = document.getElementById('directWhatsAppBtn');
                var phone = '{{ $cleanPhone }}';

                function updateWaLink() {
                    if (directBtn && textarea && phone) {
                        var text = encodeURIComponent(textarea.value);
                        directBtn.href = 'https://wa.me/' + phone + '?text=' + text;
                    }
                }

                if (textarea && directBtn) {
                    textarea.addEventListener('input', updateWaLink);
                    updateWaLink();
                }
            });
        </script>
        @endhasanyrole
    @endif
@endsection