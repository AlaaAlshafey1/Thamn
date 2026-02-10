@extends('layouts.master')
@section('css')
	<!--  Owl-carousel css-->
	<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
	<!-- Maps css -->
	<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between align-items-center">
		<div class="left-content d-flex align-items-center">
			<div class="d-flex align-items-center">
				<img src="{{ asset('assets/img/Logo.png') }}" alt="Thamen Logo" class="ht-50 wd-50 mg-r-2"
					style="border-radius: 10px;">
				<div class="mg-l-3">
					<h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1"
						style="font-family: 'Cairo', sans-serif; font-weight: 700;">
						أهلاً بك في لوحة تحكم <span class="text-primary">ثمن</span>
					</h2>
					<p class="mg-b-0 tx-15" style="font-family: 'Cairo', sans-serif;">
						نظام تثمين السلع وإدارة الإعلانات بسهولة واحترافية.
					</p>
				</div>
			</div>
		</div>
	</div>
	<!-- /breadcrumb -->
@endsection

@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">إجمالي المستخدمين</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['users_count']) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">عدد المستخدمين المسجلين في النظام</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-users text-white"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-danger-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">إجمالي الخبراء</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['experts_count']) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">عدد الخبراء المعتمدين</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-user-tie text-white"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-success-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">إجمالي الطلبات</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['orders_count']) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">إجمالي الطلبات (بانتظار التقييم:
									{{ $stats['pending_orders'] }})</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-shopping-cart text-white"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-warning-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">إجمالي الإيرادات</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['total_revenue'], 2) }} ر.س</h4>
								<p class="mb-0 tx-12 text-white op-7">إجمالي المدفوعات المكتملة</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-money-bill-wave text-white"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
				<!-- row -->
				<div class="row row-sm">
					<div class="col-md-12 col-lg-8 col-xl-8">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">آخر الطلبات</h4>
								<i class="mdi mdi-dots-horizontal text-gray"></i>
							</div>
							<span class="tx-12 tx-muted mb-3 ">قائمة بآخر 5 طلبات مسجلة في النظام.</span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-25p">رقم الطلب</th>
											<th class="wd-lg-25p">المستخدم</th>
											<th class="wd-lg-25p">الفئة</th>
											<th class="wd-lg-25p">الحالة</th>
											<th class="wd-lg-25p">السعر</th>
										</tr>
									</thead>
									<tbody>
                                        @foreach($recentOrders as $order)
										<tr>
											<td>#{{ $order->id }}</td>
											<td class="tx-right tx-medium tx-inverse">{{ $order->user->name ?? 'مستخدم' }}</td>
											<td class="tx-right tx-medium tx-inverse">{{ $order->category->name ?? 'غير محدد' }}</td>
											<td class="tx-right tx-medium tx-inverse">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'badge-warning',
                                                        'orderReceived' => 'badge-info',
                                                        'beingEstimated' => 'badge-primary',
                                                        'estimated' => 'badge-success',
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'قيد الانتظار',
                                                        'orderReceived' => 'تم الاستلام',
                                                        'beingEstimated' => 'جاري التقييم',
                                                        'estimated' => 'تم التقييم',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $statusClasses[$order->status] ?? 'badge-secondary' }}">
                                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                                </span>
                                            </td>
											<td class="tx-right tx-medium tx-danger">{{ number_format($order->total_price, 2) }} ر.س</td>
										</tr>
                                        @endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-8 col-xl-4">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">آخر المدفوعات</h4>
								<i class="mdi mdi-dots-horizontal text-gray"></i>
							</div>
							<span class="tx-12 tx-muted mb-3 ">قائمة بآخر العمليات المالية.</span>
							<div class="table-responsive">
								<table class="table table-hover mb-0 text-md-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-50p">المستخدم</th>
											<th class="wd-lg-25p">المبلغ</th>
											<th class="wd-lg-25p">الحالة</th>
										</tr>
									</thead>
									<tbody>
                                        @foreach($recentPayments as $payment)
										<tr>
											<td>{{ $payment->order->user->name ?? 'مستخدم' }}</td>
											<td class="tx-right tx-medium tx-inverse">{{ number_format($payment->amount, 2) }} ر.س</td>
											<td class="tx-right tx-medium tx-inverse">
                                                <span class="badge {{ $payment->status == 'CAPTURED' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $payment->status == 'CAPTURED' ? 'ناجحة' : 'فاشلة' }}
                                                </span>
                                            </td>
										</tr>
                                        @endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->

                <!-- slot for additional content -->
                <div class="row row-sm">
                    <div class="col-xl-12">
                        {{ $slot }}
                    </div>
                </div>

	</div>
	</div>
	<!-- Container closed -->
@endsection
@section('js')
	<!--Internal  Chart.bundle js -->
	<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
	<!-- Moment js -->
	<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
	<!--Internal  Flot js-->
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
	<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
	<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
	<!--Internal Apexchart js-->
	<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
	<!-- Internal Map -->
	<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
	<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
	<!--Internal  index js -->
	<script src="{{URL::asset('assets/js/index.js')}}"></script>
	<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>
@endsection