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
                <small class="text-muted">عرض جميع الطلبات والتحك�    @if(auth()->user()->hasRole('expert'))
    <!-- Tabs for Experts -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white pb-0 border-0">
            <ul class="nav nav-tabs card-header-tabs" id="expertTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 py-3" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-orders" type="button" role="tab">
                        <i class="bx bx-list-ul ml-1"></i> الطلبات الحالية 
                        <span class="badge bg-primary ms-1">{{ $activeOrders->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-3 text-muted" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-orders" type="button" role="tab">
                        <i class="bx bx-history ml-1"></i> الطلبات السابقة
                        <span class="badge bg-success ms-1">{{ $completedOrders->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content p-4" id="expertTabsContent">
                <!-- Active Orders Tab -->
                <div class="tab-pane fade show active" id="active-orders" role="tabpanel">
                    @include('orders.partials.table', ['orders' => $activeOrders, 'tableId' => 'activeOrdersTable'])
                </div>
                <!-- Completed Orders Tab -->
                <div class="tab-pane fade" id="completed-orders" role="tabpanel">
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