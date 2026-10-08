@extends('layouts.expert')

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap');
    :root {
        --ex-gold:       #ff9800;
        --ex-gold-light: #ffb74d;
        --ex-card:       #ffffff;
        --ex-soft:       #f0f2f5;
        --ex-text:       #111111;
        --ex-text-muted: #888888;
        --ex-border:     #eeeeee;
    }
    body,h1,h2,h3,h4,h5,h6,p,a,div,span,button,
    input,select,textarea,table,th,td,.btn,.alert,.badge {
        font-family: 'IBM Plex Sans Arabic', sans-serif !important;
    }
    .expert-dash-wrap { direction: rtl; padding-bottom: 60px; }

    /* Hero */
    .ex-hero {
        background: linear-gradient(135deg,#ffffff 0%,#f9fbfd 100%);
        border-radius: 24px; padding: 36px 40px; margin-bottom: 28px;
        position: relative; overflow: hidden;
        border: 1px solid var(--ex-border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .ex-hero::after {
        content:'⚖️'; position:absolute; left:35px; top:50%;
        transform:translateY(-50%); font-size:7rem; opacity:0.04;
    }
    .hero-badge {
        display:inline-flex; align-items:center; gap:6px;
        background:rgba(255,152,0,0.1); border:1px solid rgba(255,152,0,0.2);
        color:var(--ex-gold); border-radius:50px; padding:5px 16px;
        font-size:0.82rem; font-weight:800; margin-bottom:14px;
    }
    .hero-name { color:var(--ex-text); font-size:1.75rem; font-weight:900; margin-bottom:6px; }
    .hero-name span { color:var(--ex-gold); }
    .hero-subtitle { color:#666; font-size:0.96rem; line-height:1.7; font-weight:600; }
    .hero-date {
        background:var(--ex-soft); border:1px solid #e0e0e0;
        border-radius:12px; padding:10px 20px; color:#555;
        font-size:0.85rem; font-weight:700; display:inline-flex; align-items:center; gap:8px; margin-top:12px;
    }

    /* Stat Cards */
    .ex-stat { border-radius:20px; padding:26px 22px; position:relative; overflow:hidden;
        background: var(--ex-card); border: 1px solid var(--ex-border);
        transition:all 0.3s; box-shadow:0 4px 15px rgba(0,0,0,0.02); }
    .ex-stat:hover{transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.05);}
    .ex-stat-icon { width:54px; height:54px; border-radius:14px;
        display:flex; align-items:center;
        justify-content:center; font-size:1.5rem; margin-bottom:14px; }
    .ex-stat.s-green .ex-stat-icon { background: rgba(0,150,136,0.1); color: #009688; }
    .ex-stat.s-gold .ex-stat-icon { background: rgba(255,152,0,0.1); color: #ff9800; }
    .ex-stat.s-blue .ex-stat-icon { background: rgba(33,150,243,0.1); color: #2196f3; }
    .ex-stat.s-purple .ex-stat-icon { background: rgba(156,39,176,0.1); color: #9c27b0; }
    
    .ex-stat-lbl { color:var(--ex-text-muted); font-size:0.85rem; font-weight:700; margin-bottom:4px; }
    .ex-stat-val { color:var(--ex-text); font-size:2.1rem; font-weight:900; line-height:1; margin-bottom:6px; }
    .ex-stat-note { color:#999; font-size:0.77rem; display:flex; align-items:center; gap:5px; font-weight:600; }

    /* Orders Card */
    .orders-card { background:var(--ex-card); border-radius:20px; border:1px solid var(--ex-border);
        overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.02); }
    .orders-card-hdr {
        padding:22px 28px; display:flex; align-items:center; justify-content:space-between;
        border-bottom:1px solid #f5f5f5;
        background:#fff;
    }
    .orders-card-hdr h5 { color:var(--ex-text); font-weight:900; font-size:1.1rem; margin:0;
        display:flex; align-items:center; gap:10px; }
    .orders-card-hdr h5 i { color:var(--ex-gold); }
    .view-all-btn {
        background:rgba(255,152,0,0.1); border:1px solid rgba(255,152,0,0.2);
        color:var(--ex-gold); border-radius:10px; padding:6px 16px;
        font-size:0.82rem; font-weight:800; text-decoration:none; transition:all .25s;
    }
    .view-all-btn:hover { background:var(--ex-gold); color:#fff; }
    
    .ex-tbl { width:100%; border-collapse:collapse; }
    .ex-tbl thead tr { background:#fbfbfb; border-bottom:1px solid var(--ex-border); }
    .ex-tbl thead th { padding:14px 20px; color:var(--ex-text-muted); font-size:0.8rem;
        font-weight:800; text-align:right; }
    .ex-tbl tbody tr { border-bottom:1px solid #f9f9f9; transition:background .2s; }
    .ex-tbl tbody tr:last-child{border-bottom:none;}
    .ex-tbl tbody tr:hover{background:#fdfdfd;}
    .ex-tbl tbody td { padding:15px 20px; color:var(--ex-text); font-size:0.9rem;
        text-align:right; vertical-align:middle; font-weight:600; }
    .ex-ord-num { font-weight:900; color:var(--ex-text); }
    .ex-urow { display:flex; align-items:center; gap:10px; }
    .ex-avatar { width:34px; height:34px; border-radius:50%; background:#f0f0f0;
        display:flex; align-items:center; justify-content:center;
        color:#888; font-size:1rem; flex-shrink:0; }
    .ex-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px;
        border-radius:50px; font-size:0.75rem; font-weight:800; }
    .b-wait { background:rgba(255,152,0,0.15); color:#ff9800; }
    .b-done { background:rgba(0,150,136,0.15); color:#009688; }
    .b-prog { background:rgba(33,150,243,0.15); color:#2196f3; }
    .b-def  { background:rgba(158,158,158,0.15); color:#9e9e9e; }
    .ex-act-btn {
        display:inline-flex; align-items:center; gap:6px; padding:7px 15px;
        border-radius:10px; background:#111; color:#fff; font-size:0.8rem; font-weight:700; text-decoration:none; transition:all .25s;
    }
    .ex-act-btn:hover { background:#333; color:#fff; }
    .ex-empty { padding:60px 20px; text-align:center; color:var(--ex-text-muted); }
    .ex-empty i { font-size:3rem; opacity:.3; display:block; margin-bottom:12px; }
    .ex-price { font-weight:900; color:#009688; }

    /* Mobile adjustments - App-like look */
    @media (max-width: 768px) {
        .expert-dash-wrap { padding: 10px 5px !important; }
        
        .ex-hero { padding: 22px 18px; text-align: center; border-radius: 20px; }
        .hero-badge { margin: 0 auto 12px; }
        .d-flex.align-items-start { flex-direction: column; align-items: center !important; }
        
        /* Stats horizontal scrolling - App style */
        .an2.row {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 12px;
            margin: 0 -5px 16px -5px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .an2.row::-webkit-scrollbar { display: none; }
        .an2.row > div {
            flex: 0 0 82%;
            scroll-snap-align: center;
            padding: 0 6px;
        }
        
        .ex-stat { padding: 20px 16px; border-radius: 18px; }
        .ex-stat-icon { width: 44px; height: 44px; font-size: 1.2rem; margin-bottom: 10px; }
        .ex-stat-val { font-size: 1.8rem !important; }

        .orders-card { background: transparent; border: none; box-shadow: none; }
        .orders-card-hdr { background: var(--ex-card); border-radius: 18px; border: 1px solid var(--ex-border); margin-bottom: 16px; padding: 14px 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        
        /* Table as modern app cards */
        .ex-tbl, .ex-tbl tbody, .ex-tbl tr, .ex-tbl td { display: block; width: 100%; }
        .ex-tbl thead { display: none; }
        .ex-tbl tbody tr {
            background: var(--ex-card);
            border-radius: 20px;
            border: 1px solid var(--ex-border);
            margin-bottom: 16px;
            padding: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto auto;
            gap: 12px;
            position: relative;
        }
        .ex-tbl tbody td {
            display: flex;
            align-items: center;
            padding: 0;
            border-bottom: none;
            text-align: right;
            justify-content: flex-start;
        }
        .ex-tbl tbody td::before { display: none; } /* Hide old data labels */
        
        /* 1: Order Num */
        .ex-tbl tbody td:nth-child(1) { grid-column: 1 / 3; border-bottom: 1px dashed #f0f0f0; padding-bottom: 12px; font-size: 1.05rem; }
        .ex-tbl tbody td:nth-child(1)::before { content: "طلب "; font-size: 0.9rem; color: #888; margin-left: 4px; display: inline-block; }
        
        /* 2: Client */
        .ex-tbl tbody td:nth-child(2) { grid-column: 1 / 3; }
        .ex-urow { flex-direction: row; }
        .ex-urow span { font-size: 0.95rem; font-weight: 700; }
        
        /* 3: Category */
        .ex-tbl tbody td:nth-child(3) { grid-column: 1 / 2; background: #f9fafb; border-radius: 10px; padding: 8px 10px !important; font-size: 0.8rem; font-weight: 800; justify-content: center; }
        
        /* 4: Price */
        .ex-tbl tbody td:nth-child(4) { grid-column: 2 / 3; background: rgba(0,150,136,0.06); border-radius: 10px; padding: 8px 10px !important; justify-content: center; font-size: 0.85rem; }
        .ex-tbl tbody td:nth-child(4) .ex-price { font-size: 1.05rem; }
        
        /* 5: Status */
        .ex-tbl tbody td:nth-child(5) { grid-column: 1 / 2; justify-content: flex-start; }
        
        /* 6: Action */
        .ex-tbl tbody td:nth-child(6) { grid-column: 2 / 3; }
        .ex-tbl tbody td:nth-child(6) .ex-act-btn { width: 100%; justify-content: center; padding: 10px; border-radius: 12px; }

        /* Quick actions horizontal layout */
        .qa-wrap { padding: 16px; border-radius: 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .qa-btn { flex-direction: column; text-align: center; padding: 14px 8px; margin-bottom: 0; justify-content: center; gap: 8px; border-radius: 14px; position: relative; }
        .qa-btn span { font-size: 0.82rem; }
        .qa-cnt { margin: 0 auto; position: absolute; top: -5px; right: -5px; }
        .qa-icon { width: 42px; height: 42px; margin: 0 auto; font-size: 1.2rem; }
    }

    /* Quick Actions Sidebar */
    .qa-wrap { background: var(--ex-card); border-radius: 20px; border: 1px solid var(--ex-border); padding: 22px; }
    .qa-btn {
        display: flex; align-items: center; gap: 12px; padding: 13px 16px; border-radius: 14px;
        background: #fafafa; border: 1px solid var(--ex-border);
        color: var(--ex-text); text-decoration: none !important; transition: all .3s ease;
        margin-bottom: 10px; font-weight: 700; font-size: 0.91rem;
    }
    .qa-btn:last-child { margin-bottom: 0; }
    .qa-btn:hover { background: rgba(255,152,0,0.08); border-color: rgba(255,152,0,0.25); color: var(--ex-gold); }
    .qa-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0; }
    .qi-gold   { background: rgba(255,152,0,0.12); color: var(--ex-gold); }
    .qi-green  { background: rgba(0,150,136,0.1); color: #009688; }
    .qi-blue   { background: rgba(33,150,243,0.1); color: #2196f3; }
    .qi-gray   { background: rgba(158,158,158,0.1); color: #9e9e9e; }
    .qa-cnt { margin-right: auto; background: rgba(255,152,0,0.12); color: var(--ex-gold); border-radius: 50px; padding: 2px 10px; font-size: 0.74rem; font-weight: 800; }

    /* Performance Ring */
    .perf-wrap { background: var(--ex-card); border-radius: 20px; border: 1px solid var(--ex-border); padding: 26px 22px; }
    .perf-title { color: var(--ex-text-muted); font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 18px; text-align: center; }
    .perf-svg-wrap { position: relative; display: inline-block; }
    .perf-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; }
    .perf-pct { font-size: 1.6rem; font-weight: 900; color: #111; line-height: 1; }
    .perf-unit { font-size: 0.73rem; color: var(--ex-text-muted); font-weight: 700; }
    .perf-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f5f5f5; }
    .perf-row:last-child { border-bottom: none; }
    .perf-lbl { color: var(--ex-text-muted); font-size: 0.85rem; font-weight: 700; }
    .perf-val { color: var(--ex-text); font-size: 0.9rem; font-weight: 800; }

    /* Section title */
    .ex-sec { color: var(--ex-text-muted); font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }
    .ex-sec::after { content: ''; flex: 1; height: 1px; background: #eee; }

    /* notif-box for light theme */
    .notif-box { background: rgba(255,152,0,0.08); border: 1px solid rgba(255,152,0,0.2); border-radius: 14px; padding: 16px 24px; text-align: center; }
    .notif-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--ex-gold); display: inline-block; animation: pulse-dot 1.8s infinite; vertical-align: middle; }
    @keyframes pulse-dot {
        0%,100%{ box-shadow: 0 0 0 0 rgba(255,152,0,0.4); }
        50%{ box-shadow: 0 0 0 8px rgba(255,152,0,0); }
    }

    /* Animations */
    @keyframes fadeSlideUp { from{opacity:0;transform:translateY(22px);} to{opacity:1;transform:translateY(0);} }
    .an1{animation:fadeSlideUp .55s ease both; animation-delay:.05s;}
    .an2{animation:fadeSlideUp .55s ease both; animation-delay:.15s;}
    .an3{animation:fadeSlideUp .55s ease both; animation-delay:.25s;}
    .an4{animation:fadeSlideUp .55s ease both; animation-delay:.35s;}
</style>
@endsection



@section('content')
<div class="expert-dash-wrap px-2 px-md-3 py-1">

    {{-- Hero --}}
    <div class="an1">
        <div class="ex-hero">
            <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:16px;">
                <div>
                    <div class="hero-badge">
                        <i class="bx bx-badge-check"></i> خبير معتمد — منصة ثمن
                    </div>
                    <div class="hero-name">
                        أهلاً، <span>{{ auth()->user()->first_name ?? auth()->user()->name }}</span> 👋
                    </div>
                    <div class="hero-subtitle">
                        لوحة تحكم خبير التثمين المتكاملة<br>
                        راجع الطلبات، قيّم، واعتمد الأسعار باحترافية عالية.
                    </div>
                    <div class="hero-date">
                        <i class="bx bx-calendar"></i>
                        {{ now()->locale('ar')->translatedFormat('l، d F Y') }}
                    </div>
                </div>
                @if(($stats['pending_orders'] ?? 0) > 0)
                <div class="d-none d-md-block">
                    <div class="notif-box">
                        <span class="notif-dot"></span>
                        <div style="color:var(--ex-gold);font-size:0.77rem;font-weight:700;margin-top:6px;">طلبات تحتاجك</div>
                        <div style="color:#111;font-size:2rem;font-weight:900;line-height:1.1;">{{ $stats['pending_orders'] }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row an2" style="margin-bottom:24px;">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="ex-stat s-green">
                <div class="ex-stat-icon"><i class="bx bx-check-double" style="color:#2ecc71;"></i></div>
                <div class="ex-stat-lbl">الطلبات المنجزة</div>
                <div class="ex-stat-val">{{ number_format($stats['orders_completed'] ?? 0) }}</div>
                <div class="ex-stat-note"><i class="bx bx-trending-up"></i> تم تقييمها بنجاح</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="ex-stat s-gold">
                <div class="ex-stat-icon"><i class="bx bx-wallet" style="color:#ff9800;"></i></div>
                <div class="ex-stat-lbl">الرصيد المتاح</div>
                <div class="ex-stat-val" style="font-size:1.65rem;">{{ number_format($stats['balance'] ?? 0, 2) }}</div>
                <div class="ex-stat-note"><i class="bx bx-money"></i> ريال سعودي</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="ex-stat s-blue">
                <div class="ex-stat-icon"><i class="bx bx-time-five" style="color:#2196f3;"></i></div>
                <div class="ex-stat-lbl">الطلبات المتاحة</div>
                <div class="ex-stat-val">{{ number_format($stats['pending_orders'] ?? 0) }}</div>
                <div class="ex-stat-note"><i class="bx bx-bell"></i> تنتظر تقييمك</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="ex-stat s-purple">
                <div class="ex-stat-icon"><i class="bx bx-briefcase" style="color:#c084fc;"></i></div>
                <div class="ex-stat-lbl">إجمالي طلباتي</div>
                <div class="ex-stat-val">{{ number_format($stats['orders_count'] ?? 0) }}</div>
                <div class="ex-stat-note"><i class="bx bx-layer"></i> كل الطلبات المسندة</div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="row an3">

        {{-- Orders Table --}}
        <div class="col-lg-8 mb-4">
            <div class="ex-sec"><i class="bx bx-clipboard" style="color:var(--ex-gold);"></i> آخر الطلبات</div>
            <div class="orders-card">
                <div class="orders-card-hdr">
                    <h5><i class="bx bx-list-ul"></i> طلباتي الأخيرة</h5>
                    <a href="{{ route('orders.index') }}" class="view-all-btn">كل الطلبات ←</a>
                </div>
                <div class="table-responsive">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>الفئة</th>
                                <th>السعر</th>
                                <th style="text-align:center;">الحالة</th>
                                <th style="text-align:center;">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td data-label="رقم الطلب"><span class="ex-ord-num">#{{ $order->id }}</span></td>
                                <td data-label="العميل">
                                    <div class="ex-urow">
                                        <div class="ex-avatar"><i class="bx bx-user"></i></div>
                                        <span style="font-weight:600;">{{ $order->user->name ?? 'مستخدم' }}</span>
                                    </div>
                                </td>
                                <td data-label="الفئة" style="color:var(--ex-text-muted);">
                                    <i class="bx bx-tag" style="color:var(--ex-gold);vertical-align:middle;"></i>
                                    {{ $order->category->name ?? 'غير محدد' }}
                                </td>
                                <td data-label="السعر">
                                    <span class="ex-price">{{ number_format($order->total_price, 0) }}</span>
                                    <span style="color:var(--ex-text-muted);font-size:0.74rem;"> ر.س</span>
                                </td>
                                <td data-label="الحالة" style="text-align:center;">
                                    @php
                                    $sm = [
                                        'pending'        =>['b-wait','قيد الانتظار'],
                                        'orderReceived'  =>['b-prog','تم الاستلام'],
                                        'beingEstimated' =>['b-prog','جاري التقييم'],
                                        'estimated'      =>['b-done','تم التقييم'],
                                        'finished'       =>['b-done','مكتمل'],
                                        'completed'      =>['b-done','مكتمل'],
                                    ];
                                    [$sc,$sl] = $sm[$order->status] ?? ['b-def',$order->status];
                                    @endphp
                                    <span class="ex-badge {{ $sc }}">{{ $sl }}</span>
                                </td>
                                <td data-label="الإجراء" style="text-align:center;">
                                    <a href="{{ route('orders.show', $order->id) }}" class="ex-act-btn">
                                        <i class="bx bx-show"></i> عرض
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="ex-empty">
                                        <i class="bx bx-folder-open"></i>
                                        لا توجد طلبات مسندة إليك بعد
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4 mb-4">

            {{-- Quick Actions --}}
            <div class="ex-sec"><i class="bx bx-zap" style="color:#c1953e;"></i> إجراءات سريعة</div>
            <div class="qa-wrap" style="margin-bottom:24px;">
                <a href="{{ route('orders.index') }}" class="qa-btn">
                    <div class="qa-icon qi-gold"><i class="bx bx-task"></i></div>
                    <span>مراجعة الطلبات الجديدة</span>
                    @if(($stats['pending_orders'] ?? 0) > 0)
                    <span class="qa-cnt">{{ $stats['pending_orders'] }}</span>
                    @endif
                </a>
                <a href="{{ route('orders.index') }}" class="qa-btn">
                    <div class="qa-icon qi-green"><i class="bx bx-badge-check"></i></div>
                    <span>طلبات منجزة</span>
                    <span class="qa-cnt" style="background:rgba(46,204,113,0.15);color:#2ecc71;">{{ $stats['orders_completed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('withdrawals.index') }}" class="qa-btn">
                    <div class="qa-icon qi-blue"><i class="bx bx-money-withdraw"></i></div>
                    <span>طلب سحب رصيد</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="qa-btn">
                    <div class="qa-icon qi-gray"><i class="bx bx-user-circle"></i></div>
                    <span>الملف الشخصي</span>
                </a>
            </div>

            {{-- Performance Ring --}}
            <div class="ex-sec"><i class="bx bx-chart" style="color:var(--ex-gold);"></i> أداء التقييم</div>
            <div class="perf-wrap">
                <div class="perf-title">نسبة الإنجاز الكلية</div>
                @php
                    $total     = ($stats['orders_count'] ?? 0) + ($stats['pending_orders'] ?? 0);
                    $completed = $stats['orders_completed'] ?? 0;
                    $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;
                    $radius    = 54;
                    $circ      = 2 * 3.14159 * $radius;
                    $dash      = round($circ * $pct / 100, 1);
                @endphp
                <div class="text-center mb-3">
                    <div class="perf-svg-wrap">
                        <svg width="140" height="140" viewBox="0 0 140 140">
                            <circle cx="70" cy="70" r="{{ $radius }}" fill="none" stroke="#f0f0f0" stroke-width="12"/>
                            <circle cx="70" cy="70" r="{{ $radius }}" fill="none"
                                stroke="url(#gld)" stroke-width="12"
                                stroke-dasharray="{{ $dash }} {{ $circ }}"
                                stroke-linecap="round"
                                transform="rotate(-90 70 70)"
                                style="transition:stroke-dasharray 1.2s ease;"/>
                            <defs>
                                <linearGradient id="gld" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#ff9800"/>
                                    <stop offset="100%" stop-color="#ffb74d"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="perf-center">
                            <div class="perf-pct">{{ $pct }}%</div>
                            <div class="perf-unit">إنجاز</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="perf-row">
                        <span class="perf-lbl">الطلبات المنجزة</span>
                        <span class="perf-val" style="color:#2ecc71;">{{ $stats['orders_completed'] ?? 0 }}</span>
                    </div>
                    <div class="perf-row">
                        <span class="perf-lbl">الطلبات المعلقة</span>
                        <span class="perf-val" style="color:#fbbf24;">{{ $stats['pending_orders'] ?? 0 }}</span>
                    </div>
                    <div class="perf-row">
                        <span class="perf-lbl">الرصيد المتراكم</span>
                        <span class="perf-val" style="color:var(--ex-gold);">{{ number_format($stats['balance'] ?? 0, 0) }} ر.س</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('js')
@php
    $unassignedOrder = \App\Models\Order::with('category')->whereNull('expert_id')
        ->whereIn('status', ['pending', 'orderReceived', 'beingEstimated', 'paid', 'beingReEstimated'])
        ->where(function ($q) {
            $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
        })
        ->when(auth()->user()->category_id, function ($q) {
            return $q->where(function ($sub) {
                $sub->where('category_id', auth()->user()->category_id)
                    ->orWhereNull('category_id');
            });
        })
        ->orderBy('created_at', 'asc')
        ->first();
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($unassignedOrder)
            Swal.fire({
                title: 'طلب جديد بانتظارك',
                html: `
                    <div style="font-family:'Cairo',sans-serif; text-align:center; direction:rtl;">
                        <p style="font-size: 1.1rem; color: #555; margin-bottom: 20px;">
                            يوجد طلب جديد متاح للتقييم.
                        </p>
                        <div style="background:#f8f9fa; border:1px dashed #009688; border-radius:12px; padding:15px; margin-bottom:20px;">
                            <div style="font-size:1.4rem; font-weight:900; color:#333;">طلب رقم {{ $unassignedOrder->id }}</div>
                            <div style="font-size:1rem; color:#888; font-weight:700; margin-top:5px;">القسم: {{ $unassignedOrder->category->name_ar ?? 'عام' }}</div>
                            <div style="font-size:1rem; color:#888; font-weight:700; margin-top:5px;">تاريخ الطلب: {{ $unassignedOrder->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                `,
                icon: 'info',
                iconColor: '#009688',
                showCancelButton: true,
                confirmButtonColor: '#009688',
                cancelButtonColor: '#e0e0e0',
                confirmButtonText: 'استلام الطلب',
                cancelButtonText: '<span style="color:#555;">لاحقا</span>',
                allowOutsideClick: false,
                backdrop: `rgba(0,0,0,0.6)`
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري الاستلام',
                        text: 'الرجاء الانتظار قليلا',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch('{{ route("orders.assignExpert") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order_id: '{{ $unassignedOrder->id }}' })
                    }).then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الاستلام بنجاح',
                                text: 'جاري تحويلك لصفحة تفاصيل الطلب',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '/orders/{{ $unassignedOrder->id }}';
                            });
                        } else {
                            Swal.fire('عذرا', data.message || 'هذا الطلب تم استلامه من قبل خبير آخر', 'error');
                        }
                    }).catch(err => {
                        console.error(err);
                        Swal.fire('خطأ بالاتصال', 'تأكد من اتصالك بالإنترنت', 'error');
                    });
                }
            });
        @endif
    });
</script>
@endsection
