@extends('layouts.master')
@section('title', 'إدارة الطلبات')

@section('css')
    <!-- DataTables CSS -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .dt-buttons .btn {
            background-color: #c1953e !important;
            border: none !important;
            color: #fff !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
        }

        .dt-buttons .btn:hover {
            background-color: #a67f31 !important;
        }

        #colvisList {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        #colvisList .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        #colvisList .form-check-input {
            margin: 0;
            transform: scale(1.1);
            cursor: pointer;
        }

        #colvisList .form-check-label {
            margin: 0;
            line-height: 1;
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #4CAF50;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .table td {
            vertical-align: middle !important;
        }

        .badge {
            font-size: 12px;
            padding: 6px 10px;
        }

        .table-hover tbody tr:hover {
            background-color: #fff8e1;
        }

        .dataTables-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        #ordersTable {
            min-width: 1800px;
            /* مهم عشان الأعمدة */
            white-space: nowrap;
        }

        #ordersTable td,
        #ordersTable th {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('page-header')
    <div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3"
        style="direction: rtl;">
        <div class="d-flex flex-column">
            <h4 class="content-title mb-1 fw-bold text-primary">إدارة الطلبات</h4>
            @if(auth()->user()->hasRole('expert'))
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">عرض الطلبات الخاصة بقسم:</small>
                    @if(auth()->user()->category)
                        <span class="badge bg-gold-transparent text-gold border border-gold-light px-3 py-2" style="background-color: rgba(193, 149, 62, 0.1); color: #c1953e; border: 1px solid rgba(193, 149, 62, 0.3);">
                            <i class="fas fa-tags ml-1"></i> {{ auth()->user()->category->name_ar ?? auth()->user()->category->name_en }}
                        </span>
                    @else
                        <span class="badge bg-secondary-transparent text-secondary px-3 py-2">لم يتم تحديد قسم</span>
                    @endif
                </div>
            @else
                <small class="text-muted">عرض جميع الطلبات والتحكم بها</small>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">قائمة الطلبات</h5>
            <small class="text-muted">عرض جميع الطلبات المسجلة</small>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive dataTables-wrapper">
                <table id="ordersTable" class="table table-hover table-striped text-center align-middle">
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
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="ml-2">
                                            <div class="font-weight-bold">{{ $order->user->first_name ?? '-' }}</div>
                                            <small class="text-muted text-ltr">{{ $order->user->phone ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning-transparent text-warning',
                                            'orderReceived' => 'bg-info-transparent text-info',
                                            'beingEstimated' => 'bg-primary-transparent text-primary',
                                            'estimated' => 'bg-success-transparent text-success',
                                            'cancelled' => 'bg-danger-transparent text-danger',
                                        ];
                                        $statusClass = $statusClasses[$order->status] ?? 'bg-secondary-transparent text-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} py-2 px-3">{{ $order->status }}</span>
                                </td>
                                <td>
                                    @if($order->status !== 'pending' && $order->status !== 'failed')
                                        <span class="text-success"><i class="fa fa-check-circle ml-1"></i> مدفوع</span>
                                    @else
                                        <span class="text-danger"><i class="fa fa-times-circle ml-1"></i> لم يتم</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                
                                {{-- تقييم AI --}}
                                <td>
                                    @if($order->ai_price)
                                        <span class="font-weight-bold text-primary">{{ number_format($order->ai_price, 0) }}</span>
                                        <small class="text-muted">SAR</small>
                                    @else
                                        <span class="text-muted small">قيد الانتظار</span>
                                    @endif
                                </td>

                                {{-- تقييم الخبير --}}
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
                                        <span class="font-weight-bold text-warning">{{ number_format($order->expert_price, 0) }}</span>
                                        <small class="text-muted">SAR</small>
                                    @else
                                        <span class="badge bg-light text-muted">لم يتم</span>
                                    @endif
                                </td>

                                {{-- السعر النهائي --}}
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
        </div>
    </div>
@endsection

@section('js')
    <!-- DataTables Scripts -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function () {
            let table = $('#ordersTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
                pageLength: 10,
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
                buttons: [
                    { extend: 'copy', text: '📋 نسخ', className: 'btn-sm mx-1' },
                    { extend: 'excel', text: '📊 Excel', className: 'btn-sm mx-1' },
                    { extend: 'pdf', text: '📄 PDF', className: 'btn-sm mx-1' },
                    { extend: 'print', text: '🖨️ طباعة', className: 'btn-sm mx-1' }
                ]
            });

            // تنسيق الأزرار
            $('.dt-buttons').addClass('d-flex flex-wrap gap-2 align-items-center');
            $('.dt-buttons .btn').addClass('btn-primary').css({
                'background-color': '#c1953e',
                'border-color': '#c1953e',
                'color': '#fff'
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.expert-select').change(function () {
                let orderId = $(this).data('order-id');
                let checked = $(this).is(':checked');

                if (checked) {
                    $.ajax({
                        url: '/orders/assign-expert', // route جديد للـ controller
                        method: 'POST',
                        data: {
                            order_id: orderId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status) {
                                location.reload();
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        },
                        error: function (err) {
                            alert('حدث خطأ أثناء التعيين، قد يكون الطلب تم استلامه بالفعل.');
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>


@endsection