@extends('layouts.expert')
@section('title', 'كل الطلبات - الخبير')

@section('css')
<style>
    /* Section title */
    .ex-sec { color: var(--ex-muted); font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; display: flex; align-items: center; gap: 10px; margin-top: 30px; }
    .ex-sec::after { content: ''; flex: 1; height: 1px; background: #eee; }

    /* Orders Card */
    .orders-card { background:var(--ex-card); border-radius:20px; border:1px solid var(--ex-border); overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.02); }
    .orders-card-hdr { padding:22px 28px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f5f5f5; background:#fff; }
    .orders-card-hdr h5 { color:var(--ex-text); font-weight:900; font-size:1.1rem; margin:0; display:flex; align-items:center; gap:10px; }
    .orders-card-hdr h5 i { color:var(--ex-gold, #ff9800); }
    
    .ex-tbl { width:100%; border-collapse:collapse; }
    .ex-tbl thead tr { background:#fbfbfb; border-bottom:1px solid var(--ex-border); }
    .ex-tbl thead th { padding:14px 20px; color:var(--ex-muted); font-size:0.8rem; font-weight:800; text-align:right; }
    .ex-tbl tbody tr { border-bottom:1px solid #f9f9f9; transition:background .2s; }
    .ex-tbl tbody tr:last-child{border-bottom:none;}
    .ex-tbl tbody tr:hover{background:#fdfdfd;}
    .ex-tbl tbody td { padding:15px 20px; color:var(--ex-text); font-size:0.9rem; text-align:right; vertical-align:middle; font-weight:600; }
    .ex-ord-num { font-weight:900; color:var(--ex-text); }
    .ex-urow { display:flex; align-items:center; gap:10px; }
    .ex-avatar { width:34px; height:34px; border-radius:50%; background:#f0f0f0; display:flex; align-items:center; justify-content:center; color:#888; font-size:1rem; flex-shrink:0; }
    .ex-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:0.75rem; font-weight:800; }
    .b-wait { background:rgba(255,152,0,0.15); color:#ff9800; }
    .b-done { background:rgba(0,150,136,0.15); color:#009688; }
    .b-prog { background:rgba(33,150,243,0.15); color:#2196f3; }
    .b-def  { background:rgba(158,158,158,0.15); color:#9e9e9e; }
    .ex-act-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 15px; border-radius:10px; background:#111; color:#fff; font-size:0.8rem; font-weight:700; text-decoration:none; transition:all .25s; }
    .ex-act-btn:hover { background:#333; color:#fff; }
    .ex-empty { padding:60px 20px; text-align:center; color:var(--ex-muted); }
    .ex-empty i { font-size:3rem; opacity:.3; display:block; margin-bottom:12px; }
    .ex-price { font-weight:900; color:#009688; }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .orders-card { background: transparent; border: none; box-shadow: none; }
        .orders-card-hdr { background: var(--ex-card); border-radius: 16px; border: 1px solid var(--ex-border); margin-bottom: 16px; padding: 16px 20px; }
        .ex-tbl, .ex-tbl tbody, .ex-tbl tr, .ex-tbl td { display: block; width: 100%; }
        .ex-tbl thead { display: none; }
        .ex-tbl tbody tr { background: var(--ex-card); border-radius: 16px; border: 1px solid var(--ex-border); margin-bottom: 16px; padding: 12px 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .ex-tbl tbody td { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #eee; text-align: left; }
        .ex-tbl tbody td:last-child { border-bottom: none; padding-bottom: 0; }
        .ex-tbl tbody td::before { content: attr(data-label); font-size: 0.8rem; font-weight: 800; color: #888; }
        .ex-urow { flex-direction: row-reverse; }
    }
</style>
@endsection

@section('content')
<div class="px-2 px-md-3 py-1">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div style="font-weight: 900; font-size: 1.8rem; color: #111;">كل الطلبات</div>
        <div style="color: var(--ex-orange); font-weight: 700; font-size: 0.9rem;">
            لوحة الخبير <i class="bx bx-chevron-left" style="vertical-align: middle;"></i> كل الطلبات
        </div>
    </div>

    <!-- Active Orders -->
    <div class="ex-sec"><i class="bx bx-loader-circle" style="color:var(--ex-orange);"></i> الطلبات للتقييم (الحالية)</div>
    <div class="orders-card mb-5">
        <div class="orders-card-hdr">
            <h5><i class="bx bx-list-ul"></i> طلبات بانتظار تقييمك</h5>
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
                    @forelse($activeOrders as $order)
                    <tr>
                        <td data-label="رقم الطلب"><span class="ex-ord-num">#{{ $order->id }}</span></td>
                        <td data-label="العميل">
                            <div class="ex-urow">
                                <div class="ex-avatar"><i class="bx bx-user"></i></div>
                                <span style="font-weight:600;">{{ $order->user->name ?? 'مستخدم' }}</span>
                            </div>
                        </td>
                        <td data-label="الفئة" style="color:var(--ex-muted);">
                            <i class="bx bx-tag" style="color:var(--ex-orange);vertical-align:middle;"></i>
                            {{ $order->category->name ?? 'غير محدد' }}
                        </td>
                        <td data-label="السعر">
                            <span class="ex-price">{{ number_format($order->total_price, 0) }}</span>
                            <span style="color:var(--ex-muted);font-size:0.74rem;"> ر.س</span>
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
                                'beingReEstimated'=>['b-prog','إعادة التقييم'],
                            ];
                            [$sc,$sl] = $sm[$order->status] ?? ['b-def',$order->status];
                            @endphp
                            <span class="ex-badge {{ $sc }}">{{ $sl }}</span>
                        </td>
                        <td data-label="الإجراء" style="text-align:center;">
                            <a href="{{ route('orders.show', $order->id) }}" class="ex-act-btn">
                                <i class="bx bx-show"></i> فتح للتقييم
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="ex-empty">
                                <i class="bx bx-check-shield"></i>
                                لا يوجد طلبات حالية بانتظار تقييمك
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="ex-sec"><i class="bx bx-check-double" style="color:#009688;"></i> الطلبات المنجزة</div>
    <div class="orders-card mb-5">
        <div class="orders-card-hdr">
            <h5><i class="bx bx-history"></i> سجل طلباتك السابقة</h5>
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
                    @forelse($completedOrders as $order)
                    <tr>
                        <td data-label="رقم الطلب"><span class="ex-ord-num">#{{ $order->id }}</span></td>
                        <td data-label="العميل">
                            <div class="ex-urow">
                                <div class="ex-avatar"><i class="bx bx-user"></i></div>
                                <span style="font-weight:600;">{{ $order->user->name ?? 'مستخدم' }}</span>
                            </div>
                        </td>
                        <td data-label="الفئة" style="color:var(--ex-muted);">
                            <i class="bx bx-tag" style="color:var(--ex-orange);vertical-align:middle;"></i>
                            {{ $order->category->name ?? 'غير محدد' }}
                        </td>
                        <td data-label="السعر">
                            <span class="ex-price">{{ number_format($order->total_price, 0) }}</span>
                            <span style="color:var(--ex-muted);font-size:0.74rem;"> ر.س</span>
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
                            <a href="{{ route('orders.show', $order->id) }}" class="ex-act-btn" style="background:#f5f5f5;color:#111;border:1px solid #ddd;">
                                <i class="bx bx-show"></i> التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="ex-empty">
                                <i class="bx bx-folder-open"></i>
                                لم تنجز أي طلبات بعد
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
