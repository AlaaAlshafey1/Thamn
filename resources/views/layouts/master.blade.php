<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="ثمن - التثمين الاسترشادي للممتلكات والسلع المستعملة">
		<meta name="Author" content="Thamn - ثمن">
		<meta name="Keywords" content="ثمن, تقييم, تثمين, مقتنيات فاخرة, ذكاء اصطناعي, Thamn, تقييم منتجات, تسعير"/>
		@include('layouts.head')
		<style>
			@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
			body, h1, h2, h3, h4, h5, h6, p, a, div, span, button, input, select, textarea, table, th, td, .btn, .alert, .badge {
				font-family: 'Cairo', sans-serif !important;
			}
			/* Mobile: prevent horizontal scroll since sidebar is hidden */
			@media (max-width: 767px) {
				.app-content,
				.container-fluid {
					overflow-x: hidden;
				}
			}
			/* Desktop: force margin-right to prevent sidebar overlap */
			@media (min-width: 768px) {
				.app-content {
					margin-right: 240px !important;
				}
			}
		</style>
	</head>

	<body class="main-body app sidebar-mini">
		<!-- Loader -->
		<div id="global-loader">
			<img src="{{URL::asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->
		@include('layouts.main-sidebar')
		<!-- main-content -->
		<div class="main-content app-content">
			@include('layouts.main-header')
			<!-- container -->
			<div class="container-fluid">
				@yield('page-header')
				@yield('content')
				@include('layouts.sidebar')
				@include('layouts.models')
            	@include('layouts.footer')
				@include('layouts.footer-scripts')
	</body>
</html>
