@include('partials.modern-table-css')
<style>
    .btn-receive {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
        box-shadow: 0 4px 6px rgba(40,167,69,0.2);
        transition: all 0.3s;
        border-radius: 20px;
        padding: 6px 16px;
    }
    .btn-receive:hover {
        background: linear-gradient(135deg, #218838, #1ba87e);
        box-shadow: 0 6px 8px rgba(40,167,69,0.3);
        transform: translateY(-1px);
        color: white;
    }
</style>

<div class="table-responsive dataTables-wrapper">
    <table id="{{ $tableId }}" class="table modern-table text-center align-middle w-100">
        <thead class="text-center">
            <tr>
                <th>#</th>
                <th>المستخدم</th>
                <th>نوع التثمين</th>
                <th>الحالة</th>
                <th>الدفع</th>
                <th>التاريخ</th>
                @if(!auth()->user()->hasRole('expert'))
                    <th>تقييم AI</th>
                    <th>تقييم الخبير</th>
                    <th>السعر النهائي</th>
                @else
                    <th>تقييمك</th>
                @endif
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $key => $order)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="fw-bold">{{ $order->user->first_name ?? '-' }}</span>
                            <small class="text-muted">{{ $order->user->phone ?? '' }}</small>
                        </div>
                    </td>
                    <td>
                        @php
                            $evalType = $order->evaluation_type;
                            $typeLabels = [
                                'ai' => 'bg-info-transparent text-info border border-info',
                                'expert' => 'bg-warning-transparent text-warning border border-warning',
                                'best' => 'bg-primary-transparent text-primary border border-primary',
                            ];
                            $typeNames = [
                                'ai' => 'ذكاء اصطناعي',
                                'expert' => 'تقييم خبراء',
                                'best' => 'تثمين احترافي',
                            ];
                            $typeClass = $typeLabels[$evalType] ?? 'bg-secondary-transparent text-secondary';
                            $typeName = $typeNames[$evalType] ?? 'غير محدد';
                        @endphp
                        <span class="badge {{ $typeClass }} py-2 px-3">{{ $typeName }}</span>
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-warning-transparent text-warning',
                                'orderReceived' => 'bg-info-transparent text-info',
                                'beingEstimated' => 'bg-primary-transparent text-primary',
                                'evaluated' => 'bg-success-transparent text-success',
                                'finished' => 'bg-success-transparent text-success',
                                'cancelled' => 'bg-danger-transparent text-danger',
                            ];
                            $statusClass = $statusClasses[$order->status] ?? 'bg-secondary-transparent text-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }} py-2 px-3">{{ $order->status }}</span>
                    </td>
                    <td>
                        @if($order->status !== 'pending' && $order->status !== 'failed')
                            <span class="text-success small fw-bold"><i class="fa fa-check-circle ml-1"></i> مدفوع</span>
                        @else
                            <span class="text-danger small fw-bold"><i class="fa fa-times-circle ml-1"></i> لم يتم</span>
                        @endif
                    </td>
                    <td class="small">{{ $order->created_at->format('Y-m-d') }}</td>
                    
                    @if(!auth()->user()->hasRole('expert'))
                        <td>
                            @if($order->ai_price)
                                <span class="fw-bold text-primary">{{ number_format($order->ai_price, 0) }}</span>
                                <small class="text-muted">SAR</small>
                            @else
                                <span class="text-muted small">قيد الانتظار</span>
                            @endif
                        </td>

                        <td>
                            @if($order->expert_price)
                                <span class="fw-bold text-warning">{{ number_format($order->expert_price, 0) }}</span>
                                <small class="text-muted">SAR</small>
                            @else
                                <span class="badge bg-light text-muted">لم يتم</span>
                            @endif
                        </td>

                        <td>
                            <strong class="text-dark">{{ number_format($order->total_price, 0) }}</strong>
                            <small class="text-muted">SAR</small>
                        </td>
                    @else
                        <td>
                            @if(!$order->expert_id)
                                <span class="badge bg-warning-transparent text-warning px-2 py-1"><i class="bx bx-time-five ml-1"></i> بانتظار الاستلام</span>
                            @elseif($order->expert_price)
                                <span class="fw-bold text-warning">{{ number_format($order->expert_price, 0) }}</span>
                                <small class="text-muted">SAR</small>
                            @else
                                <span class="badge bg-light text-muted">بانتظار تقييمك</span>
                            @endif
                        </td>
                    @endif

                    <td>
                        <div class="btn-list d-flex justify-content-center gap-1">
                            @if(auth()->user()->hasRole('expert'))
                                @if(!$order->expert_id)
                                    <button type="button" class="btn btn-receive fw-bold expert-receive-btn" data-order-id="{{ $order->id }}">
                                        <i class="bx bx-check-double fs-18 ml-1 align-middle"></i> استلام الطلب
                                    </button>
                                @elseif($order->expert_id == auth()->id())
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info-light btn-icon" title="عرض التفاصيل وتقييم">
                                        <i class="bx bx-show fs-18"></i>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info-light btn-icon" title="عرض التفاصيل">
                                    <i class="bx bx-show fs-18"></i>
                                </a>
                                @can('orders_edit')
                                <a href="#" class="btn btn-sm btn-primary-light btn-icon" title="تعديل">
                                    <i class="bx bx-edit fs-18"></i>
                                </a>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-light text-center">
            <tr>
                <th>#</th>
                <th>المستخدم</th>
                <th>نوع التثمين</th>
                <th>الحالة</th>
                <th>الدفع</th>
                <th>التاريخ</th>
                @if(!auth()->user()->hasRole('expert'))
                    <th>تقييم AI</th>
                    <th>تقييم الخبير</th>
                    <th>السعر النهائي</th>
                @else
                    <th>تقييمك</th>
                @endif
                <th>العمليات</th>
            </tr>
        </tfoot>
    </table>
</div>
