@extends('layouts.master')
@section('css')
	<!--  Owl-carousel css-->
	<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
	<!-- Maps css -->
	<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
	
	<!-- Custom Dashboard Styles -->
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap');
		
		body {
			font-family: 'Tajawal', sans-serif;
		}

		/* AI Assistant Pulse */
		.ai-widget {
			background: linear-gradient(135deg, #1f2937, #111827);
			border-radius: 15px;
			padding: 25px;
			color: #fff;
			position: relative;
			overflow: hidden;
			box-shadow: 0 10px 30px rgba(0,0,0,0.15);
			border: 1px solid rgba(193,149,62,0.3);
			transition: all 0.3s ease;
		}
		.ai-widget:hover {
			transform: translateY(-5px);
			box-shadow: 0 15px 35px rgba(193,149,62,0.4);
		}
		.ai-widget::before {
			content: '';
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: radial-gradient(circle, rgba(193,149,62,0.1) 0%, transparent 60%);
			animation: rotate 15s linear infinite;
		}
		@keyframes rotate {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
		.ai-icon {
			font-size: 3rem;
			color: #c1953e;
			animation: pulse 2s infinite;
			text-shadow: 0 0 15px rgba(193,149,62,0.6);
		}
		@keyframes pulse {
			0% { transform: scale(1); opacity: 1; }
			50% { transform: scale(1.1); opacity: 0.8; }
			100% { transform: scale(1); opacity: 1; }
		}

		/* Module Cards */
		.module-card {
			background: #fff;
			border-radius: 15px;
			padding: 20px;
			text-align: center;
			transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
			box-shadow: 0 5px 15px rgba(0,0,0,0.05);
			border-bottom: 4px solid transparent;
			display: block;
			color: #333;
			text-decoration: none !important;
			margin-bottom: 20px;
			height: calc(100% - 20px);
		}
		.module-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 15px 30px rgba(193,149,62,0.15);
			border-bottom: 4px solid #c1953e;
			color: #c1953e;
		}
		.module-icon-wrap {
			width: 65px;
			height: 65px;
			margin: 0 auto 15px;
			background: linear-gradient(135deg, #fdf6e3, #f5ecd5);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 26px;
			color: #c1953e;
			transition: all 0.5s ease;
			box-shadow: inset 0 0 10px rgba(193,149,62,0.1);
		}
		.module-card:hover .module-icon-wrap {
			background: linear-gradient(135deg, #c1953e, #b08637);
			color: #fff;
			transform: rotateY(180deg) scale(1.1);
			box-shadow: 0 5px 15px rgba(193,149,62,0.4);
		}

		/* Stats Cards (Update existing) */
		.sales-card {
			transition: all 0.3s ease;
			border-radius: 15px;
			overflow: hidden;
		}
		.sales-card:hover {
			transform: scale(1.02) translateY(-5px);
			box-shadow: 0 12px 25px rgba(0,0,0,0.15);
		}

		/* Glassmorphism for tables */
		.card-table-two {
			border-radius: 15px;
			box-shadow: 0 8px 25px rgba(0,0,0,0.05);
			border: none;
			transition: all 0.3s;
		}
		.card-table-two:hover {
			box-shadow: 0 12px 30px rgba(0,0,0,0.08);
		}
		
		/* Fade in animations */
		.fade-in-up {
			animation: fadeInUp 0.8s ease-out forwards;
			opacity: 0;
			transform: translateY(20px);
		}
		.delay-1 { animation-delay: 0.1s; }
		.delay-2 { animation-delay: 0.2s; }
		.delay-3 { animation-delay: 0.3s; }
		
		@keyframes fadeInUp {
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
	</style>
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between align-items-center mb-4 fade-in-up">
		<div class="left-content d-flex align-items-center">
			<div class="d-flex align-items-center">
				<div class="header-logo-wrap">
					<img src="{{ asset('assets/img/Logo.png') }}" alt="Thamen Logo" class="ht-60 wd-60 mg-r-3 shadow-sm" style="border-radius: 15px; transition: transform 0.3s;">
				</div>
				<div class="mg-l-4">
					<h2 class="main-content-title tx-28 mg-b-2" style="font-family: 'Tajawal', sans-serif; font-weight: 800; color: #2c3e50;">
						لوحة التحكم الذكية <span style="color: #c1953e;">ثمن</span> ✨
					</h2>
					<p class="mg-b-0 tx-15 text-muted" style="font-family: 'Tajawal', sans-serif;">
						نظام إدارة متكامل مدعوم بالذكاء الاصطناعي لتحليل البيانات وتسهيل الوصول.
					</p>
				</div>
			</div>
		</div>
	</div>
	<!-- /breadcrumb -->
@endsection

@section('content')
	@hasanyrole('admin|superadmin')
	<!-- AI Widget Row -->
	<div class="row row-sm mb-4 fade-in-up delay-1">
		<div class="col-xl-12">
			<div class="ai-widget">
				<div class="d-flex align-items-center">
					<div class="ai-icon mg-l-20 mr-4 ml-4">
						<i class="fas fa-brain"></i>
					</div>
					<div style="z-index: 1; position: relative; width: 100%;">
						<h4 class="mb-2 font-weight-bold" style="font-family: 'Tajawal', sans-serif; color: #e5cc98;">مساعد ثمن الذكي (AI)</h4>
						<p class="mb-0 text-white-50 tx-15" style="font-family: 'Tajawal', sans-serif;" id="ai-response-text">
							<i class="fas fa-magic text-warning"></i> تحليل البيانات يشير إلى أداء ممتاز! تم إنجاز {{ $stats['orders_count'] ?? 0 }} طلب تقييم، والمنصة تجذب المزيد من المستخدمين بشكل مستمر.
						</p>
						<div class="mt-3">
							<form id="ai-chat-form" class="d-flex align-items-center" onsubmit="askAI(event)">
								<input type="text" id="ai-prompt" class="form-control" placeholder="اسأل مساعد ثمن الذكي أي شيء عن المنصة..." style="background: rgba(255,255,255,0.1); border: 1px solid rgba(193,149,62,0.4); color: white; border-radius: 10px; margin-left: 10px;">
								<button type="submit" class="btn btn-warning" style="border-radius: 10px; font-weight: bold; min-width: 100px;">
									<span id="ai-btn-text">إرسال <i class="fas fa-paper-plane ml-1"></i></span>
									<span id="ai-btn-loader" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modules Grid Row -->
	<div class="row row-sm mb-4 fade-in-up delay-2">
		<div class="col-xl-12">
			<h5 class="mb-3 font-weight-bold" style="font-family: 'Tajawal', sans-serif; color: #2c3e50;">
				<i class="fas fa-th-large text-warning mr-2 ml-2"></i> الوصول السريع للإضافات (Modules)
			</h5>
		</div>
		
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
			<a href="{{ route('orders.index') }}" class="module-card">
				<div class="module-icon-wrap"><i class="fas fa-shopping-cart"></i></div>
				<h6 class="font-weight-bold mb-0">إدارة الطلبات</h6>
			</a>
		</div>
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
			<a href="{{ route('experts.index') }}" class="module-card">
				<div class="module-icon-wrap"><i class="fas fa-user-tie"></i></div>
				<h6 class="font-weight-bold mb-0">الخبراء</h6>
			</a>
		</div>
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
			<a href="{{ route('users.index') }}" class="module-card">
				<div class="module-icon-wrap"><i class="fas fa-users"></i></div>
				<h6 class="font-weight-bold mb-0">المستخدمين</h6>
			</a>
		</div>
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
			<a href="{{ route('withdrawals.index') }}" class="module-card">
				<div class="module-icon-wrap"><i class="fas fa-money-check-alt"></i></div>
				<h6 class="font-weight-bold mb-0">طلبات السحب</h6>
			</a>
		</div>
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
			<a href="{{ route('categories.index') }}" class="module-card">
				<div class="module-icon-wrap"><i class="fas fa-layer-group"></i></div>
				<h6 class="font-weight-bold mb-0">الأقسام</h6>
			</a>
		</div>
		<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
			<a href="{{ route('payments.index') }}" class="module-card">
				<div class="module-icon-wrap"><i class="fas fa-file-invoice-dollar"></i></div>
				<h6 class="font-weight-bold mb-0">المدفوعات</h6>
			</a>
		</div>
	</div>

	<!-- Stats Row -->
	<div class="row row-sm fade-in-up delay-3">
		<div class="col-xl-12">
			<h5 class="mb-3 font-weight-bold" style="font-family: 'Tajawal', sans-serif; color: #2c3e50;">
				<i class="fas fa-chart-pie text-warning mr-2 ml-2"></i> إحصائيات النظام
			</h5>
		</div>
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
									{{ number_format($stats['users_count'] ?? 0) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">عدد المستخدمين المسجلين في النظام</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-users text-white tx-30"></i>
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
									{{ number_format($stats['experts_count'] ?? 0) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">عدد الخبراء المعتمدين</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-user-tie text-white tx-30"></i>
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
									{{ number_format($stats['orders_count'] ?? 0) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">إجمالي الطلبات (بانتظار التقييم:
									{{ $stats['pending_orders'] ?? 0 }})</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-shopping-cart text-white tx-30"></i>
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
									{{ number_format($stats['total_revenue'] ?? 0, 2) }} ر.س</h4>
								<p class="mb-0 tx-12 text-white op-7">إجمالي المدفوعات المكتملة</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-money-bill-wave text-white tx-30"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endhasanyrole

	@if(auth()->user()->hasRole('expert'))
	<!-- Expert Dashboard View -->
	<div class="row row-sm fade-in-up delay-1">
		<div class="col-xl-12">
			<h5 class="mb-3 font-weight-bold" style="font-family: 'Tajawal', sans-serif; color: #2c3e50;">
				<i class="fas fa-star text-warning mr-2 ml-2"></i> إحصائيات الخبير
			</h5>
		</div>
		<div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-success-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">طلبات مقيمة بنجاح</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['orders_completed'] ?? 0) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">عدد الطلبات التي قمت بتقييمها</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-check-circle text-white tx-30"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-warning-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">الرصيد المتاح</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['balance'] ?? 0, 2) }} ر.س</h4>
								<p class="mb-0 tx-12 text-white op-7">أرباحك القابلة للسحب</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-wallet text-white tx-30"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-info-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-12 text-white">إجمالي الطلبات المتاحة</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{ number_format($stats['pending_orders'] ?? 0) }}</h4>
								<p class="mb-0 tx-12 text-white op-7">طلبات بانتظار التقييم في النظام</p>
							</div>
							<span class="float-right my-auto mr-auto">
								<i class="fas fa-list text-white tx-30"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif

	<!-- row -->
	<div class="row row-sm fade-in-up delay-3">
		<div class="{{ auth()->user()->hasAnyRole(['admin', 'superadmin']) ? 'col-md-12 col-lg-8 col-xl-8' : 'col-xl-12' }}">
			<div class="card card-table-two">
				<div class="d-flex justify-content-between">
					<h4 class="card-title mb-1"><i class="fas fa-clock text-primary mr-2 ml-2"></i> آخر الطلبات</h4>
					<i class="mdi mdi-dots-horizontal text-gray"></i>
				</div>
				<span class="tx-12 tx-muted mb-3 ">
					{{ auth()->user()->hasAnyRole(['admin', 'superadmin']) ? 'قائمة بآخر 5 طلبات مسجلة في النظام.' : 'قائمة بآخر 5 طلبات قمت بتقييمها.' }}
				</span>
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
									<span class="badge {{ $statusClasses[$order->status] ?? 'badge-secondary' }} px-2 py-1 tx-11">
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
		
		@hasanyrole('admin|superadmin')
		<div class="col-md-12 col-lg-8 col-xl-4">
			<div class="card card-table-two">
				<div class="d-flex justify-content-between">
					<h4 class="card-title mb-1"><i class="fas fa-wallet text-success mr-2 ml-2"></i> آخر المدفوعات</h4>
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
									<span class="badge {{ $payment->status == 'CAPTURED' ? 'badge-success' : 'badge-danger' }} px-2 py-1 tx-11">
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
		@endhasanyrole
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
	<script>
		function askAI(event) {
			event.preventDefault();
			const promptInput = document.getElementById('ai-prompt');
			const promptText = promptInput.value;
			if (!promptText) return;

			// Show loader
			document.getElementById('ai-btn-text').style.display = 'none';
			document.getElementById('ai-btn-loader').style.display = 'inline-block';
			promptInput.disabled = true;

			// Send to backend
			fetch('{{ route('admin.ai.ask') }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify({ prompt: promptText })
			})
			.then(response => response.json())
			.then(data => {
				document.getElementById('ai-response-text').innerHTML = `<i class="fas fa-robot text-warning"></i> ${data.response}`;
				promptInput.value = '';
			})
			.catch(error => {
				document.getElementById('ai-response-text').innerHTML = `<i class="fas fa-exclamation-triangle text-danger"></i> حدث خطأ، يرجى المحاولة لاحقاً.`;
			})
			.finally(() => {
				document.getElementById('ai-btn-text').style.display = 'inline-block';
				document.getElementById('ai-btn-loader').style.display = 'none';
				promptInput.disabled = false;
				promptInput.focus();
			});
		}
	</script>
@endsection