<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Dashboard | STAFO HRMS')</title>
    <link rel="shortcut icon" href="{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap.min-5.3.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}?v={{ time() }}">
    
    <!-- Immediate Theme Setup (Prevents FOUC) -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('stafo_theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <style>
        :root {
            --stafo-header-height: 58px;
            --stafo-primary: #059669;
            --stafo-primary-hover: #047857;
            --stafo-primary-light: rgba(16, 185, 129, 0.12);
            --stafo-accent-cyan: #0284c7;
            --stafo-brand-gradient: linear-gradient(135deg, #10b981 0%, #059669 45%, #0284c7 100%);
            --stafo-brand-gradient-hover: linear-gradient(135deg, #059669 0%, #047857 45%, #0369a1 100%);
            --stafo-body-bg: #f8fafc;
            --stafo-sidebar-bg: #ffffff;
            --stafo-card-bg: #ffffff;
            --stafo-card-border: #e2e8f0;
            --stafo-text-dark: #0f172a;
            --stafo-text-muted: #64748b;
        }

        /* Primary Button STAFO Theme */
        .btn-primary {
            background: var(--stafo-brand-gradient) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.28);
            font-weight: 600;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--stafo-brand-gradient-hover) !important;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
            color: #ffffff !important;
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        .theme-toggle-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: scale(1.05);
        }

        header.top-header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: var(--stafo-header-height) !important;
            z-index: 1030 !important;
            background: linear-gradient(135deg, #091224 0%, #0d1e38 50%, #062b19 100%) !important;
            box-shadow: 0 4px 12px -2px rgba(9, 18, 36, 0.25) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        header.top-header .container-fluid,
        header.top-header .container-fluid > div {
            height: 100% !important;
        }

        .app-wrapper {
            display: flex !important;
            min-height: calc(100vh - var(--stafo-header-height)) !important;
            margin-top: var(--stafo-header-height) !important;
            background-color: var(--stafo-body-bg, #f8fafc) !important;
        }

        @media (min-width: 992px) {
            aside.sidebar {
                position: fixed !important;
                top: var(--stafo-header-height) !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 250px !important;
                height: calc(100vh - var(--stafo-header-height)) !important;
                overflow-y: auto !important;
                z-index: 1020 !important;
                border-right: 1px solid rgba(226, 232, 240, 0.8) !important;
                background-color: var(--stafo-sidebar-bg, #ffffff) !important;
                transition: left 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            body.sidebar-collapsed aside.sidebar {
                left: -250px !important;
            }

            main.app-content {
                margin-left: 250px !important;
                width: calc(100% - 250px) !important;
                min-height: calc(100vh - var(--stafo-header-height)) !important;
                padding: 24px 28px 48px 28px !important;
                transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            body.sidebar-collapsed main.app-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        @media (max-width: 991.98px) {
            main.app-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 16px 14px 32px 14px !important;
            }
            aside.sidebar {
                position: fixed !important;
                top: var(--stafo-header-height) !important;
                bottom: 0 !important;
                left: -280px !important;
                width: 280px !important;
                height: calc(100vh - var(--stafo-header-height)) !important;
                overflow-y: auto !important;
                z-index: 1040 !important;
                background-color: #ffffff !important;
                transition: left 0.3s ease-in-out !important;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1) !important;
            }
            aside.sidebar.mobile-open {
                left: 0 !important;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: var(--stafo-header-height);
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.5);
                z-index: 1035;
                backdrop-filter: blur(2px);
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }

        /* Custom Scrollbar for Sidebar */
        aside.sidebar::-webkit-scrollbar {
            width: 5px;
        }
        aside.sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        aside.sidebar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.25);
            border-radius: 10px;
        }
        aside.sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.45);
        }

        /* Category Header in Sidebar */
        .sidebar-category-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            padding: 3px 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .sidebar-category-label .indicator-pill {
            width: 3px;
            height: 11px;
            background: #10b981;
            border-radius: 3px;
            display: inline-block;
        }

        /* Nav Item Styling */
        .employee-nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 3px;
            position: relative;
        }
        .employee-nav-item .nav-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.88rem;
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .employee-nav-item .nav-icon i {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", sans-serif !important;
            font-weight: 900 !important;
            font-style: normal !important;
            line-height: 1;
        }
        .employee-nav-item:hover {
            color: #059669;
            background-color: rgba(16, 185, 129, 0.08);
            transform: translateX(3px);
        }
        .employee-nav-item:hover .nav-icon {
            background-color: rgba(16, 185, 129, 0.16);
            color: #059669;
        }
        .employee-nav-item.active {
            color: #ffffff !important;
            background: var(--stafo-brand-gradient) !important;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }
        .employee-nav-item.active .nav-icon {
            background-color: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
        }
        .employee-nav-item.has-submenu:not(.collapsed) {
            color: #059669;
            background-color: rgba(16, 185, 129, 0.06);
            font-weight: 600;
        }
        .employee-nav-item.has-submenu:not(.collapsed) .nav-icon {
            background-color: rgba(16, 185, 129, 0.15);
            color: #059669;
        }
        .employee-nav-item .submenu-chevron {
            font-size: 0.68rem;
            color: #94a3b8;
            margin-left: auto;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s ease;
        }
        .employee-nav-item.has-submenu:not(.collapsed) .submenu-chevron {
            transform: rotate(180deg);
            color: #059669;
        }

        /* Submenu Styling */
        .employee-submenu-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 5px;
            margin: 2px 0 6px 12px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        .employee-sub-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 6.5px 10px;
            border-radius: 7px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            transition: all 0.18s ease;
            margin-bottom: 2px;
        }
        .employee-sub-item:last-child {
            margin-bottom: 0;
        }
        .employee-sub-item .sub-icon-pill {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            background-color: #ffffff;
            color: #64748b;
            border: 1px solid #e2e8f0;
            transition: all 0.18s ease;
            flex-shrink: 0;
        }
        .employee-sub-item .sub-icon-pill i {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", sans-serif !important;
            font-weight: 900 !important;
            font-style: normal !important;
            line-height: 1;
        }
        .employee-sub-item:hover {
            color: #059669;
            background-color: rgba(16, 185, 129, 0.08);
            transform: translateX(3px);
        }
        .employee-sub-item:hover .sub-icon-pill {
            background-color: #059669;
            color: #ffffff;
            border-color: #059669;
        }
        .employee-sub-item.active {
            color: #059669;
            background-color: rgba(16, 185, 129, 0.12);
            font-weight: 600;
        }
        .employee-sub-item.active .sub-icon-pill {
            background: var(--stafo-brand-gradient);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);
        }

        /* Log out Item */
        .logout-btn:hover {
            color: #dc2626 !important;
            background-color: rgba(239, 68, 68, 0.08) !important;
        }
        .logout-btn:hover .nav-icon {
            background-color: rgba(239, 68, 68, 0.18) !important;
            color: #dc2626 !important;
        }

        .user-initials-top {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #0284c7);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .user-avatar-top {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* ========================================================
           Dark Mode Comprehensive Contrast & Visibility Engine
           ======================================================== */
        /* ========================================================
           Contrast & Theme Visibility Engine (Light & Dark)
           ======================================================== */
        /* Universal Badge Contrast in Light Mode */
        .badge.bg-success, .badge.text-success {
            background-color: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
            font-weight: 600;
        }
        .badge.bg-warning, .badge.text-warning {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
            font-weight: 600;
        }
        .badge.bg-danger, .badge.text-danger {
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
            font-weight: 600;
        }
        .badge.bg-info, .badge.text-info {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd !important;
            font-weight: 600;
        }
        .badge.bg-secondary, .badge.text-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 600;
        }
        .badge.bg-primary, .badge.text-primary {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
            border: 1px solid #a7f3d0 !important;
            font-weight: 600;
        }

        /* Punch terminal card and not punched indicator in Light Mode */
        .emp-punch-terminal-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }
        .emp-not-punched-box {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        .emp-terminal-badge {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 9999px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        /* Banner chip utilities (Crisp glassmorphism in both modes) */
        .emp-banner-chip {
            background: rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 9999px !important;
            padding: 3px 12px !important;
            font-size: 0.76rem !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }
        .emp-banner-chip * {
            color: #ffffff;
        }
        .emp-banner-chip i.text-info {
            color: #38bdf8 !important;
        }
        .emp-banner-chip i.text-warning {
            color: #fde047 !important;
        }
        .emp-banner-chip i.text-success {
            color: #86efac !important;
        }

        .emp-portal-pill {
            background: #ffffff;
            color: #064e3b;
            border-radius: 9999px;
            padding: 5px 14px;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .emp-hero-btn-light {
            background-color: #ffffff !important;
            color: #064e3b !important;
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .emp-hero-btn-light:hover {
            background-color: #f1f5f9 !important;
            color: #047857 !important;
            transform: translateY(-1px);
        }

        /* Punch pill indicator in Hero Banner */
        .punch-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .punch-pill-warning {
            background: rgba(245, 158, 11, 0.22) !important;
            color: #fef08a !important;
            border: 1px solid rgba(245, 158, 11, 0.45) !important;
        }
        .punch-pill-warning i {
            color: #fbbf24 !important;
        }
        .punch-pill-success {
            background: rgba(16, 185, 129, 0.22) !important;
            color: #a7f3d0 !important;
            border: 1px solid rgba(16, 185, 129, 0.45) !important;
        }
        .punch-pill-success i {
            color: #34d399 !important;
        }

        /* ========================================================
           Dark Mode Comprehensive Contrast & Visibility Engine
           ======================================================== */
        [data-theme="dark"] {
            --stafo-body-bg: #090e1a;
            --stafo-sidebar-bg: #0f172a;
            --stafo-card-bg: #111c30;
            --stafo-card-border: rgba(255, 255, 255, 0.08);
            --stafo-text-dark: #f8fafc;
            --stafo-text-muted: #94a3b8;
            --bs-body-color: #f8fafc;
            --bs-body-bg: #090e1a;
            --bs-emphasis-color: #ffffff;
            --bs-secondary-color: #94a3b8;
            --bs-table-color: #e2e8f0;
            --bs-table-bg: transparent;
            color-scheme: dark;
        }

        [data-theme="dark"] body {
            background-color: #090e1a !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .app-wrapper {
            background-color: #090e1a !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] main.app-content {
            color: #f8fafc !important;
        }

        [data-theme="dark"] aside.sidebar {
            background-color: #0f172a !important;
            border-right-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-theme="dark"] .sidebar-category-label {
            color: #64748b;
        }

        [data-theme="dark"] .employee-nav-item {
            color: #94a3b8;
        }

        [data-theme="dark"] .employee-nav-item .nav-icon {
            background-color: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
        }

        [data-theme="dark"] .employee-nav-item:hover {
            color: #34d399;
            background-color: rgba(255, 255, 255, 0.06);
        }

        [data-theme="dark"] .employee-nav-item:hover .nav-icon {
            background-color: rgba(52, 211, 153, 0.15);
            color: #34d399;
        }

        [data-theme="dark"] .employee-nav-item.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 40%, #0284c7 100%) !important;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);
        }

        [data-theme="dark"] .employee-nav-item.active .nav-icon {
            background-color: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .employee-nav-item.has-submenu:not(.collapsed) {
            color: #34d399;
            background-color: rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] .employee-nav-item.has-submenu:not(.collapsed) .nav-icon {
            background-color: rgba(52, 211, 153, 0.2);
            color: #34d399;
        }

        [data-theme="dark"] .employee-submenu-box {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }

        [data-theme="dark"] .employee-sub-item {
            color: #94a3b8;
        }

        [data-theme="dark"] .employee-sub-item .sub-icon-pill {
            background-color: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }

        [data-theme="dark"] .employee-sub-item:hover {
            color: #34d399;
            background-color: rgba(255, 255, 255, 0.06);
        }

        [data-theme="dark"] .employee-sub-item:hover .sub-icon-pill {
            background-color: #059669;
            color: #ffffff;
            border-color: #059669;
        }

        [data-theme="dark"] .employee-sub-item.active {
            color: #34d399;
            background-color: rgba(16, 185, 129, 0.15);
        }

        [data-theme="dark"] .employee-sub-item.active .sub-icon-pill {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border-color: transparent;
        }

        [data-theme="dark"] .logout-btn:hover {
            color: #f87171 !important;
            background-color: rgba(239, 68, 68, 0.12) !important;
        }

        /* Headings, Displays, Paragraphs, and Strong Text */
        [data-theme="dark"] h1, 
        [data-theme="dark"] h2, 
        [data-theme="dark"] h3, 
        [data-theme="dark"] h4, 
        [data-theme="dark"] h5, 
        [data-theme="dark"] h6,
        [data-theme="dark"] .text-dark,
        [data-theme="dark"] strong,
        [data-theme="dark"] b,
        [data-theme="dark"] .display-1,
        [data-theme="dark"] .display-2,
        [data-theme="dark"] .display-3,
        [data-theme="dark"] .display-4,
        [data-theme="dark"] .display-5,
        [data-theme="dark"] .display-6 {
            color: #f8fafc !important;
        }

        /* Muted and Secondary Texts */
        [data-theme="dark"] .text-muted,
        [data-theme="dark"] small.text-muted,
        [data-theme="dark"] span.text-muted,
        [data-theme="dark"] div.text-muted,
        [data-theme="dark"] p.text-muted {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .text-secondary {
            color: #cbd5e1 !important;
        }

        [data-theme="dark"] .text-warning {
            color: #fbbf24 !important;
        }

        [data-theme="dark"] .text-success {
            color: #34d399 !important;
        }

        [data-theme="dark"] .text-danger {
            color: #f87171 !important;
        }

        [data-theme="dark"] .text-info {
            color: #38bdf8 !important;
        }

        [data-theme="dark"] .text-primary {
            color: #34d399 !important;
        }

        /* Links in Dark Mode */
        [data-theme="dark"] a:not(.btn):not(.dropdown-item):not(.employee-nav-item):not(.nav-link) {
            color: #38bdf8 !important;
        }
        [data-theme="dark"] a:not(.btn):not(.dropdown-item):not(.employee-nav-item):not(.nav-link):hover {
            color: #7dd3fc !important;
            text-decoration: underline !important;
        }
        [data-theme="dark"] .btn-link {
            color: #38bdf8 !important;
        }
        [data-theme="dark"] .btn-link:hover {
            color: #7dd3fc !important;
        }

        /* Outline Buttons in Dark Mode */
        [data-theme="dark"] .btn-outline-dark {
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        [data-theme="dark"] .btn-outline-dark:hover {
            color: #090e1a !important;
            background-color: #ffffff !important;
            border-color: #ffffff !important;
        }

        [data-theme="dark"] .btn-outline-primary {
            color: #34d399 !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
            background-color: rgba(16, 185, 129, 0.08) !important;
        }
        [data-theme="dark"] .btn-outline-primary:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-color: #10b981 !important;
        }

        [data-theme="dark"] .btn-outline-secondary {
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        [data-theme="dark"] .btn-outline-secondary:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
        }

        /* Hero Banner Apply Leave button in Dark Mode */
        [data-theme="dark"] .emp-hero-btn-light {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.35) !important;
            backdrop-filter: blur(8px) !important;
        }
        [data-theme="dark"] .emp-hero-btn-light:hover {
            background-color: #ffffff !important;
            color: #064e3b !important;
        }

        /* Cards & Surfaces in Dark Mode */
        [data-theme="dark"] .card,
        [data-theme="dark"] .emp-card,
        [data-theme="dark"] .emp-stat-card,
        [data-theme="dark"] .emp-punch-terminal-card,
        [data-theme="dark"] .modal-content {
            background: #111c30 !important;
            background-color: #111c30 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .card-header,
        [data-theme="dark"] .emp-card-header,
        [data-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-footer {
            background: #132038 !important;
            background-color: #132038 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .card-footer {
            background-color: #0d1527 !important;
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Background Utilities in Dark Mode */
        [data-theme="dark"] .bg-white {
            background-color: #111c30 !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .bg-light {
            background-color: #0d1527 !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-theme="dark"] .border,
        [data-theme="dark"] .border-bottom,
        [data-theme="dark"] .border-top,
        [data-theme="dark"] .border-end,
        [data-theme="dark"] .border-start {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Tables in Dark Mode - Exhaustive Contrast Coverage */
        [data-theme="dark"] .table {
            color: #e2e8f0 !important;
            --bs-table-color: #e2e8f0 !important;
            --bs-table-bg: transparent !important;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.04) !important;
            --bs-table-hover-color: #ffffff !important;
            --bs-table-border-color: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-theme="dark"] .table td,
        [data-theme="dark"] .table tbody td {
            color: #e2e8f0 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-theme="dark"] .table thead th,
        [data-theme="dark"] .table-light,
        [data-theme="dark"] thead.table-light,
        [data-theme="dark"] .table-light th,
        [data-theme="dark"] .table-light td {
            background-color: #16243f !important;
            color: #f8fafc !important;
            --bs-table-color: #f8fafc !important;
            --bs-table-bg: #16243f !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        [data-theme="dark"] .table-hover tbody tr:hover td {
            color: #ffffff !important;
        }

        /* Badges in Dark Mode - Vibrant Luminous Contrasts */
        [data-theme="dark"] .badge.bg-light {
            background-color: #16243f !important;
            color: #cbd5e1 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
        }

        [data-theme="dark"] .badge.bg-success,
        [data-theme="dark"] .badge.text-success {
            background-color: rgba(16, 185, 129, 0.2) !important;
            color: #86efac !important;
            border: 1px solid rgba(16, 185, 129, 0.35) !important;
        }

        [data-theme="dark"] .badge.bg-warning,
        [data-theme="dark"] .badge.text-warning,
        [data-theme="dark"] .badge.bg-warning.text-dark {
            background-color: rgba(245, 158, 11, 0.22) !important;
            color: #fde047 !important;
            border: 1px solid rgba(245, 158, 11, 0.4) !important;
        }

        [data-theme="dark"] .badge.bg-danger,
        [data-theme="dark"] .badge.text-danger {
            background-color: rgba(239, 68, 68, 0.2) !important;
            color: #fca5a5 !important;
            border: 1px solid rgba(239, 68, 68, 0.35) !important;
        }

        [data-theme="dark"] .badge.bg-info,
        [data-theme="dark"] .badge.text-info {
            background-color: rgba(2, 132, 199, 0.2) !important;
            color: #7dd3fc !important;
            border: 1px solid rgba(2, 132, 199, 0.35) !important;
        }

        [data-theme="dark"] .badge.bg-secondary,
        [data-theme="dark"] .badge.text-secondary {
            background-color: rgba(148, 163, 184, 0.2) !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
        }

        [data-theme="dark"] .badge.bg-primary,
        [data-theme="dark"] .badge.text-primary {
            background-color: rgba(16, 185, 129, 0.22) !important;
            color: #6ee7b7 !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
        }

        [data-theme="dark"] .badge.bg-primary.text-white {
            background-color: #059669 !important;
            color: #ffffff !important;
        }

        /* Alerts in Dark Mode */
        [data-theme="dark"] .alert-success {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #a7f3d0 !important;
            border: 1px solid rgba(16, 185, 129, 0.35) !important;
        }
        [data-theme="dark"] .alert-danger {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
            border: 1px solid rgba(239, 68, 68, 0.35) !important;
        }
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* List Groups in Dark Mode */
        [data-theme="dark"] .list-group-item {
            background-color: transparent !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Form Controls in Dark Mode */
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background-color: #0d1527 !important;
            border-color: #24344d !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25) !important;
            background-color: #111c30 !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .form-control::placeholder {
            color: #64748b !important;
        }

        [data-theme="dark"] .form-label {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .form-select option {
            background-color: #111c30 !important;
            color: #f8fafc !important;
        }

        /* Dropdowns in Dark Mode */
        .dropdown-menu {
            z-index: 1060 !important;
        }
        [data-theme="dark"] .dropdown-menu {
            background-color: #111c30 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
        }
        [data-theme="dark"] .dropdown-menu .dropdown-item {
            color: #cbd5e1 !important;
        }
        [data-theme="dark"] .dropdown-menu .dropdown-item:hover,
        [data-theme="dark"] .dropdown-menu .dropdown-item:focus {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #34d399 !important;
        }
        [data-theme="dark"] .dropdown-menu .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Accent Utilities in Dark Mode */
        [data-theme="dark"] .bg-primary.bg-opacity-10 {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }
        [data-theme="dark"] .progress {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        [data-theme="dark"] .progress-bar.bg-primary {
            background: linear-gradient(90deg, #10b981 0%, #0284c7 100%) !important;
        }

        [data-theme="dark"] .emp-punch-terminal-card {
            background: linear-gradient(135deg, #111c30 0%, #0d1527 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        [data-theme="dark"] .emp-not-punched-box {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
        }
        [data-theme="dark"] .emp-terminal-badge {
            background: rgba(16, 185, 129, 0.18) !important;
            color: #34d399 !important;
            border-color: rgba(16, 185, 129, 0.35) !important;
        }

        /* ========================================================
           STAFO Ultra-Modern Marquee Ticker Styles
           ======================================================== */
        .stafo-marquee-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .stafo-marquee-bar:hover {
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07);
            border-color: #cbd5e1;
        }

        .stafo-marquee-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #10b981 0%, #059669 45%, #0284c7 100%);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .stafo-marquee-pill .pulse-dot {
            width: 7px;
            height: 7px;
            background: #ffffff;
            border-radius: 50%;
            animation: marqueePulse 1.8s infinite;
        }

        @keyframes marqueePulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }

        .stafo-marquee-container {
            flex: 1;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .stafo-marquee-content {
            font-size: 0.85rem;
            color: #334155;
            font-weight: 500;
            cursor: pointer;
        }

        .marquee-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 0 8px;
            white-space: nowrap;
            transition: transform 0.2s ease;
        }

        .marquee-chip:hover {
            transform: translateY(-1px);
        }

        .marquee-chip.chip-holiday {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .marquee-chip.chip-attendance {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .marquee-chip.chip-birthday {
            background: rgba(168, 85, 247, 0.12);
            color: #7e22ce;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        .marquee-chip.chip-payroll {
            background: rgba(2, 132, 199, 0.12);
            color: #0369a1;
            border: 1px solid rgba(2, 132, 199, 0.3);
        }

        .marquee-chip.chip-policy {
            background: rgba(99, 102, 241, 0.12);
            color: #4338ca;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .marquee-chip.chip-task {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .marquee-divider {
            color: #cbd5e1;
            margin: 0 6px;
            font-weight: bold;
            font-size: 0.75rem;
        }

        /* Dark mode marquee styles */
        [data-theme="dark"] .stafo-marquee-bar {
            background: #111c30 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="dark"] .stafo-marquee-content {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .marquee-chip.chip-holiday {
            background: rgba(245, 158, 11, 0.2) !important;
            color: #fde047 !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
        }

        [data-theme="dark"] .marquee-chip.chip-attendance {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #86efac !important;
            border-color: rgba(16, 185, 129, 0.4) !important;
        }

        [data-theme="dark"] .marquee-chip.chip-birthday {
            background: rgba(168, 85, 247, 0.2) !important;
            color: #d8b4fe !important;
            border-color: rgba(168, 85, 247, 0.4) !important;
        }

        [data-theme="dark"] .marquee-chip.chip-payroll {
            background: rgba(2, 132, 199, 0.2) !important;
            color: #7dd3fc !important;
            border-color: rgba(2, 132, 199, 0.4) !important;
        }

        [data-theme="dark"] .marquee-chip.chip-policy {
            background: rgba(99, 102, 241, 0.2) !important;
            color: #a5b4fc !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
        }

        [data-theme="dark"] .marquee-chip.chip-task {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #fca5a5 !important;
            border-color: rgba(239, 68, 68, 0.4) !important;
        }

        [data-theme="dark"] .marquee-divider {
            color: rgba(255, 255, 255, 0.18) !important;
        }
    </style>
    @yield('css')
</head>

<body>
    <!-- Fixed Top Header -->
    <header class="top-header">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex align-items-center justify-content-between h-100">
                <!-- Left: Hamburger (Mobile) + Logo + Portal Badge -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-white p-0" id="mobileMenuBtn" type="button" aria-label="Toggle navigation" title="Toggle Sidebar">
                        <i class="fa-solid fa-bars-staggered fs-5"></i>
                    </button>

                    <a href="{{ route('employee.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                        <img src="{{ asset('main/images/logo.png') }}" alt="STAFO HRMS" height="32" class="d-inline-block rounded-2">
                    </a>

                    <span class="badge rounded-pill d-none d-sm-inline-flex align-items-center gap-1.5 px-3 py-1 text-white border"
                          style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4) !important; color: #a7f3d0 !important; font-size: 0.74rem; letter-spacing: 0.3px;">
                        <i class="fa-solid fa-id-card-clip text-success"></i> Employee Portal
                    </span>
                </div>

                <!-- Right: Company Affiliation, Theme Toggle, Profile Menu -->
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    @php
                        $empUser = Auth::guard('employee')->user();
                    @endphp

                    @if($empUser && $empUser->company)
                        <div class="d-none d-md-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.08); font-size: 0.78rem;">
                            <i class="fa-solid fa-building text-warning" style="font-size: 0.75rem;"></i>
                            <span class="text-white-50">Org:</span>
                            <span class="text-white fw-semibold">{{ Str::limit($empUser->company->company_name, 22) }}</span>
                        </div>
                    @endif

                    <!-- Dark / Light Theme Toggle -->
                    <button class="btn btn-sm btn-outline-light border-0 d-flex align-items-center gap-1.5 theme-toggle-btn px-2.5 py-1" 
                            id="themeToggleBtn" type="button" onclick="toggleTheme()" 
                            title="Toggle Light / Dark Mode" 
                            style="background: rgba(255,255,255,0.08); border-radius: 9999px; height: 34px;">
                        <i class="fa-solid fa-moon text-info" id="themeToggleIcon" style="font-size: 0.9rem; transition: transform 0.3s ease;"></i>
                        <span class="d-none d-lg-inline text-light" style="font-size: 0.8rem;" id="themeToggleText">Dark</span>
                    </button>

                    <!-- Employee Profile Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none text-white" 
                           href="javascript:void(0)" 
                           role="button" 
                           id="employeeUserDropdown" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false" 
                           style="cursor: pointer;">
                            @if ($empUser && $empUser->image_url)
                                <img src="{{ $empUser->image_url }}" alt="{{ $empUser->name }}" class="user-avatar-top" />
                            @else
                                <div class="user-initials-top">
                                    {{ strtoupper(substr($empUser->name ?? 'E', 0, 1)) }}
                                </div>
                            @endif
                            <div class="d-none d-sm-block text-start lh-1">
                                <span class="text-capitalize fw-bold d-block text-white" style="font-size: 0.85rem;">{{ Str::limit($empUser->name ?? 'Employee', 16) }}</span>
                                <small class="text-info fw-semibold font-monospace" style="font-size: 0.7rem;">{{ $empUser->emp_id ?? 'STAFO ID' }}</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2" aria-labelledby="employeeUserDropdown" style="border-radius: 14px; border: 1px solid #e2e8f0; min-width: 230px;">
                            <li class="px-3 py-2.5 border-bottom">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.875rem;">{{ $empUser->name ?? 'Employee' }}</span>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ $empUser->email ?? $empUser->phone }}</small>
                                <span class="badge bg-success bg-opacity-10 text-success mt-1" style="font-size: 0.7rem;">{{ $empUser->position ?? 'Staff Member' }}</span>
                            </li>
                            <li>
                                <a href="{{ route('employee.profile') }}" class="dropdown-item py-2 mt-1">
                                    <i class="fa-solid fa-id-badge text-success me-2"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employee.attendance') }}" class="dropdown-item py-2">
                                    <i class="fa-solid fa-clipboard-user text-success me-2"></i> My Attendance
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employee.leaves') }}" class="dropdown-item py-2">
                                    <i class="fa-solid fa-calendar-days text-warning me-2"></i> My Leaves
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employee.salarySlips') }}" class="dropdown-item py-2">
                                    <i class="fa-solid fa-file-invoice-dollar text-info me-2"></i> Payslips
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

    <!-- App Wrapper -->
    <div class="app-wrapper">
        <!-- Mobile Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Left Sidebar Partial -->
        @include('employee.layouts.sidebar')

        <!-- Main Content View -->
        <main class="app-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center mb-3" role="alert">
                    <i class="fa-solid fa-circle-check fs-5 me-2 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center mb-3" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fs-5 me-2 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('main/js/bootstrap.bundle.min5.3.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize Bootstrap Dropdowns robustly
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
                dropdownElementList.forEach(function (dropdownToggleEl) {
                    new bootstrap.Dropdown(dropdownToggleEl);
                });
            }
        });

        // Sidebar Toggle (Mobile Offcanvas & Desktop Collapse to side)
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebarNav');
        const backdrop = document.getElementById('sidebarBackdrop');

        // Restore desktop collapsed state from preferences
        if (localStorage.getItem('stafo_sidebar_collapsed') === '1' && window.innerWidth >= 992) {
            document.body.classList.add('sidebar-collapsed');
        }

        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', function() {
                if (window.innerWidth >= 992) {
                    document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('stafo_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
                } else {
                    sidebar.classList.toggle('mobile-open');
                    if (backdrop) backdrop.classList.toggle('show');
                }
            });
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    backdrop.classList.remove('show');
                });
            }
        }

        // Dark / Night Mode Controller
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('stafo_theme', newTheme);
            updateThemeUI(newTheme);
        }

        function updateThemeUI(theme) {
            const icon = document.getElementById('themeToggleIcon');
            const text = document.getElementById('themeToggleText');
            if (theme === 'dark') {
                if (icon) {
                    icon.className = 'fa-solid fa-sun text-warning';
                    icon.style.transform = 'rotate(180deg)';
                }
                if (text) text.textContent = 'Light';
            } else {
                if (icon) {
                    icon.className = 'fa-solid fa-moon text-info';
                    icon.style.transform = 'rotate(0deg)';
                }
                if (text) text.textContent = 'Dark';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeUI(currentTheme);
        });
    </script>
    @yield('js')
</body>

</html>
