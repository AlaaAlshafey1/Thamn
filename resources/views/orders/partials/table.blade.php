<div class="table-responsive dataTables-wrapper">
    <table id="{{ $tableId }}" class="table table-hover table-striped text-center align-middle w-100">
        <thead class="bg-light text-center">
            <tr>
                <th class="border-bottom-0">#</th>
                <th class="border-bottom-0">المستخدم</th>
                <th class="border-bottom-0">الحالة</th>
                <th class="border-bottom-0">الدفع</th>
                <th class="border-bottom-0">التاريخ</th>
                <th class="border-bottom-0">تقييم AI</th>
                <th class="border-bottom-0">تقييم الخبير</th>
                <th class="border-bottom-0">السعر النهائي</th>
                <th class="border-bottom-0">العمليات</th>
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
                    
                    <td>
                        @if($order->ai_price)
                            <span class="fw-bold text-primary">{{ number_format($order->ai_price, 0) }}</span>
                            <small class="text-muted">SAR</small>
                        @else
                            <span class="text-muted small">قيد الانتظار</span>
                        @endif
                    </td>

                    <td>
                        @if(auth()->user()->hasRole('expert') && !$order->expert_id)
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <label class="switch mb-0">
                                    <input type="checkbox" class="expert-select" data-order-id="{{ $order->id }}">
                                    <span class="slider round"></span>
                                </label>
                                <small class="text-muted">استلام</small>
                            </div>
                        @elseif($order->expert_price)
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

                    <td>
                        <div class="btn-list">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info-light btn-icon" title="عرض التفاصيل">
                                <i class="bx bx-show fs-18"></i>
                            </a>
                            @can('orders_edit')
                            <a href="#" class="btn btn-sm btn-primary-light btn-icon" title="تعديل">
                                <i class="bx bx-edit fs-18"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
