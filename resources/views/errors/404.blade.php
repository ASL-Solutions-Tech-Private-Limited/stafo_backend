<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | STAFO HRMS</title>
    <link rel="shortcut icon" href="{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap.min-5.3.css') }}">

    <style>
        :root {
            --stafo-emerald: #10b981;
            --stafo-emerald-dark: #059669;
            --stafo-cyan: #0284c7;
            --stafo-cyan-light: #38bdf8;
            --stafo-navy-dark: #090e1a;
            --stafo-navy-card: #111c30;
            --stafo-gradient: linear-gradient(135deg, #10b981 0%, #059669 45%, #0284c7 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--stafo-navy-dark);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Background Orbs */
        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            opacity: 0.35;
            z-index: 0;
            animation: orbFloat 14s ease-in-out infinite alternate;
        }
        .ambient-orb-1 {
            width: 450px;
            height: 450px;
            background: #10b981;
            top: -120px;
            left: -80px;
        }
        .ambient-orb-2 {
            width: 500px;
            height: 500px;
            background: #0284c7;
            bottom: -150px;
            right: -100px;
            animation-duration: 18s;
            animation-delay: -5s;
        }
        .ambient-orb-3 {
            width: 300px;
            height: 300px;
            background: #8b5cf6;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.15;
            animation-duration: 22s;
        }

        @keyframes orbFloat {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.08); }
            100% { transform: translateY(20px) scale(0.95); }
        }

        /* Subtle Geometric Grid Overlay */
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 1;
        }

        /* Top Minimal Header */
        .error-header {
            position: relative;
            z-index: 10;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .error-header .brand-logo img {
            height: 38px;
            width: auto;
            transition: transform 0.25s ease;
        }
        .error-header .brand-logo:hover img {
            transform: scale(1.04);
        }

        /* Main Container */
        .error-main-wrapper {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        /* Glassmorphic 404 Hero Card */
        .error-card {
            background: rgba(17, 28, 48, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 3.5rem 3rem;
            max-width: 780px;
            width: 100%;
            text-align: center;
            box-shadow: 0 24px 60px -12px rgba(0, 0, 0, 0.6),
                        0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--stafo-gradient);
        }

        /* 404 Big Animated Number */
        .error-number-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .error-number {
            font-size: clamp(6.5rem, 16vw, 10rem);
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #ffffff 20%, #a7f3d0 50%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 40px rgba(16, 185, 129, 0.25);
            user-select: none;
            display: inline-block;
        }

        /* Floating Radar Icon Badge in 0 */
        .floating-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(8px);
        }

        .floating-status-badge .dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ef4444;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: redPulse 2s infinite;
        }

        @keyframes redPulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .error-title {
            font-size: clamp(1.4rem, 3.5vw, 2rem);
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .error-desc {
            font-size: 1rem;
            color: #94a3b8;
            max-width: 540px;
            margin: 0 auto 2rem auto;
            line-height: 1.6;
        }

        /* Context Pills for Portal Mode */
        .context-pill-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Action Buttons */
        .btn-stafo-primary {
            background: var(--stafo-gradient);
            color: #ffffff !important;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 24px -4px rgba(16, 185, 129, 0.45);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }
        .btn-stafo-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -2px rgba(16, 185, 129, 0.6);
            color: #ffffff;
        }

        .btn-stafo-ghost {
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0 !important;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            text-decoration: none;
        }
        .btn-stafo-ghost:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        /* Quick Navigation Link Strip */
        .quick-links-strip {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .quick-links-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .quick-link-item {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .quick-link-item:hover {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
            transform: translateY(-1px);
        }

        /* Footer */
        .error-footer {
            position: relative;
            z-index: 10;
            padding: 1.5rem 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 576px) {
            .error-card {
                padding: 2.5rem 1.5rem;
                border-radius: 20px;
            }
            .error-header {
                padding: 1.25rem 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Ambient Background Lighting -->
    <div class="ambient-orb ambient-orb-1"></div>
    <div class="ambient-orb ambient-orb-2"></div>
    <div class="ambient-orb ambient-orb-3"></div>
    <div class="grid-overlay"></div>

    <!-- Top Minimal Header -->
    <header class="error-header">
        <a href="{{ url('/') }}" class="brand-logo text-decoration-none d-flex align-items-center gap-2">
            <img src="{{ asset('main/images/logo.png') }}" alt="STAFO HRMS" />
        </a>

        @php
            $isEmployee = request()->is('employee*') || \Illuminate\Support\Facades\Auth::guard('employee')->check();
            $isCompanyUser = \Illuminate\Support\Facades\Auth::check();
        @endphp

        <div class="d-flex align-items-center gap-2">
            @if($isEmployee)
                <a href="{{ route('employee.dashboard') }}" class="btn-stafo-ghost py-1.5 px-3 small">
                    <i class="fa-solid fa-gauge-high text-success"></i>
                    <span class="d-none d-sm-inline">Employee Dashboard</span>
                </a>
            @elseif($isCompanyUser)
                <a href="{{ route('user.dashboard') }}" class="btn-stafo-ghost py-1.5 px-3 small">
                    <i class="fa-solid fa-building text-info"></i>
                    <span class="d-none d-sm-inline">Company Dashboard</span>
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn-stafo-ghost py-1.5 px-3 small">
                    <i class="fa-solid fa-right-to-bracket text-success"></i>
                    <span class="d-none d-sm-inline">Sign In</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Main 404 Hero View -->
    <main class="error-main-wrapper">
        <div class="error-card">
            <!-- Dynamic Context Badge -->
            @if($isEmployee)
                <div class="context-pill-indicator">
                    <i class="fa-solid fa-id-card-clip"></i> Employee Portal Module
                </div>
            @elseif($isCompanyUser)
                <div class="context-pill-indicator" style="background: rgba(2, 132, 199, 0.15); border-color: rgba(2, 132, 199, 0.35); color: #7dd3fc;">
                    <i class="fa-solid fa-building"></i> Company HRMS Workspace
                </div>
            @else
                <div class="floating-status-badge">
                    <span class="dot-pulse"></span>
                    <span>HTTP 404 Error • Resource Missing</span>
                </div>
            @endif

            <!-- 404 Visual Numerals -->
            <div class="error-number-wrap">
                <div class="error-number">404</div>
            </div>

            <!-- Context-Aware Dynamic Headings & Subtitles -->
            @if($isEmployee)
                <h1 class="error-title">Employee Portal Page Not Found</h1>
                <p class="error-desc">
                    The employee module, attendance record, or requested action does not exist or has been shifted. Let's get you back to your workspace.
                </p>

                <!-- Primary Action Buttons for Employees -->
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                    <a href="{{ route('employee.dashboard') }}" class="btn-stafo-primary">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Return to Dashboard</span>
                    </a>
                    <button type="button" onclick="window.history.back()" class="btn-stafo-ghost">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Go Back</span>
                    </button>
                </div>

                <!-- Quick Employee Links -->
                <div class="quick-links-strip">
                    <div class="quick-links-title">Quick Employee Shortcuts</div>
                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                        <a href="{{ route('employee.attendance') }}" class="quick-link-item">
                            <i class="fa-solid fa-clipboard-user text-success"></i> Attendance & Punches
                        </a>
                        <a href="{{ route('employee.leaves') }}" class="quick-link-item">
                            <i class="fa-solid fa-calendar-days text-warning"></i> Leave Requests
                        </a>
                        <a href="{{ route('employee.tasks') }}" class="quick-link-item">
                            <i class="fa-solid fa-list-check text-info"></i> My Tasks
                        </a>
                        <a href="{{ route('employee.profile') }}" class="quick-link-item">
                            <i class="fa-solid fa-user text-success"></i> My Profile
                        </a>
                    </div>
                </div>

            @elseif($isCompanyUser)
                <h1 class="error-title">Company Portal Page Not Found</h1>
                <p class="error-desc">
                    The company management route, report, or configuration page you requested could not be located in your organization workspace.
                </p>

                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                    <a href="{{ route('user.dashboard') }}" class="btn-stafo-primary">
                        <i class="fa-solid fa-building"></i>
                        <span>Company Dashboard</span>
                    </a>
                    <button type="button" onclick="window.history.back()" class="btn-stafo-ghost">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Go Back</span>
                    </button>
                </div>

                <div class="quick-links-strip">
                    <div class="quick-links-title">Quick HRMS Shortcuts</div>
                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                        <a href="{{ route('attendance.index') }}" class="quick-link-item">
                            <i class="fa-solid fa-fingerprint text-success"></i> Live Attendance
                        </a>
                        <a href="{{ route('employee.index') }}" class="quick-link-item">
                            <i class="fa-solid fa-users text-info"></i> Staff Directory
                        </a>
                        <a href="{{ route('salarytype.index') }}" class="quick-link-item">
                            <i class="fa-solid fa-file-invoice-dollar text-warning"></i> Payroll
                        </a>
                        <a href="{{ route('shifts.index') }}" class="quick-link-item">
                            <i class="fa-solid fa-business-time text-success"></i> Shifts & Roaster
                        </a>
                    </div>
                </div>

            @else
                <h1 class="error-title">Oops! Page Lost in Space</h1>
                <p class="error-desc">
                    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable on the STAFO HRMS platform.
                </p>

                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                    <a href="{{ url('/') }}" class="btn-stafo-primary">
                        <i class="fa-solid fa-house"></i>
                        <span>Back to Homepage</span>
                    </a>
                    <a href="{{ url('/login') }}" class="btn-stafo-ghost">
                        <i class="fa-solid fa-arrow-right-to-bracket text-success"></i>
                        <span>Sign In / Login</span>
                    </a>
                </div>

                <div class="quick-links-strip">
                    <div class="quick-links-title">Popular Destinations</div>
                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                        <a href="{{ url('/about-us') }}" class="quick-link-item">
                            <i class="fa-solid fa-circle-info text-info"></i> About STAFO
                        </a>
                        <a href="{{ url('/price') }}" class="quick-link-item">
                            <i class="fa-solid fa-tags text-warning"></i> Pricing Plans
                        </a>
                        <a href="{{ url('/contact-us') }}" class="quick-link-item">
                            <i class="fa-solid fa-headset text-success"></i> Contact Support
                        </a>
                        <a href="{{ url('/login') }}" class="quick-link-item">
                            <i class="fa-solid fa-user-shield text-info"></i> Employee & Company Login
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="error-footer">
        <div class="container d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
            <div>
                &copy; {{ date('Y') }} STAFO HRMS. All rights reserved.
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" class="text-secondary text-decoration-none small hover-white">Home</a>
                <span>•</span>
                <a href="{{ url('/contact-us') }}" class="text-secondary text-decoration-none small hover-white">Help & Support</a>
                <span>•</span>
                <a href="{{ url('/login') }}" class="text-secondary text-decoration-none small hover-white">Portals</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Script -->
    <script src="{{ asset('main/js/bootstrap.bundle.min5.3.js') }}"></script>
</body>

</html>
