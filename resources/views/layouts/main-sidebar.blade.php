<!-- main-sidebar -->
		<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
		<aside class="app-sidebar sidebar-scroll">
			<div class="main-sidebar-header active">
				<a class="desktop-logo logo-light active" href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/Logo.png')}}" class="main-logo" alt="logo"></a>
			</div>
			<div class="main-sidemenu">
				<div class="app-sidebar__user clearfix">
					<div class="dropdown user-pro-body">
						<div class="">
							<img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/img/Logo.png')}}"><span class="avatar-status profile-status bg-green"></span>
						</div>
						<div class="user-info">
							<h4 class="font-weight-semibold mt-3 mb-0">{{ auth()->user()->name }}</h4>
							<span class="mb-0 text-muted">{{ auth()->user()->getRoleNames()->first()  }}</span>
						</div>
					</div>
				</div>
				<ul class="side-menu">
					<li class="side-item side-item-category">Users Managment</li>
					<li class="slide">
                    @can("roles_view")

                        <a class="side-menu__item" href="{{ route('roles.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
                                <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                                <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
                            </svg>
                            <span class="side-menu__label">الادوار والصلاحيات</span>
                            {{-- <span class="badge badge-success side-badge">1</span> --}}
                        </a>
                    @endcan

					</li>
                    <li class="slide">
                        @can('users_view')
                            <a class="side-menu__item" href="{{ route('users.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0V0z" fill="none"/>
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                <span class="side-menu__label">المستخدمين</span>
                            </a>
                        @endcan
                    </li>

                    <li class="side-item side-item-category">Valuation Management</li>
                    <li class="slide">
                        @can('question_steps_view')
                            <a class="side-menu__item" href="{{ route('question_steps.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="side-menu__icon"
                                    viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0V0z" fill="none"/>
                                    <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2zm0 6h12v2H3v-2z"/>
                                </svg>
                                <span class="side-menu__label">مراحل الأسئلة</span>
                            </a>
                        @endcan
                    </li>
                    <li class="slide">
                        @can('categories_view')
                            <a class="side-menu__item" href="{{ route('categories.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0V0z" fill="none"/>
                                    <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                                </svg>
                                <span class="side-menu__label">الفئات</span>
                            </a>
                        @endcan
                    </li>

                    <li class="slide">
                        @can('questions_view')
                            <a class="side-menu__item" href="{{ route('questions.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0V0z" fill="none"/>
                                    <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                                </svg>
                                <span class="side-menu__label"> الاسئلة</span>
                            </a>
                        @endcan
                    </li>

                    {{-- <li class="slide">
                        @can('app_pages_view')
                            <a class="side-menu__item" href="{{ route('app_pages.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0V0z" fill="none"/>
                                    <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                                </svg>
                                <span class="side-menu__label">الصفحات</span>
                            </a>
                        @endcan
                    </li> --}}
                <li class="slide">
                    @can('terms_view')
                        <a class="side-menu__item" href="{{ route('terms.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0V0z" fill="none"/>
                                <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm8 1.5V8h4.5"/>
                                <path d="M8 12h8M8 16h8M8 20h5"/>
                            </svg>
                            <span class="side-menu__label">الشروط والأحكام</span>
                        </a>
                    @endcan
                </li>
                <li class="side-item side-item-category">Orders Management</li>

                <li class="slide">
                    @can('orders_view')
                        <a class="side-menu__item" href="{{ route('orders.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="side-menu__icon"
                                viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0V0z" fill="none"/>
                                <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2
                                        2-.9 2-2-.9-2-2-2zm10 0c-1.1
                                        0-2 .9-2 2s.9 2 2 2 2-.9
                                        2-2-.9-2-2-2zM7.16 14h9.45c.75
                                        0 1.41-.41 1.75-1.03l3.58-6.49
                                        -1.74-.97-3.58 6.49H8.53L4.27
                                        4H1v2h2l3.6 7.59-1.35 2.44C4.52
                                        16.37 5.48 18 7 18h12v-2H7l1.16-2z"/>
                            </svg>
                            <span class="side-menu__label">الطلبات</span>
                        </a>
                    @endcan
                </li>
                <li class="side-item side-item-category">Payments Management</li>

                <li class="slide">
                    @can('payments_view')
                        <a class="side-menu__item" href="{{ route('payments.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0V0z" fill="none"/>
                                <path d="M12 1C5.93 1 1 5.93 1 12s4.93 11 11 11
                                        11-4.93 11-11S18.07 1 12 1zm1 17.93c-2.83.48-5.48-.48-7.07-2.07
                                        S4.48 14.83 4 12c.48-2.83 2.48-5.18 5.07-6.07
                                        2.59-.89 5.32.07 7.07 2.07 1.75 2 2.41 4.74 1.93 7.57-.48 2.83-2.48 5.18-5.07 6.07z"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <span class="side-menu__label">المدفوعات</span>
                        </a>
                    @endcan
                </li>


				</ul>
			</div>
		</aside>
<!-- main-sidebar -->
