@extends('layouts.master')

@section('title', 'تقييمات ورضا العملاء (CSAT)')

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">خدمة العملاء</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقييمات ورضا العملاء (CSAT)</span>
        </div>
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><i class="bx bx-check-circle"></i></strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- ─── KPI Cards ─── --}}
<div class="row row-sm">
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-primary-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <div>
                    <h6 class="mb-3 tx-12 text-white font-weight-bold">معدل رضا العملاء (CSAT)</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="d-flex">
                        <div>
                            <h4 class="tx-22 font-weight-bold mb-1 text-white">{{ $csatPercent }}%</h4>
                            <p class="mb-0 tx-12 text-white op-7">نسبة العملاء الراضين (4 و 5 نجوم)</p>
                        </div>
                        <span class="float-right my-auto mr-auto">
                            <i class="bx bx-smile text-white font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-warning-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <div>
                    <h6 class="mb-3 tx-12 text-white font-weight-bold">متوسط التقييم العام</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="d-flex">
                        <div>
                            <h4 class="tx-22 font-weight-bold mb-1 text-white">{{ $averageRating }} / 5</h4>
                            <p class="mb-0 tx-12 text-white op-7">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bx {{ $i <= round($averageRating) ? 'bxs-star' : 'bx-star' }} text-white"></i>
                                @endfor
                            </p>
                        </div>
                        <span class="float-right my-auto mr-auto">
                            <i class="bx bx-star text-white font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-success-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <div>
                    <h6 class="mb-3 tx-12 text-white font-weight-bold">إجمالي التقييمات</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="d-flex">
                        <div>
                            <h4 class="tx-22 font-weight-bold mb-1 text-white">{{ number_format($totalRatings) }}</h4>
                            <p class="mb-0 tx-12 text-white op-7">{{ $satisfiedCount }} راضٍ | {{ $neutralCount }} محايد</p>
                        </div>
                        <span class="float-right my-auto mr-auto">
                            <i class="bx bx-message-check text-white font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card {{ $unresolvedCount > 0 ? 'bg-danger-gradient' : 'bg-secondary' }}">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <div>
                    <h6 class="mb-3 tx-12 text-white font-weight-bold">تنبيهات عدم الرضا (بحاجة لمتابعة)</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="d-flex">
                        <div>
                            <h4 class="tx-22 font-weight-bold mb-1 text-white">{{ $unresolvedCount }}</h4>
                            <p class="mb-0 tx-12 text-white op-7">إجمالي السلبي: {{ $negativeCount }} تقييم</p>
                        </div>
                        <span class="float-right my-auto mr-auto">
                            <i class="bx bx-error-alt text-white font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm">
    {{-- ─── توزيع التقييمات ─── --}}
    <div class="col-lg-5 col-md-12">
        <div class="card custom-card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 font-weight-bold">
                    <i class="bx bx-bar-chart-alt-2 text-warning"></i> توزيع تقييمات العملاء
                </h5>
            </div>
            <div class="card-body">
                @php
                    $emojiMap = [
                        5 => ['icon' => '😍', 'label' => 'ممتازة', 'color' => '#10b981'],
                        4 => ['icon' => '🙂', 'label' => 'جيدة', 'color' => '#3b82f6'],
                        3 => ['icon' => '😐', 'label' => 'متوسطة', 'color' => '#f59e0b'],
                        2 => ['icon' => '🙁', 'label' => 'سيئة', 'color' => '#ef4444'],
                        1 => ['icon' => '😡', 'label' => 'سيئة جداً', 'color' => '#dc2626'],
                    ];
                @endphp

                @foreach($distribution as $score => $data)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold small">
                                <span class="font-size-18">{{ $emojiMap[$score]['icon'] }}</span>
                                {{ $score }} نجوم ({{ $emojiMap[$score]['label'] }})
                            </span>
                            <span class="text-muted small">
                                <strong>{{ $data['count'] }}</strong> ({{ $data['percent'] }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 6px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ $data['percent'] }}%; background-color: {{ $emojiMap[$score]['color'] }};"
                                 aria-valuenow="{{ $data['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ─── تقييم الخبراء من العملاء ─── --}}
        @if($expertsStats->isNotEmpty())
            <div class="card custom-card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="bx bx-award text-warning"></i> تقييم رضا العملاء عن الخبراء
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0 text-center">
                            <thead>
                                <tr>
                                    <th>الخبير</th>
                                    <th>عدد التقييمات</th>
                                    <th>متوسط التقييم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expertsStats as $stat)
                                    <tr>
                                        <td class="font-weight-bold text-dark text-right pr-3">
                                            {{ $stat->expert->first_name . ' ' . $stat->expert->last_name }}
                                            <span class="badge bg-light text-dark border">{{ $stat->expert->membership_number ?? $stat->expert->id }}</span>
                                        </td>
                                        <td>{{ $stat->total_reviews }}</td>
                                        <td>
                                            <span class="badge {{ $stat->avg_rating >= 4 ? 'bg-success text-white' : ($stat->avg_rating >= 3 ? 'bg-warning text-dark' : 'bg-danger text-white') }} px-2 py-1 font-weight-bold">
                                                ⭐ {{ round($stat->avg_rating, 1) }} / 5
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ─── جدول سجل التقييمات والمتابعات ─── --}}
    <div class="col-lg-7 col-md-12">
        <div class="card custom-card">
            <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 font-weight-bold">
                    <i class="bx bx-list-check text-warning"></i> سجل تقييمات العملاء
                </h5>

                {{-- الفلاتر السريعة --}}
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.ratings.index', ['filter' => 'all']) }}" class="btn btn-outline-primary {{ $filter === 'all' ? 'active' : '' }}">الكل</a>
                    <a href="{{ route('admin.ratings.index', ['filter' => 'unresolved']) }}" class="btn btn-outline-danger {{ $filter === 'unresolved' ? 'active' : '' }}">
                        بحاجة لمتابعة
                        @if($unresolvedCount > 0)
                            <span class="badge bg-danger text-white ml-1">{{ $unresolvedCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.ratings.index', ['filter' => 'positive']) }}" class="btn btn-outline-success {{ $filter === 'positive' ? 'active' : '' }}">راضٍ (4-5)</a>
                    <a href="{{ route('admin.ratings.index', ['filter' => 'neutral']) }}" class="btn btn-outline-warning {{ $filter === 'neutral' ? 'active' : '' }}">محايد (3)</a>
                    <a href="{{ route('admin.ratings.index', ['filter' => 'negative']) }}" class="btn btn-outline-danger {{ $filter === 'negative' ? 'active' : '' }}">سيئ (1-2)</a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle" style="font-size: 0.9rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>الطلب</th>
                                <th>العميل</th>
                                <th>الخبير</th>
                                <th>التقييم</th>
                                <th>القناة</th>
                                <th>الملاحظات / المتابعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ratings as $item)
                                <tr style="{{ $item->isDissatisfied() && !$item->is_resolved ? 'background: rgba(239, 68, 68, 0.05);' : '' }}">
                                    <td>
                                        <a href="{{ route('orders.show', $item->order_id) }}" class="font-weight-bold text-primary" target="_blank">
                                            {{ $item->order_id }}
                                        </a>
                                        <div class="small text-muted">{{ $item->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $item->user->first_name . ' ' . $item->user->last_name }}</div>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->user->phone) }}" target="_blank" class="text-success text-ltr small">
                                            <i class="bx bxl-whatsapp"></i> {{ $item->user->phone ?? '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($item->expert)
                                            <div class="text-dark small font-weight-bold">{{ $item->expert->first_name . ' ' . $item->expert->last_name }}</div>
                                            <span class="badge bg-light text-muted border">{{ $item->expert->membership_number ?? $item->expert->id }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-size-20">{{ $item->emoji }}</div>
                                        <span class="{{ $item->badge_class }} px-2 py-1 small">
                                            {{ $item->rating }} / 5 - {{ $item->label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->channel === 'whatsapp_chat')
                                            <span class="badge bg-success-transparent text-success" title="رد مباشر بالواتساب">
                                                <i class="bx bxl-whatsapp"></i> واتساب شات
                                            </span>
                                        @else
                                            <span class="badge bg-info-transparent text-info" title="الرابط السريع">
                                                <i class="bx bx-link"></i> رابط سريع
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right" style="max-width: 250px;">
                                        @if($item->feedback)
                                            <div class="p-2 rounded bg-light small mb-2 border text-dark" style="white-space: pre-line;">
                                                <i class="bx bxs-quote-alt-right text-muted"></i> {{ $item->feedback }}
                                            </div>
                                        @endif

                                        {{-- معالجة حالات عدم الرضا (1 أو 2) --}}
                                        @if($item->isDissatisfied())
                                            @if($item->is_resolved)
                                                <div class="badge bg-success-transparent text-success p-1 d-block mb-1">
                                                    <i class="bx bx-check-circle"></i> تم التواصل والحل
                                                </div>
                                                <small class="text-muted d-block">{{ $item->admin_notes }}</small>
                                            @else
                                                <button type="button" class="btn btn-sm btn-danger btn-block font-weight-bold py-1"
                                                        data-toggle="modal" data-target="#resolveModal{{ $item->id }}">
                                                    <i class="bx bx-phone-call"></i> تواصل وحل المشكلة
                                                </button>

                                                {{-- Modal تسجيل الحل --}}
                                                <div class="modal fade" id="resolveModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title font-weight-bold text-dark">
                                                                    متابعة عدم رضا العميل للطلب {{ $item->order_id }}
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('admin.ratings.resolve', $item->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body text-right">
                                                                    <div class="alert alert-light border mb-3">
                                                                        <strong>العميل:</strong> {{ $item->user->first_name . ' ' . $item->user->last_name }} ({{ $item->user->phone }})<br>
                                                                        <strong>التقييم:</strong> {{ $item->emoji }} {{ $item->rating }}/5 ({{ $item->label }})<br>
                                                                        @if($item->feedback)
                                                                            <strong>الملاحظة:</strong> {{ $item->feedback }}
                                                                        @endif
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label font-weight-bold">ملاحظات خدمة العملاء والإجراء المتخذ:</label>
                                                                        <textarea name="admin_notes" class="form-control" rows="4" required
                                                                                  placeholder="اكتب تفاصيل التواصل مع العميل، سبب الشكوى، وما تم الاتفاق عليه لحل الإشكالية..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="bx bx-check"></i> حفظ كـ "تم حل الإشكالية"
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-success small font-weight-bold">
                                                <i class="bx bx-check-double"></i> عميل راضٍ
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-muted">
                                        <i class="bx bx-folder-open font-size-24 d-block mb-2"></i>
                                        لا توجد تقييمات مطابقة للبحث أو الفلتر المختار
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $ratings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
