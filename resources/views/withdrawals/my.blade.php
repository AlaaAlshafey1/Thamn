@extends('layouts.master')
@section('title', 'المحفظة والسحوبات')

@section('css')
<style>
    .wallet-card {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 20px;
        color: white;
        padding: 35px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(30, 60, 114, 0.3);
    }
    .wallet-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .wallet-card::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .wallet-title {
        font-size: 1.1rem;
        font-weight: 300;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    .wallet-balance {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 25px;
        font-family: 'Tajawal', sans-serif;
    }
    .wallet-currency {
        font-size: 1.2rem;
        font-weight: 400;
        opacity: 0.8;
    }
    .transaction-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .transaction-item {
        border-bottom: 1px solid #f8f9fa;
        padding: 20px 0;
        transition: background 0.2s;
    }
    .transaction-item:hover {
        background: #fafbfe;
    }
    .transaction-item:last-child {
        border-bottom: none;
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .icon-pending { background: #fff3cd; color: #856404; }
    .icon-approved { background: #d4edda; color: #155724; }
    .icon-rejected { background: #f8d7da; color: #721c24; }
    
    .status-badge {
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    
    .action-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
    }
    .action-btn:hover {
        background: white;
        color: #1e3c72;
    }
    .table-container {
        max-height: 400px;
        overflow-y: auto;
    }
    .table-container::-webkit-scrollbar {
        width: 6px;
    }
    .table-container::-webkit-scrollbar-thumb {
        background-color: #e2e8f0;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pt-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 10px; font-size: 1.1rem;">
            <i class="bx bx-check-circle fs-20 align-middle ml-2"></i> 
            <strong class="ml-1">نجاح!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="wallet-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="wallet-title"><i class="bx bx-wallet-alt"></i> إجمالي الرصيد القابل للسحب</div>
                    <img src="{{ asset('assets/img/Logo.png') }}" width="50" style="filter: brightness(0) invert(1); opacity:0.8;">
                </div>
                <div class="wallet-balance">
                    {{ number_format(auth()->user()->balance, 2) }} <span class="wallet-currency">SAR</span>
                </div>
                <div class="mt-4">
                    <a href="{{ route('withdrawals.create') }}" class="action-btn">
                        <i class="bx bx-transfer-alt fs-5"></i> تقديم طلب سحب
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card transaction-card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-history text-primary"></i> سجل عمليات السحب</h5>
                        <p class="text-muted small mt-1 mb-0">تتبع حالة طلبات السحب الخاصة بك</p>
                    </div>
                </div>
                <div class="card-body px-4 table-container pt-0">
                    @forelse($requests as $req)
                        <div class="transaction-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box 
                                    @if($req->status == 'pending') icon-pending
                                    @elseif($req->status == 'approved') icon-approved
                                    @else icon-rejected @endif">
                                    @if($req->status == 'pending') <i class="bx bx-time"></i>
                                    @elseif($req->status == 'approved') <i class="bx bx-check"></i>
                                    @else <i class="bx bx-x"></i> @endif
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">طلب سحب ({{ $req->method == 'bank' ? 'حساب بنكي' : ($req->method == 'wallet' ? 'محفظة إلكترونية' : 'أخرى') }})</h6>
                                    <small class="text-muted"><i class="bx bx-calendar-alt"></i> {{ $req->created_at->format('Y-m-d h:i A') }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark fs-5 mb-1 text-ltr">-{{ number_format($req->amount, 2) }} SAR</div>
                                <span class="status-badge 
                                    @if($req->status == 'pending') status-pending
                                    @elseif($req->status == 'approved') status-approved
                                    @else status-rejected @endif">
                                    @if($req->status == 'pending') قيد المراجعة
                                    @elseif($req->status == 'approved') تمت الموافقة
                                    @else مرفوض @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bx bx-receipt text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
                            <h6 class="mt-3 text-muted">لا يوجد سجل سحوبات حتى الآن</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
