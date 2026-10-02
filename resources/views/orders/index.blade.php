@extends('layouts.master')
@section('title', 'إدارة الطلبات')

@section('css')
    <style>
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

        #ordersTable, #activeOrdersTable, #completedOrdersTable {
            min-width: 1800px;
            white-space: nowrap;
        }

        #ordersTable td, #ordersTable th,
        #activeOrdersTable td, #activeOrdersTable th,
        #completedOrdersTable td, #completedOrdersTable th {
            vertical-align: middle !important;
        }

        /* Switch styling */
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc; transition: .4s;
        }
        .slider:before {
            position: absolute; content: "";
            height: 16px; width: 16px;
            left: 2px; bottom: 2px;
            background-color: white; transition: .4s;
        }
        input:checked + .slider { background-color: #c1953e; }
        input:checked + .slider:before { transform: translateX(20px); }
        .slider.round { border-radius: 20px; }
        .slider.round:before { border-radius: 50%; }
    </style>
@endsection

@section('page-header')
    <div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3"
        style="direction: rtl;">
        <div class="d-flex flex-column">
            <h4 class="content-title mb-1 fw-bold text-primary">إدارة جميع الطلبات</h4>
            @if(auth()->user()->hasRole('expert'))
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">عرض الطلبات المتاحة لتخصصك:</small>
                    @if(auth()->user()->category)
                        <span class="badge bg-gold-transparent text-gold border border-gold-light px-3 py-2" style="background-color: rgba(193, 149, 62, 0.1); color: #c1953e; border: 1px solid rgba(193, 149, 62, 0.3);">
                            <i class="fas fa-tags ml-1"></i> {{ auth()->user()->category->name_ar ?? auth()->user()->category->name_en }}
                        </span>
                    @else
                        <span class="badge bg-secondary-transparent text-secondary px-3 py-2">لم يتم تحديد تخصص</span>
                    @endif
                </div>
            @else
                <small class="text-muted">نظرة عامة على الطلبات والتحاليل</small>
            @endif
        </div>
    </div>
@endsection

@section('content')
    @if(auth()->user()->hasRole('expert'))
    <!-- Tabs for Experts -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white pb-0 border-0">
            <ul class="nav nav-tabs card-header-tabs" id="expertTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request('tab') == 'completed' ? '' : 'active' }} fw-bold px-4 py-3" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-orders" type="button" role="tab">
                        <i class="bx bx-list-ul ml-1"></i> الطلبات الحالية 
                        <span class="badge bg-primary ms-1">{{ $activeOrders->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request('tab') == 'completed' ? 'active' : 'text-muted' }} fw-bold px-4 py-3" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-orders" type="button" role="tab">
                        <i class="bx bx-history ml-1"></i> الطلبات السابقة
                        <span class="badge bg-success ms-1">{{ $completedOrders->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content p-4" id="expertTabsContent">
                <div class="tab-pane fade {{ request('tab') == 'completed' ? '' : 'show active' }}" id="active-orders" role="tabpanel">
                    @include('orders.partials.table', ['orders' => $activeOrders, 'tableId' => 'activeOrdersTable'])
                </div>
                <div class="tab-pane fade {{ request('tab') == 'completed' ? 'show active' : '' }}" id="completed-orders" role="tabpanel">
                    @include('orders.partials.table', ['orders' => $completedOrders, 'tableId' => 'completedOrdersTable'])
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Standard View for Admins -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 fw-bold">قائمة جميع الطلبات</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
            @endif
            @include('orders.partials.table', ['orders' => $orders, 'tableId' => 'ordersTable'])
        </div>
    </div>
    @endif

    @if(auth()->user()->hasRole('expert') && isset($activeOrders) && $activeOrders->whereNull('expert_id')->count() > 0)
        @php
            $firstNewOrder = $activeOrders->whereNull('expert_id')->first();
        @endphp
        <!-- New Order Modal -->
        <div class="modal fade" id="newOrderModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 pt-0">
                        <div class="mb-4">
                            <div style="width: 80px; height: 80px; background: rgba(193, 149, 62, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                <i class="fas fa-bell fa-3x" style="color: #c1953e; animation: ring 2s infinite;"></i>
                            </div>
                            <h4 class="fw-bold mb-2" style="color: #2c3e50;">طلب جديد متاح!</h4>
                            <p class="text-muted">هناك طلب جديد في تخصصك ينتظر التقييم</p>
                        </div>
                        <div class="bg-light p-3 mb-4 text-start" style="border-radius: 15px; border: 1px solid #f0f0f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">رقم الطلب:</span>
                                <span class="fw-bold text-dark">#{{ $firstNewOrder->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">القسم:</span>
                                <span class="fw-bold text-dark"><i class="fas fa-tag text-warning mr-1 ml-1"></i> {{ $firstNewOrder->category->name_ar ?? $firstNewOrder->category->name_en ?? 'غير محدد' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">وقت الطلب:</span>
                                <span class="fw-bold text-dark"><i class="fas fa-clock text-info mr-1 ml-1"></i> {{ $firstNewOrder->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning btn-lg w-100 fw-bold expert-receive-btn mb-2" data-order-id="{{ $firstNewOrder->id }}" style="border-radius: 12px; background: linear-gradient(135deg, #c1953e, #b08637); border: none; color: white;">
                            <i class="fas fa-hand-holding-usd ml-1"></i> استلام الطلب الآن
                        </button>
                        <button type="button" class="btn btn-light w-100 fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">تخطي حالياً</button>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @keyframes ring {
                0% { transform: rotate(0); }
                10% { transform: rotate(15deg); }
                20% { transform: rotate(-10deg); }
                30% { transform: rotate(5deg); }
                40% { transform: rotate(-5deg); }
                50% { transform: rotate(0); }
                100% { transform: rotate(0); }
            }
        </style>
    @endif
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            // Setup - add a text input to each footer cell
            $('.table tfoot th').each(function () {
                var title = $(this).text();
                if(title !== 'العمليات' && title !== '#') {
                    $(this).html('<input type="text" class="form-control form-control-sm" placeholder="بحث ' + title + '" />');
                } else {
                    $(this).html('');
                }
            });

            var table = $('.table').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json' },
                pageLength: 10,
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"btn-left"B><"search-box"f>>rtip',
                buttons: [
                    { extend: 'copy', text: 'نسخ', className: 'btn-sm mx-1' },
                    { extend: 'excel', text: 'Excel', className: 'btn-sm mx-1' },
                    { extend: 'pdf', text: 'PDF', className: 'btn-sm mx-1' },
                    { extend: 'print', text: 'طباعة', className: 'btn-sm mx-1' }
                ],
                initComplete: function () {
                    // Apply the search
                    this.api().columns().every(function () {
                        var that = this;
                        $('input', this.footer()).on('keyup change clear', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            // Show new order modal if exists
            if ($('#newOrderModal').length > 0) {
                if (typeof bootstrap !== 'undefined') {
                    var myModal = new bootstrap.Modal(document.getElementById('newOrderModal'));
                    myModal.show();
                } else {
                    $('#newOrderModal').modal('show');
                }
            }

            // Expert Assignment logic
            $(document).on('click', '.expert-receive-btn', function () {
                let orderId = $(this).data('order-id');

                if (confirm('هل أنت متأكد أنك تريد استلام هذا الطلب؟')) {
                    $.ajax({
                        url: '{{ route("orders.assignExpert") }}',
                        method: 'POST',
                        data: {
                            order_id: orderId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status) {
                                window.location.href = '{{ url("orders") }}/' + orderId;
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        },
                        error: function (err) {
                            alert('حدث خطأ أثناء الاتصال بالخادم.');
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endsection
