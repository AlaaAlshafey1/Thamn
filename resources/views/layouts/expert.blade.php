<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الخبير - ثمن')</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap CSS RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <style>
        :root {
            --ex-bg:      #f8f9fc;
            --ex-card:    #ffffff;
            --ex-text:    #111111;
            --ex-muted:   #888888;
            --ex-orange:  #ff9800;
            --ex-border:  #eeeeee;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif !important;
            background-color: var(--ex-bg);
            color: var(--ex-text);
            margin: 0; padding: 0;
            overflow-x: hidden;
        }

        /* ══════════ SIDEBAR ══════════ */
        .ex-sidebar {
            width: 270px;
            background: #fff;
            position: fixed;
            top: 0; right: 0; bottom: 0;
            border-left: 1px solid var(--ex-border);
            z-index: 1001;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-logo {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--ex-border);
            flex-shrink: 0;
        }
        .sidebar-logo img { max-height: 44px; max-width: 170px; object-fit: contain; }

        .sidebar-menu { padding: 16px 12px; flex-grow: 1; overflow-y: auto; }
        .menu-section-title {
            font-size: 0.7rem; font-weight: 800; color: #bbb;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 8px 12px 4px; margin-bottom: 4px;
        }
        .menu-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 11px 14px; border-radius: 12px;
            color: #555; text-decoration: none;
            font-weight: 700; font-size: 0.92rem; margin-bottom: 4px;
            transition: all 0.2s;
        }
        .menu-item.active { background: #111; color: #fff; }
        .menu-item:hover:not(.active) { background: #f5f5f5; color: #111; }
        .menu-item-content { display: flex; align-items: center; gap: 12px; }
        .menu-item i { font-size: 1.25rem; }
        .menu-badge {
            background: var(--ex-orange); color: #fff; font-size: 0.72rem;
            font-weight: 900; min-width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50px; padding: 0 5px;
        }
        .sidebar-footer {
            padding: 16px; border-top: 1px solid var(--ex-border);
            text-align: center; flex-shrink: 0;
        }
        .footer-terms { font-size: 0.72rem; color: #aaa; font-weight: 600; line-height: 1.8; }

        /* ══════════ HEADER ══════════ */
        .ex-header {
            height: 72px;
            background: #fff;
            border-bottom: 1px solid var(--ex-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0; right: 270px; left: 0;
            z-index: 1000;
            transition: right 0.3s;
        }
        .header-right { display: flex; align-items: center; gap: 16px; }
        .header-left  { display: flex; align-items: center; gap: 12px; }

        .search-bar { position: relative; }
        .search-bar input {
            background: #f5f5f5; border: none; border-radius: 50px;
            padding: 9px 40px 9px 90px; font-size: 0.88rem; outline: none;
            width: 320px; font-family: 'Cairo', sans-serif;
            transition: box-shadow 0.2s;
        }
        .search-bar input:focus { box-shadow: 0 0 0 3px rgba(255,152,0,0.15); }
        .search-bar .s-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 1.1rem; }
        .search-btn {
            position: absolute; left: 4px; top: 4px; bottom: 4px;
            background: #111; color: #fff; border: none; border-radius: 50px;
            padding: 0 18px; font-size: 0.82rem; font-weight: 800;
            cursor: pointer; font-family: 'Cairo', sans-serif;
            transition: background 0.2s;
        }
        .search-btn:hover { background: #333; }

        .h-icon-btn {
            width: 40px; height: 40px; border-radius: 50%;
            border: 1px solid #eee; display: flex; align-items: center;
            justify-content: center; color: #555; font-size: 1.2rem;
            cursor: pointer; background: #fff; position: relative;
            transition: all 0.2s; text-decoration: none;
        }
        .h-icon-btn:hover { background: #f5f5f5; color: #111; }
        .h-badge {
            position: absolute; top: -4px; right: -4px;
            background: #f44336; color: #fff; font-size: 0.6rem;
            font-weight: 900; padding: 2px 5px; border-radius: 10px;
            min-width: 16px; text-align: center; line-height: 1.3;
        }

        .user-profile {
            display: flex; align-items: center; gap: 10px;
            cursor: pointer; padding-right: 14px; border-right: 1px solid #eee;
        }
        .user-profile img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #eee; }
        .user-info { text-align: right; }
        .user-name { font-size: 0.88rem; font-weight: 900; color: #111; line-height: 1.2; }
        .user-role { font-size: 0.72rem; color: var(--ex-orange); font-weight: 800; }

        /* ══════════ NOTIFICATION DROPDOWN ══════════ */
        .notif-dropdown {
            position: absolute; top: 58px; left: 0;
            width: 370px; background: #fff;
            border: 1px solid #eee; border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            z-index: 2000; display: none;
            max-height: 480px; overflow: hidden;
            flex-direction: column;
        }
        .notif-dropdown.show { display: flex; }
        .notif-header {
            padding: 16px 18px 12px; border-bottom: 1px solid #f5f5f5;
            display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
        }
        .notif-header-title { font-weight: 900; font-size: 1rem; color: #111; display: flex; align-items: center; gap: 8px; }
        .notif-read-all { font-size: 0.8rem; font-weight: 700; color: var(--ex-orange); cursor: pointer; text-decoration: none; border: none; background: none; }
        .notif-list { overflow-y: auto; flex-grow: 1; }
        .notif-item {
            display: flex; gap: 12px; padding: 14px 18px;
            border-bottom: 1px solid #fafafa; cursor: pointer;
            transition: background 0.2s; text-decoration: none; color: inherit;
        }
        .notif-item:hover { background: #fafafa; }
        .notif-item.unread { background: rgba(255,152,0,0.04); border-right: 3px solid var(--ex-orange); }
        .notif-icon-wrap {
            width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .notif-icon-wrap.type-order { background: rgba(255,152,0,0.12); color: var(--ex-orange); }
        .notif-icon-wrap.type-eval  { background: rgba(0,150,136,0.1); color: #009688; }
        .notif-icon-wrap.type-warn  { background: rgba(244,67,54,0.1); color: #f44336; }
        .notif-icon-wrap.type-gen   { background: rgba(33,150,243,0.1); color: #2196f3; }
        .notif-content { flex: 1; min-width: 0; }
        .notif-title { font-weight: 800; font-size: 0.88rem; color: #111; margin-bottom: 2px; line-height: 1.3; }
        .notif-msg   { font-size: 0.78rem; color: #777; font-weight: 600; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-time  { font-size: 0.7rem; color: #aaa; font-weight: 700; margin-top: 4px; }
        .notif-empty { padding: 50px 20px; text-align: center; color: #bbb; }
        .notif-empty i { font-size: 2.5rem; display: block; margin-bottom: 8px; opacity: 0.4; }
        .notif-footer { padding: 10px 18px; border-top: 1px solid #f5f5f5; text-align: center; flex-shrink: 0; }
        .notif-footer a { font-size: 0.82rem; font-weight: 800; color: #555; text-decoration: none; }
        .notif-footer a:hover { color: var(--ex-orange); }

        /* ══════════ MAIN CONTENT ══════════ */
        .ex-main {
            margin-right: 270px;
            margin-top: 72px;
            padding: 28px;
            min-height: calc(100vh - 72px);
        }

        /* ══════════ TOAST NOTIFICATION ══════════ */
        #ex-toast-wrap {
            position: fixed; bottom: 24px; left: 24px;
            z-index: 9999; display: flex; flex-direction: column; gap: 12px;
        }
        .ex-toast {
            background: #fff; border: 1px solid #eee; border-radius: 14px;
            padding: 14px 18px; min-width: 300px; max-width: 360px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            display: flex; align-items: flex-start; gap: 12px;
            animation: toastIn 0.4s ease both; cursor: pointer;
        }
        .ex-toast.removing { animation: toastOut 0.35s ease forwards; }
        @keyframes toastIn { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
        @keyframes toastOut { from{opacity:1;transform:translateY(0);} to{opacity:0;transform:translateY(20px);} }
        .toast-icon { width: 42px; height: 42px; border-radius: 12px; background: rgba(255,152,0,0.12); color: var(--ex-orange); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .toast-body .toast-title { font-weight: 900; font-size: 0.9rem; color: #111; margin-bottom: 2px; }
        .toast-body .toast-msg { font-size: 0.8rem; color: #666; font-weight: 600; line-height: 1.4; }
        .toast-close { margin-right: auto; color: #bbb; font-size: 1.1rem; cursor: pointer; flex-shrink: 0; background: none; border: none; padding: 0; }

        /* ══════════ MOBILE ══════════ */
        .mobile-toggle { display: none; font-size: 1.5rem; cursor: pointer; color: #333; background: none; border: none; padding: 4px; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 1000; }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 991px) {
            .ex-sidebar { transform: translateX(100%); }
            .ex-sidebar.open { transform: translateX(0); box-shadow: -8px 0 30px rgba(0,0,0,0.1); }
            .ex-header { right: 0; padding: 0 16px; }
            .ex-main { margin-right: 0; padding: 16px; }
            .mobile-toggle { display: flex; }
            .search-bar { display: none; }
            .notif-dropdown { left: auto; right: -10px; width: 320px; }
        }
    </style>
    @yield('css')
</head>
<body>

    <!-- Toast wrap -->
    <div id="ex-toast-wrap"></div>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- ══════════ SIDEBAR ══════════ -->
    <aside class="ex-sidebar" id="expertSidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <a href="{{ route('dashboard') }}">
                <img 
                    src="{{ asset('assets/img/Logo-black.png') }}" 
                    alt="ثمن"
                    onerror="this.onerror=null; this.src='{{ asset('assets/img/Logo.png') }}'"
                >
            </a>
        </div>

        <!-- Menu -->
        <div class="sidebar-menu">
            <div class="menu-section-title">القائمة الرئيسية</div>

            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="menu-item-content">
                    <i class='bx bx-grid-alt'></i>
                    <span>لوحة التحكم</span>
                </div>
            </a>

            <a href="{{ route('orders.index') }}" class="menu-item {{ request()->routeIs('orders.index') && !request('status') ? 'active' : '' }}">
                <div class="menu-item-content">
                    <i class='bx bx-list-ul'></i>
                    <span>كل الطلبات</span>
                </div>
            </a>

            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="menu-item {{ (request('status') == 'pending' || request()->routeIs('orders.show')) ? 'active' : '' }}">
                <div class="menu-item-content">
                    <i class='bx bx-check-shield'></i>
                    <span>الطلبات للتقييم</span>
                </div>
                <span class="menu-badge" id="sidebar-pending-badge" style="display:none;">0</span>
            </a>

            <div class="menu-section-title" style="margin-top:12px;">المالية</div>

            <a href="{{ route('withdrawals.create') }}" class="menu-item {{ request()->routeIs('withdrawals.create') ? 'active' : '' }}">
                <div class="menu-item-content">
                    <i class='bx bx-money-withdraw'></i>
                    <span>طلب سحب رصيد</span>
                </div>
            </a>

            <a href="{{ route('withdrawals.my') }}" class="menu-item {{ request()->routeIs('withdrawals.my') ? 'active' : '' }}">
                <div class="menu-item-content">
                    <i class='bx bx-wallet'></i>
                    <span>سحوباتي</span>
                </div>
            </a>

            <div class="menu-section-title" style="margin-top:12px;">الحساب</div>

            <a href="{{ route('profile.edit') }}" class="menu-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <div class="menu-item-content">
                    <i class='bx bx-user-circle'></i>
                    <span>الملف الشخصي</span>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="d-contents">
                @csrf
                <button type="submit" class="menu-item w-100 text-danger" style="border:none;background:none;text-align:right;">
                    <div class="menu-item-content">
                        <i class='bx bx-log-out'></i>
                        <span>تسجيل الخروج</span>
                    </div>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="sidebar-footer">
            <div class="footer-terms">
                سياسة الخصوصية | الشروط والأحكام<br>
                © جميع الحقوق محفوظة لشركة ثمن {{ date('Y') }}
            </div>
        </div>
    </aside>

    <!-- ══════════ HEADER ══════════ -->
    <header class="ex-header">
        <!-- Right: Toggle + Search -->
        <div class="header-right">
            <button class="mobile-toggle" onclick="toggleSidebar()" aria-label="فتح القائمة">
                <i class='bx bx-menu'></i>
            </button>

            <div class="search-bar d-none d-lg-block">
                <i class='bx bx-search s-icon'></i>
                <input type="text" id="ex-search-input" placeholder="ابحث في الطلبات..." onkeydown="if(event.key==='Enter'){handleSearch();}">
                <button class="search-btn" onclick="handleSearch()">بحث</button>
            </div>
        </div>

        <!-- Left: Actions + User -->
        <div class="header-left">

            <!-- Greeting -->
            <div class="d-none d-md-flex align-items-center gap-2" style="font-size:0.88rem;font-weight:700;color:#555;">
                <i class='bx bx-sun' style="color:var(--ex-orange);font-size:1.2rem;"></i>
                <span id="ex-greeting">أهلاً</span>
                <strong style="color:#111;">{{ auth()->user()->first_name ?? explode(' ', auth()->user()->name)[0] }}</strong>
            </div>

            <!-- Notification Bell -->
            <div style="position:relative;">
                <div class="h-icon-btn" id="notif-bell" onclick="toggleNotifications()" title="الإشعارات">
                    <i class='bx bx-bell' style="font-size:1.3rem;"></i>
                    <span class="h-badge" id="notif-count-badge" style="display:none;">0</span>
                </div>

                <!-- Notification Dropdown -->
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-header">
                        <div class="notif-header-title">
                            <i class='bx bx-bell' style="color:var(--ex-orange);"></i>
                            الإشعارات
                        </div>
                        <button class="notif-read-all" onclick="markAllRead()">
                            <i class='bx bx-check-double'></i> قراءة الكل
                        </button>
                    </div>
                    <div class="notif-list" id="notif-list">
                        <div class="notif-empty">
                            <i class='bx bx-bell-off'></i>
                            لا توجد إشعارات جديدة
                        </div>
                    </div>
                    <div class="notif-footer">
                        <a href="{{ route('orders.index', ['status' => 'pending']) }}">
                            <i class='bx bx-list-ul'></i> عرض كل الطلبات المعلقة
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->first_name ?? auth()->user()->name }}</div>
                        <div class="user-role">خبير معتمد</div>
                    </div>
                    <img 
                        src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : asset('assets/img/faces/6.jpg') }}" 
                        alt="{{ auth()->user()->name }}"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ff9800&color=fff&bold=true'"
                    >
                    <i class='bx bx-chevron-down' style="color:#aaa;font-size:1rem;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:14px;padding:8px;min-width:190px;">
                    <li>
                        <div style="padding:10px 14px 6px;border-bottom:1px solid #f5f5f5;margin-bottom:4px;">
                            <div style="font-weight:800;font-size:0.9rem;">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.75rem;color:#888;">{{ auth()->user()->email }}</div>
                        </div>
                    </li>
                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class='bx bx-user me-2'></i>الملف الشخصي</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class='bx bx-grid-alt me-2'></i>لوحة التحكم</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class='bx bx-log-out me-2'></i>تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- ══════════ MAIN CONTENT ══════════ -->
    <main class="ex-main">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // ─── CSRF Setup ────────────────────────────────────────────────
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const fetchOpts = (method = 'POST', body = null) => ({
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: body ? JSON.stringify(body) : null,
    });

    // ─── Greeting ──────────────────────────────────────────────────
    function setGreeting() {
        const h = new Date().getHours();
        const g = h < 12 ? 'صباح الخير،' : h < 17 ? 'مساء الخير،' : 'مساء النور،';
        const el = document.getElementById('ex-greeting');
        if (el) el.textContent = g;
    }
    setGreeting();

    // ─── Sidebar Toggle ────────────────────────────────────────────
    function toggleSidebar() {
        document.getElementById('expertSidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('expertSidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }

    // ─── Search ────────────────────────────────────────────────────
    function handleSearch() {
        const q = document.getElementById('ex-search-input').value.trim();
        if (q) window.location.href = `{{ route('orders.index') }}?search=` + encodeURIComponent(q);
    }

    // ─── Notification System ───────────────────────────────────────
    let notifOpen = false;
    let lastUnreadCount = 0;
    let knownIds = new Set();

    function toggleNotifications() {
        notifOpen = !notifOpen;
        document.getElementById('notif-dropdown').classList.toggle('show', notifOpen);
        if (notifOpen) fetchNotifications();
        // Close when clicking outside
        if (notifOpen) {
            setTimeout(() => document.addEventListener('click', closeNotifOutside), 10);
        }
    }

    function closeNotifOutside(e) {
        const dd = document.getElementById('notif-dropdown');
        const bell = document.getElementById('notif-bell');
        if (!dd.contains(e.target) && !bell.contains(e.target)) {
            notifOpen = false;
            dd.classList.remove('show');
            document.removeEventListener('click', closeNotifOutside);
        }
    }

    function getNotifIcon(type) {
        const map = {
            'new_expert_order':       ['bx-package', 'type-order'],
            'expert_evaluated':       ['bx-badge-check', 'type-eval'],
            'order_warning':          ['bx-error', 'type-warn'],
        };
        const [icon, cls] = map[type] || ['bx-bell', 'type-gen'];
        return { icon, cls };
    }

    function renderNotifications(notifications) {
        const list = document.getElementById('notif-list');
        if (!notifications.length) {
            list.innerHTML = `<div class="notif-empty"><i class='bx bx-bell-off'></i>لا توجد إشعارات بعد</div>`;
            return;
        }
        list.innerHTML = notifications.map(n => {
            const { icon, cls } = getNotifIcon(n.type);
            const orderHref = n.order_id ? `{{ url('orders') }}/${n.order_id}` : '#';
            return `
            <a href="${orderHref}" class="notif-item ${n.read ? '' : 'unread'}" 
               data-id="${n.id}" data-order="${n.order_id || ''}"
               onclick="handleNotifClick(event, '${n.id}', '${orderHref}')">
                <div class="notif-icon-wrap ${cls}"><i class='bx ${icon}'></i></div>
                <div class="notif-content">
                    <div class="notif-title">${n.title}</div>
                    <div class="notif-msg">${n.message}</div>
                    <div class="notif-time"><i class='bx bx-time-five' style="font-size:0.7rem;vertical-align:middle;"></i> ${n.time}</div>
                </div>
                ${!n.read ? '<div style="width:8px;height:8px;background:var(--ex-orange);border-radius:50%;flex-shrink:0;margin-top:6px;"></div>' : ''}
            </a>`;
        }).join('');
    }

    function handleNotifClick(e, id, href) {
        e.preventDefault();
        // Mark as read
        fetch(`/expert/notifications/${id}/read`, fetchOpts('POST'));
        // Navigate
        window.location.href = href;
    }

    async function fetchNotifications() {
        try {
            const res = await fetch('/expert/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
            const data = await res.json();
            const { notifications, unread } = data;

            // Update badge
            const badge = document.getElementById('notif-count-badge');
            const sbBadge = document.getElementById('sidebar-pending-badge');
            if (unread > 0) {
                badge.textContent = unread > 99 ? '99+' : unread;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }

            // Sidebar pending badge (from notifications of type new_expert_order)
            const pendingOrders = notifications.filter(n => n.type === 'new_expert_order' && !n.read).length;
            if (sbBadge) {
                if (pendingOrders > 0) {
                    sbBadge.textContent = pendingOrders;
                    sbBadge.style.display = 'flex';
                } else {
                    sbBadge.style.display = 'none';
                }
            }

            // Detect NEW notifications (since last check)
            if (knownIds.size > 0) {
                notifications.filter(n => !n.read && !knownIds.has(n.id)).forEach(n => {
                    showToast(n);
                });
            }
            // Update known IDs
            notifications.forEach(n => knownIds.add(n.id));
            lastUnreadCount = unread;

            // Render dropdown list if open
            if (notifOpen) renderNotifications(notifications);
        } catch (e) {
            console.warn('Notification fetch failed:', e);
        }
    }

    async function markAllRead() {
        await fetch('/expert/notifications/read-all', fetchOpts('POST'));
        document.getElementById('notif-count-badge').style.display = 'none';
        document.getElementById('notif-list').querySelectorAll('.notif-item').forEach(el => {
            el.classList.remove('unread');
            const dot = el.querySelector('div[style*="border-radius:50%"]');
            if (dot) dot.remove();
        });
        const sb = document.getElementById('sidebar-pending-badge');
        if (sb) sb.style.display = 'none';
    }

    // ─── Toast Notification ────────────────────────────────────────
    function showToast(notif) {
        const { icon, cls } = getNotifIcon(notif.type);
        const wrap = document.getElementById('ex-toast-wrap');
        const toast = document.createElement('div');
        toast.className = 'ex-toast';
        const orderHref = notif.order_id ? `{{ url('orders') }}/${notif.order_id}` : null;
        toast.innerHTML = `
            <div class="toast-icon ${cls}"><i class='bx ${icon}'></i></div>
            <div class="toast-body">
                <div class="toast-title">${notif.title}</div>
                <div class="toast-msg">${notif.message.substring(0, 80)}${notif.message.length > 80 ? '...' : ''}</div>
            </div>
            <button class="toast-close" onclick="removeToast(this.parentElement)"><i class='bx bx-x'></i></button>
        `;
        if (orderHref) {
            toast.style.cursor = 'pointer';
            toast.addEventListener('click', (e) => {
                if (e.target.closest('.toast-close')) return;
                fetch(`/expert/notifications/${notif.id}/read`, fetchOpts('POST'));
                window.location.href = orderHref;
            });
        }
        wrap.appendChild(toast);
        // Auto remove after 6 seconds
        setTimeout(() => removeToast(toast), 6000);

        // Browser Notification (if permitted)
        if (Notification.permission === 'granted') {
            const bn = new Notification(notif.title, {
                body: notif.message.substring(0, 100),
                icon: '{{ asset("assets/img/brand/logo.png") }}',
                tag: notif.id,
            });
            if (orderHref) bn.onclick = () => { window.focus(); window.location.href = orderHref; };
        }
    }

    function removeToast(el) {
        el.classList.add('removing');
        setTimeout(() => el.remove(), 350);
    }

    // ─── Request Browser Notification Permission ───────────────────
    function requestNotifPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    // ─── Start Polling ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        requestNotifPermission();
        fetchNotifications(); // Initial fetch
        setInterval(fetchNotifications, 30000); // Poll every 30s
    });
    </script>
    @yield('js')
</body>
</html>
