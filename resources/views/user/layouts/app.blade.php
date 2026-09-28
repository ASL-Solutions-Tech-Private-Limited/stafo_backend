<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="STAFO is an advanced HRMS software with AI-driven payroll, attendance tracking, and leave management. Automate HR processes seamlessly. Try now!">
    <meta property="og:title" content="STAFO - Best HR Management System | Payroll, Attendance">
    <meta property="og:description"
        content="STAFO is an advanced HRMS software with AI-driven payroll, attendance tracking, and leave management. Automate HR processes seamlessly. Try now!">

    <title>@yield('title', 'Company Dashboard | STAFO HRMS')</title>
    <link rel="shortcut icon" href="{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap.min-5.3.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}?v={{ file_exists(public_path('main/css/dashboard.css')) ? filemtime(public_path('main/css/dashboard.css')) : time() }}">
    <!-- Immediate Theme Setup (Prevents FOUC) -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('stafo_theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    <style>
        :root {
            --stafo-header-height: 58px;
        }

        .theme-toggle-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: scale(1.05);
        }

        /* Permanently lock top header to the top of the viewport */
        header.top-header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: var(--stafo-header-height) !important;
            z-index: 1030 !important;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.15) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        header.top-header .container-fluid,
        header.top-header .container-fluid > div {
            height: 100% !important;
        }

        /* Ensure page content wrapper is pushed down below the fixed header */
        .app-wrapper {
            display: flex !important;
            min-height: calc(100vh - var(--stafo-header-height)) !important;
            margin-top: var(--stafo-header-height) !important;
            background-color: var(--stafo-body-bg, #f8fafc) !important;
        }

        /* Permanently lock desktop sidebar to viewport under the fixed header */
        @media (min-width: 992px) {
            aside.desktop-sidebar {
                position: fixed !important;
                top: var(--stafo-header-height) !important;
                left: 0 !important;
                bottom: 0 !important;
                width: 270px !important;
                height: calc(100vh - var(--stafo-header-height)) !important;
                z-index: 1010 !important;
            }

            .main-content-wrapper {
                margin-left: 270px !important;
            }
        }

        .topbar-dropdown-menu {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            min-width: 220px;
        }
        [data-theme="dark"] .topbar-dropdown-menu {
            border: 1px solid #334155 !important;
            background-color: #1e293b !important;
        }

        /* Notification Dropdown & Toast */
        .notification-btn {
            position: relative;
            background: rgba(255, 255, 255, 0.08) !important;
            border-radius: 8px;
            height: 34px;
            width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .notification-btn:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff;
            transform: scale(1.05);
        }
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            font-size: 0.65rem;
            padding: 2px 5px;
            border-radius: 9999px;
            box-shadow: 0 0 0 2px #0f172a;
            animation: pulseNotif 2s infinite;
        }
        @keyframes pulseNotif {
            0% { transform: scale(1); }
            50% { transform: scale(1.18); }
            100% { transform: scale(1); }
        }
        .topbar-notification-menu {
            width: 340px;
            max-width: 90vw;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        [data-theme="dark"] .topbar-notification-menu {
            border: 1px solid #334155 !important;
            background-color: #1e293b !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        .notif-item {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
            text-decoration: none !important;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        [data-theme="dark"] .notif-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .notif-item:hover {
            background-color: #f8fafc;
        }
        [data-theme="dark"] .notif-item:hover {
            background-color: #0f172a;
        }
        .notif-item.unread {
            background-color: #f0f9ff;
        }
        [data-theme="dark"] .notif-item.unread {
            background-color: rgba(56, 189, 248, 0.08);
        }

        /* Floating Push Toast Alert */
        #stafoNotificationToastContainer {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 380px;
            pointer-events: none;
        }
        .stafo-toast {
            pointer-events: auto;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-left: 4px solid #10b981 !important;
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
            animation: slideInToast 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .stafo-toast:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.45);
        }
        @keyframes slideInToast {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    @yield('css')
</head>

<body>
    <!-- Top Sticky Header -->
    <header class="top-header">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex align-items-center justify-content-between py-2">
                
                <!-- Left: Mobile Toggle + Logo + Date -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-light d-lg-none p-2 border-0" type="button" 
                            data-bs-toggle="offcanvas" data-bs-target="#userSidebarOffcanvas" 
                            aria-controls="userSidebarOffcanvas" style="background: rgba(255,255,255,0.1); border-radius: 8px;">
                        <i class="fa-solid fa-bars fs-5 text-white"></i>
                    </button>
                    
                    <a class="navbar-brand m-0" href="{{ route('user.dashboard') }}">
                        <img src="{{ asset('main/images/logo.png') }}" alt="STAFO logo" height="36">
                    </a>
                    
                    <!-- Realtime Date Badge (Desktop) -->
                    <div class="topbar-date-pill d-none d-md-inline-flex">
                        <i class="fa-regular fa-calendar-days" style="color: #38bdf8;"></i>
                        <span>{{ date('D, d M Y') }}</span>
                    </div>
                </div>

                <!-- Right Menu Items -->
                <div class="d-flex align-items-center gap-3">
                    
                    <!-- Quick Portal Links (Desktop) -->
                    <div class="d-none d-md-flex align-items-center gap-2">
                        <a href="{{ route('company.helpList') }}" class="btn btn-sm btn-outline-light border-0 px-2 py-1 text-light-50" title="Help & Support" style="background: rgba(255,255,255,0.06); border-radius: 8px;">
                            <i class="fa-solid fa-circle-question text-info"></i>
                            <span class="d-none d-lg-inline ms-1 text-light" style="font-size: 0.8rem;">Help</span>
                        </a>
                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-light border-0 px-2 py-1 text-light-50" title="Messages" style="background: rgba(255,255,255,0.06); border-radius: 8px;">
                            <i class="fa-solid fa-comment-dots text-warning"></i>
                            <span class="d-none d-lg-inline ms-1 text-light" style="font-size: 0.8rem;">Chat</span>
                        </a>
                    </div>

                    <!-- Real-Time Push Notifications Dropdown -->
                    @php
                        $stafoCompanyId = Auth::id();
                        $stafoUnreadNotifCount = \App\Models\Notification::where('company_id', $stafoCompanyId)->where('status', 'unread')->count();
                        $stafoInitialNotifications = \App\Models\Notification::with('employee:id,name,emp_id')
                            ->where('company_id', $stafoCompanyId)
                            ->orderBy('id', 'desc')
                            ->take(12)
                            ->get();
                        $stafoLatestId = $stafoInitialNotifications->first() ? $stafoInitialNotifications->first()->id : 0;
                    @endphp
                    <div class="dropdown" id="companyNotificationDropdownWrap">
                        <button class="btn btn-sm btn-outline-light border-0 notification-btn" 
                                type="button" id="companyNotifDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false"
                                title="Live Employee Notifications" onclick="requestPushPermission(); fetchCompanyNotifications();">
                            <i class="fa-solid fa-bell text-warning" style="font-size: 0.95rem;"></i>
                            <span class="badge bg-danger notification-badge" id="companyNotifBadge" style="{{ $stafoUnreadNotifCount > 0 ? '' : 'display: none;' }}">
                                {{ $stafoUnreadNotifCount > 99 ? '99+' : $stafoUnreadNotifCount }}
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg mt-2 topbar-notification-menu" aria-labelledby="companyNotifDropdownBtn">
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light bg-opacity-50">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-bell text-warning"></i>
                                    <span class="fw-bold text-dark" style="font-size: 0.88rem;">Live Notifications</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill" id="companyNotifHeaderCount" style="{{ $stafoUnreadNotifCount > 0 ? '' : 'display: none;' }}">
                                        {{ $stafoUnreadNotifCount }} new
                                    </span>
                                </div>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted" style="font-size: 0.72rem;" onclick="markAllNotificationsReadDropdown(event)">
                                    <i class="fa-solid fa-check-double me-1"></i>Mark all read
                                </button>
                            </div>
                            <div id="companyNotifListContainer" style="max-height: 360px; overflow-y: auto;">
                                @forelse ($stafoInitialNotifications as $notif)
                                    @php
                                        $msg = strtolower($notif->message ?? '');
                                        $url = route('user.dashboard');
                                        $icon = 'fa-solid fa-bell text-primary';
                                        if (str_contains($msg, 'punch-out') || str_contains($msg, 'regularisation') || str_contains($msg, 'regularization')) {
                                            $url = route('user.attendanceRequests.index');
                                            $icon = 'fa-solid fa-business-time text-warning';
                                        } elseif (str_contains($msg, 'punch in') || str_contains($msg, 'punched in')) {
                                            $url = route('attendance.index');
                                            $icon = 'fa-solid fa-arrow-right-to-bracket text-success';
                                        } elseif (str_contains($msg, 'punch out') || str_contains($msg, 'punched out')) {
                                            $url = route('attendance.index');
                                            $icon = 'fa-solid fa-arrow-right-from-bracket text-danger';
                                        } elseif (str_contains($msg, 'leave')) {
                                            $url = route('leaveList');
                                            $icon = 'fa-solid fa-calendar-minus text-warning';
                                        } elseif (str_contains($msg, 'location') || str_contains($msg, 'tracking') || str_contains($msg, 'geo')) {
                                            $url = route('employee.location');
                                            $icon = 'fa-solid fa-location-dot text-info';
                                        } elseif (str_contains($msg, 'expense') || str_contains($msg, 'claim')) {
                                            $url = route('expenseList');
                                            $icon = 'fa-solid fa-receipt text-success';
                                        } elseif (str_contains($msg, 'device')) {
                                            $url = route('deviceList');
                                            $icon = 'fa-solid fa-fingerprint text-primary';
                                        }
                                        $isUnread = $notif->status === 'unread';
                                    @endphp
                                    <a href="{{ $url }}" class="notif-item {{ $isUnread ? 'unread' : '' }}" onclick="markSingleReadDropdown({{ $notif->id }})">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-light" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                            <i class="{{ $icon }}"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="small fw-semibold text-dark {{ $isUnread ? 'fw-bold' : '' }}" style="line-height: 1.35; font-size: 0.82rem;">
                                                {{ $notif->message }}
                                            </div>
                                            <div class="text-muted small d-flex align-items-center justify-content-between mt-1" style="font-size: 0.72rem;">
                                                <span>{{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Just now' }}</span>
                                                @if ($isUnread)
                                                    <span class="badge bg-danger px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem;">NEW</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-4 text-muted small">
                                        <i class="fa-solid fa-bell-slash fs-4 d-block mb-1 opacity-50"></i>
                                        No notifications yet
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-2 border-top bg-light bg-opacity-50 text-center">
                                <a href="{{ route('user.notifications.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold" style="font-size: 0.78rem;">
                                    View All Notifications <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dark / Night Mode Toggle Button -->
                    <button class="btn btn-sm btn-outline-light border-0 d-flex align-items-center gap-1 theme-toggle-btn px-2 py-1" 
                            id="themeToggleBtn" type="button" onclick="toggleTheme()" 
                            title="Toggle Light / Dark Mode" 
                            style="background: rgba(255,255,255,0.08); border-radius: 8px; height: 34px;">
                        <i class="fa-solid fa-moon text-info" id="themeToggleIcon" style="font-size: 0.9rem; transition: transform 0.3s ease;"></i>
                        <span class="d-none d-lg-inline text-light" style="font-size: 0.8rem;" id="themeToggleText">Dark</span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown user">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (Auth::user()->image_name && file_exists(public_path('uploads/compnay_logo/' . Auth::user()->image_name)))
                                <img src="{{ asset('uploads/compnay_logo/' . Auth::user()->image_name) }}"
                                    alt="Company Logo" class="user-avatar-top" />
                            @else
                                <div class="user-initials-top">
                                    {{ strtoupper(substr(Auth::user()->company_name ?? 'C', 0, 2)) }}
                                </div>
                            @endif
                            <div class="d-none d-sm-block text-start lh-1">
                                <span class="text-capitalize fw-bold d-block text-white" style="font-size: 0.85rem;">{{ Str::limit(Auth::user()->company_name, 18) }}</span>
                                <small class="text-success fw-semibold" style="font-size: 0.7rem;"><span class="user-status-dot me-1"></span>Online</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2 topbar-dropdown-menu">
                            <li class="px-3 py-2 border-bottom">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.875rem;">{{ Auth::user()->company_name }}</span>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ Auth::user()->email }}</small>
                            </li>
                            <li>
                                <a href="{{ route('company.profile.edit') }}" class="dropdown-item py-2 mt-1">
                                    <i class="fa-solid fa-user-gear text-primary me-2"></i> Company Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('referralList') }}" class="dropdown-item py-2">
                                    <i class="fa-solid fa-gift text-warning me-2"></i> My Referrals
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('company.feedback') }}" class="dropdown-item py-2">
                                    <i class="fa-solid fa-comment-dots text-info me-2"></i> Feedback
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="toggleTheme()" class="dropdown-item py-2 d-flex align-items-center justify-content-between">
                                    <span>
                                        <i class="fa-solid fa-circle-half-stroke text-primary me-2"></i> Theme Mode
                                    </span>
                                    <span class="badge bg-light text-muted border" id="dropdownThemeBadge" style="font-size: 0.7rem;">Light</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a href="{{ route('logout') }}" class="dropdown-item py-2 text-danger fw-semibold">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </header>

    <!-- Main Layout Container -->
    <div class="app-wrapper">
        
        <!-- Sidebar Navigation (Desktop & Mobile) -->
        @include('user.layouts.sidebar')

        <!-- Right Main Content Area -->
        <main class="main-content-wrapper">
            @yield('content')
        </main>

    </div>

    <!-- Core Scripts -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('main/js/bootstrap.bundle.min5.3.js') }}"></script>

    <!-- Sidebar Menu State Controller -->
    <script>
        $(document).ready(function () {
            // Clean up any stale menu cache from previous sessions
            try {
                sessionStorage.removeItem('stafo_active_menu');
            } catch (e) {}

            const currentUrl = window.location.href.split(/[?#]/)[0];
            const currentPath = window.location.pathname;

            // Highlight sub-items matching current URL exactly or precise route prefix
            $('.sidebar-sub-item').each(function () {
                const href = $(this).attr('href');
                if (!href || href === '#' || href === 'javascript:void(0)') return;

                const linkUrl = href.split(/[?#]/)[0];
                const linkPath = new URL(href, window.location.origin).pathname;

                if (currentUrl === linkUrl || currentPath === linkPath) {
                    $(this).addClass('active');
                } else if (linkPath.length > 9 && currentPath.startsWith(linkPath + '/')) {
                    $(this).addClass('active');
                }
            });

            // If on employees-show or employee edit/documents, activate Employees List item
            if (currentPath.includes('employees-show') || currentPath.includes('employee/') || currentPath.includes('employee-create')) {
                $('a[href*="company/employee"]').first().addClass('active');
            }

            // Ensure parent collapse of active sub-item is shown and parent nav marked active
            function syncParentActiveMenus() {
                const $activeSubItems = $('.sidebar-sub-item.active');
                if ($activeSubItems.length) {
                    $activeSubItems.each(function () {
                        const parentCollapse = $(this).closest('.collapse');
                        if (parentCollapse.length) {
                            parentCollapse.addClass('show');
                            const collapseId = parentCollapse.attr('id');
                            $(`[data-bs-target="#${collapseId}"]`)
                                .addClass('active active-parent')
                                .removeClass('collapsed')
                                .attr('aria-expanded', 'true');
                        }
                    });
                }
            }

            syncParentActiveMenus();

            // When a standalone nav item (not an accordion toggle) is clicked,
            // immediately close all open collapses so no other menu stays open
            $('.sidebar-nav-item:not([data-bs-toggle="collapse"])').on('click', function () {
                $('.collapse.show').collapse('hide');
                $('.sidebar-nav-item[data-bs-toggle="collapse"]')
                    .addClass('collapsed')
                    .removeClass('active active-parent')
                    .attr('aria-expanded', 'false');
            });
        });

        // Dark / Night Mode Controller
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            if (document.body) {
                document.body.setAttribute('data-theme', newTheme);
                document.body.setAttribute('data-bs-theme', newTheme);
            }
            localStorage.setItem('stafo_theme', newTheme);
            updateThemeUI(newTheme);
            window.dispatchEvent(new CustomEvent('stafoThemeChanged', { detail: { theme: newTheme } }));
        }

        function updateThemeUI(theme) {
            const icon = document.getElementById('themeToggleIcon');
            const text = document.getElementById('themeToggleText');
            const btn = document.getElementById('themeToggleBtn');
            const badge = document.getElementById('dropdownThemeBadge');
            const mobileIcons = document.querySelectorAll('.mobile-theme-icon');

            if (theme === 'dark') {
                if (icon) {
                    icon.className = 'fa-solid fa-sun text-warning';
                    icon.style.transform = 'rotate(180deg)';
                }
                if (text) text.textContent = 'Light';
                if (btn) btn.setAttribute('title', 'Switch to Light Mode');
                if (badge) {
                    badge.textContent = 'Dark';
                    badge.className = 'badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25';
                }
                mobileIcons.forEach(el => {
                    el.className = 'fa-solid fa-sun text-warning mobile-theme-icon';
                });
            } else {
                if (icon) {
                    icon.className = 'fa-solid fa-moon text-info';
                    icon.style.transform = 'rotate(0deg)';
                }
                if (text) text.textContent = 'Dark';
                if (btn) btn.setAttribute('title', 'Switch to Dark / Night Mode');
                if (badge) {
                    badge.textContent = 'Light';
                    badge.className = 'badge bg-light text-muted border';
                }
                mobileIcons.forEach(el => {
                    el.className = 'fa-solid fa-moon text-info mobile-theme-icon';
                });
            }
        }

        // Initialize UI on load
        document.addEventListener('DOMContentLoaded', function () {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            if (document.body) {
                document.body.setAttribute('data-theme', currentTheme);
                document.body.setAttribute('data-bs-theme', currentTheme);
            }
            updateThemeUI(currentTheme);

            // Start Company Real-Time Notification Engine
            initCompanyNotificationEngine();
        });

        // ========================================================
        // Company Real-Time Push Notification Engine
        // ========================================================
        let lastSeenNotifId = {{ $stafoLatestId ?? 0 }};
        let isFirstNotifLoad = true;

        function requestPushPermission() {
            if ('Notification' in window && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        }

        function playNotificationChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const audioCtx = new AudioCtx();
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.12); // A5
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.35);
            } catch (e) {}
        }

        function showBrowserPush(title, message, targetUrl) {
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const notif = new Notification(title, {
                        body: message,
                        icon: "{{ asset('main/images/logo.png') }}",
                        badge: "{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}"
                    });
                    notif.onclick = function() {
                        window.focus();
                        if (targetUrl) window.location.href = targetUrl;
                        this.close();
                    };
                } catch (e) {}
            }
        }

        function showFloatingToast(message, timeAgo, targetUrl, iconClass) {
            const container = document.getElementById('stafoNotificationToastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'stafo-toast';
            toast.innerHTML = `
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-primary bg-opacity-20 text-success" style="width: 38px; height: 38px; font-size: 1rem;">
                    <i class="${iconClass || 'fa-solid fa-bell'}"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold mb-0.5 text-white" style="font-size: 0.85rem;">Employee Activity</div>
                    <div class="text-light-50 small text-truncate" style="font-size: 0.78rem;">${message}</div>
                    <small class="text-secondary opacity-75" style="font-size: 0.7rem;">${timeAgo}</small>
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.65rem;" onclick="event.stopPropagation(); this.parentElement.remove();"></button>
            `;

            toast.onclick = function() {
                if (targetUrl) window.location.href = targetUrl;
            };

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.35s ease';
                setTimeout(() => toast.remove(), 400);
            }, 6000);
        }

        function fetchCompanyNotifications() {
            fetch("{{ route('user.notifications.feed') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    const count = data.unread_count || 0;
                    const badge = document.getElementById('companyNotifBadge');
                    const headerCount = document.getElementById('companyNotifHeaderCount');
                    const listContainer = document.getElementById('companyNotifListContainer');

                    // Update badge
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }

                    if (headerCount) {
                        if (count > 0) {
                            headerCount.textContent = count + ' new';
                            headerCount.style.display = 'inline-block';
                        } else {
                            headerCount.style.display = 'none';
                        }
                    }

                    // Check for brand-new notification arrived in real-time
                    if (data.notifications && data.notifications.length > 0) {
                        const newest = data.notifications[0];
                        if (!isFirstNotifLoad && lastSeenNotifId && newest.id > lastSeenNotifId && newest.status === 'unread') {
                            playNotificationChime();
                            showFloatingToast(newest.message, newest.time_ago, newest.url, newest.icon);
                            showBrowserPush("STAFO HRMS Alert", newest.message, newest.url);
                        }
                        lastSeenNotifId = newest.id;
                    }

                    isFirstNotifLoad = false;

                    // Render list inside dropdown
                    if (listContainer) {
                        if (!data.notifications || data.notifications.length === 0) {
                            listContainer.innerHTML = `
                                <div class="text-center py-4 text-muted small">
                                    <i class="fa-solid fa-bell-slash fs-4 d-block mb-1 opacity-50"></i>
                                    No notifications yet
                                </div>
                            `;
                        } else {
                            let html = '';
                            data.notifications.forEach(item => {
                                const isUnread = item.status === 'unread';
                                html += `
                                    <a href="${item.url}" class="notif-item ${isUnread ? 'unread' : ''}" onclick="markSingleReadDropdown(${item.id})">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-light" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                            <i class="${item.icon}"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="small fw-semibold text-dark ${isUnread ? 'fw-bold' : ''}" style="line-height: 1.35; font-size: 0.82rem;">
                                                ${item.message}
                                            </div>
                                            <div class="text-muted small d-flex align-items-center justify-content-between mt-1" style="font-size: 0.72rem;">
                                                <span>${item.time_ago}</span>
                                                ${isUnread ? '<span class="badge bg-danger px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem;">NEW</span>' : ''}
                                            </div>
                                        </div>
                                    </a>
                                `;
                            });
                            listContainer.innerHTML = html;
                        }
                    }
                }
            })
            .catch(err => {});
        }

        function markAllNotificationsReadDropdown(e) {
            if (e) e.stopPropagation();
            fetch("{{ route('user.notifications.markRead', 'all') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    fetchCompanyNotifications();
                }
            });
        }

        function markSingleReadDropdown(id) {
            fetch("{{ url('company/notifications/mark-read') }}/" + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(e => {});
        }

        function initCompanyNotificationEngine() {
            fetchCompanyNotifications();
            // Poll every 12 seconds for real-time live employee activity updates
            if (!window._companyNotifInterval) {
                window._companyNotifInterval = setInterval(fetchCompanyNotifications, 12000);
            }
        }

        // Auto-start notification engine immediately on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCompanyNotificationEngine);
        } else {
            initCompanyNotificationEngine();
        }
    </script>

    <!-- Floating Toast Notification Container -->
    <div id="stafoNotificationToastContainer"></div>

    @yield('js')
</body>

</html>