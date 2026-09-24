@extends('user.layouts.app')

@section('title', 'Dashboard | STAFO HRMS')

@section('css')
<style>
    /* Confetti floating animation for celebrations */
    @keyframes float-confetti {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(260px) rotate(360deg); opacity: 0; }
    }

    .confetti-piece {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        top: -10px;
        opacity: 0;
        pointer-events: none;
        animation: float-confetti 3.5s infinite linear;
    }
    .confetti-piece:nth-child(1) { left: 10%; background: #FFD700; animation-delay: 0.2s; }
    .confetti-piece:nth-child(2) { left: 30%; background: #FF69B4; animation-delay: 0.8s; }
    .confetti-piece:nth-child(3) { left: 55%; background: #00FFFF; animation-delay: 1.4s; }
    .confetti-piece:nth-child(4) { left: 75%; background: #7CFC00; animation-delay: 0.5s; }
    .confetti-piece:nth-child(5) { left: 90%; background: #FF4500; animation-delay: 1.9s; }

    .ratio-progress-bar {
        height: 6px;
        border-radius: 999px;
        background-color: #e2e8f0;
        overflow: hidden;
        margin-top: 10px;
    }
    .ratio-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .celebration-empty-state {
        background: var(--stafo-card-bg, #ffffff);
        border: 2px dashed var(--stafo-card-border, #cbd5e1);
        border-radius: var(--stafo-radius-lg);
        padding: 36px 20px;
        text-align: center;
        color: var(--stafo-text-muted, #475569);
    }

    /* HRMS Analytics Donut & Pie Chart Styles */
    .chart-card {
        background: var(--stafo-card-bg, #ffffff);
        border: 1px solid var(--stafo-card-border, #e2e8f0);
        border-radius: var(--stafo-radius-lg);
        box-shadow: var(--stafo-shadow-sm, 0 1px 3px 0 rgba(0, 0, 0, 0.05));
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    .chart-card:hover {
        box-shadow: var(--stafo-shadow-md, 0 10px 25px -5px rgba(15, 23, 42, 0.08));
        transform: translateY(-2px);
        border-color: #cbd5e1;
    }
    .chart-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--stafo-card-border, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--stafo-card-bg, #ffffff);
    }
    .chart-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        color: var(--stafo-text-dark, #0f172a);
        display: flex;
        align-items: center;
    }
    .chart-card-body {
        padding: 20px;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .chart-container-relative {
        position: relative;
        width: 100%;
        height: 200px;
        margin: 0 auto 12px auto;
    }
    .chart-center-metric {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }
    .chart-center-val {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.1;
        display: block;
    }
    .chart-center-sub {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--stafo-text-muted, #475569);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .chart-legend-list {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 6px;
        margin-bottom: 0;
        padding: 0;
        list-style: none;
    }
    .chart-legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 12px;
        background: var(--stafo-legend-bg, #f8fafc);
        border: 1px solid var(--stafo-card-border, #f1f5f9);
        border-radius: 8px;
        font-size: 0.8125rem;
        transition: background 0.15s ease;
    }
    .chart-legend-item:hover {
        background: var(--stafo-body-bg, #f1f5f9);
    }
    .chart-legend-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .chart-card-footer {
        padding: 12px 20px;
        background: var(--stafo-footer-bg, #f8fafc);
        border-top: 1px solid var(--stafo-card-border, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8125rem;
        color: var(--stafo-text-muted, #475569);
    }
    @keyframes live-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .live-pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        animation: live-pulse 2s infinite;
    }

    /* Executive Quick Launchpad (Zero-Scroll & Cross-Device Responsive) */
    .quick-launchpad {
        margin-bottom: 20px;
    }
    .quick-launchpad-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .quick-action-card {
        background: var(--stafo-card-bg, #ffffff);
        border: 1px solid var(--stafo-card-border, #e2e8f0);
        border-radius: 12px;
        padding: 11px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--stafo-text-dark, #0f172a);
        box-shadow: var(--stafo-shadow-xs, 0 1px 3px 0 rgba(0, 0, 0, 0.04));
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--stafo-shadow-md, 0 8px 18px -4px rgba(15, 23, 42, 0.08));
        border-color: #cbd5e1;
        color: var(--stafo-primary, #059669);
    }
    .quick-action-card .quick-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .quick-action-card:hover .quick-icon-box {
        transform: scale(1.1);
    }
    .quick-action-card .quick-title {
        font-size: 0.86rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--stafo-text-dark, #0f172a);
        display: block;
    }
    .quick-action-card .quick-sub {
        font-size: 0.71rem;
        color: var(--stafo-text-muted, #475569);
        display: block;
        margin-top: 2px;
    }

    /* Contrast enhancements for hero text */
    .dashboard-hero-banner .text-white-50 {
        color: rgba(255, 255, 255, 0.88) !important;
    }

    /* Mobile & Tablet Fine-Tuning */
    @media (max-width: 767.98px) {
        .dashboard-hero-banner {
            padding: 16px 18px !important;
            margin-bottom: 14px !important;
            border-radius: 12px !important;
        }
        .dashboard-hero-banner h2 {
            font-size: 1.2rem !important;
        }
        .dashboard-hero-banner p {
            font-size: 0.82rem !important;
        }
        .quick-launchpad {
            margin-bottom: 14px;
        }
        .quick-action-card {
            flex-direction: column;
            text-align: center;
            padding: 10px 4px;
            gap: 6px;
            border-radius: 10px;
        }
        .quick-action-card .quick-icon-box {
            width: 34px;
            height: 34px;
            font-size: 0.92rem;
        }
        .quick-action-card .quick-title {
            font-size: 0.74rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .quick-action-card .quick-sub {
            display: none;
        }
        .kpi-card {
            padding: 14px 16px !important;
            border-radius: 12px !important;
        }
        .kpi-value {
            font-size: 1.5rem !important;
        }
        .chart-container-relative {
            height: 180px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- Hero Welcome Banner -->
    <div class="dashboard-hero-banner">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold shadow-sm hero-pill-badge">
                        <i class="fa-solid fa-sparkles me-1 text-warning"></i> HRMS Workspace
                    </span>
                    <span class="text-white-50" style="font-size: 0.8125rem;">• {{ date('l, F j, Y') }}</span>
                </div>
                <h2 class="text-white mb-2 fw-bold">Welcome back, {{ Auth::user()->company_name }}! 👋</h2>
                <p class="text-white-50 mb-0" style="font-size: 0.9375rem; max-width: 600px;">
                    Here is a quick overview of your employee attendance, payroll status, and upcoming company events today.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('employee.index') }}" class="btn btn-hero-manage px-3 py-2 shadow-sm">
                    <i class="fa-solid fa-user-plus me-1 text-primary"></i> Manage Employees
                </a>
            </div>
        </div>
    </div>

    <!-- Company Live Notification & Announcement Marquee Ticker -->
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 50%, #06b6d4 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-bullhorn"></i>
            <span>HRMS Live Ticker</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                <!-- 1. Real-time Attendance Status -->
                <span class="marquee-chip chip-attendance">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Today's Attendance: <strong>{{ $presentCount }}</strong> Present out of <strong>{{ $employeeCount }}</strong> active staff</span>
                </span>
                <span class="marquee-divider">•</span>

                <!-- 2. Upcoming Holidays / Festivals -->
                @if(isset($upcomingHolidays) && $upcomingHolidays->count() > 0)
                    @foreach($upcomingHolidays as $uh)
                        @php
                            $uDate = \Carbon\Carbon::parse($uh->start_date);
                            $diff = \Carbon\Carbon::today()->diffInDays($uDate, false);
                            $fData = $uh->festival_data;
                        @endphp
                        <span class="marquee-chip chip-holiday">
                            <span>{{ $fData['emoji'] }}</span>
                            <span>Upcoming Holiday: <strong>{{ $uh->title }}</strong> on {{ $uDate->format('d M, Y') }} ({{ $uDate->format('l') }})</span>
                            @if($diff == 0)
                                <span class="badge bg-danger text-white ms-1">Today!</span>
                            @elseif($diff == 1)
                                <span class="badge bg-warning text-dark ms-1">Tomorrow</span>
                            @elseif($diff > 1)
                                <span class="badge bg-success text-white ms-1">In {{ $diff }} days</span>
                            @endif
                        </span>
                        <span class="marquee-divider">•</span>
                    @endforeach
                @endif

                <!-- 3. Birthday Celebrations -->
                @if($birthday->count() > 0)
                    <span class="marquee-chip chip-birthday">
                        <i class="fa-solid fa-cake-candles"></i>
                        <span>🎂 Birthdays This Month: <strong>{{ $birthday->pluck('name')->implode(', ') }}</strong> - Wish them a wonderful year!</span>
                    </span>
                    <span class="marquee-divider">•</span>
                @endif

                <!-- 4. Work Anniversaries -->
                @php
                    $upcomingSoon = $anniversary->where('days_until_anniversary', '<=', 30);
                @endphp
                @if($upcomingSoon->count() > 0)
                    <span class="marquee-chip chip-notice">
                        <i class="fa-solid fa-award"></i>
                        <span>🎉 Upcoming Work Anniversaries: 
                            @foreach($upcomingSoon->take(4) as $uEmp)
                                <strong>{{ $uEmp->name }}</strong> ({{ $uEmp->days_until_anniversary == 0 ? 'Today! 🎊' : 'in ' . $uEmp->days_until_anniversary . ' days' }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </span>
                    </span>
                    <span class="marquee-divider">•</span>
                @elseif($anniversary->count() > 0)
                    <span class="marquee-chip chip-notice">
                        <i class="fa-solid fa-award"></i>
                        <span>🎉 Next Work Anniversary: <strong>{{ $anniversary->first()->name }}</strong> (in {{ $anniversary->first()->days_until_anniversary }} days on {{ $anniversary->first()->next_anniversary_date->format('d M') }})</span>
                    </span>
                    <span class="marquee-divider">•</span>
                @endif

                <!-- 5. Payroll Notice -->
                <span class="marquee-chip chip-payroll">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Payroll Reminder: Monthly salary computation, deductions, and payslip generation window is active.</span>
                </span>
                <span class="marquee-divider">•</span>

                <!-- 6. Attendance & Policy Guidelines -->
                <span class="marquee-chip chip-policy">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Shift & Punch Policy: Employees must clock in within branch geofencing or designated IP zones.</span>
                </span>
            </marquee>
        </div>
    </div>

    <!-- Executive Quick Operations Launchpad (Zero-Scroll Instant Navigation) -->
    <div class="quick-launchpad">
        <div class="quick-launchpad-header">
            <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2" style="font-size: 0.88rem; letter-spacing: 0.2px;">
                <span class="d-inline-flex align-items-center justify-content-center rounded-2 shadow-xs quick-icon-bolt" style="width: 24px; height: 24px;">
                    <i class="fa-solid fa-bolt" style="font-size: 0.72rem;"></i>
                </span>
                <span>Quick Operations</span>
                <span class="badge quick-header-badge fw-normal d-none d-sm-inline-block" style="font-size: 0.68rem;">Instant Access</span>
            </h6>
            <span class="text-muted small d-none d-md-inline" style="font-size: 0.75rem;">1-Click Shortcuts</span>
        </div>

        <div class="row g-2">
            <!-- 1. Employees -->
            <div class="col-4 col-md-4 col-xl-2">
                <a href="{{ route('employee.index') }}" class="quick-action-card">
                    <div class="quick-icon-box quick-icon-emp">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <span class="quick-title">Employees</span>
                        <span class="quick-sub">Manage & Add</span>
                    </div>
                </a>
            </div>

            <!-- 2. Attendance -->
            <div class="col-4 col-md-4 col-xl-2">
                <a href="{{ route('attendance.index') }}" class="quick-action-card">
                    <div class="quick-icon-box quick-icon-att">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <div>
                        <span class="quick-title">Attendance</span>
                        <span class="quick-sub">Live Roster</span>
                    </div>
                </a>
            </div>

            <!-- 3. Payroll -->
            <div class="col-4 col-md-4 col-xl-2">
                <a href="{{ route('generateSalary') }}" class="quick-action-card">
                    <div class="quick-icon-box quick-icon-pay">
                        <i class="fa-solid fa-money-check-dollar"></i>
                    </div>
                    <div>
                        <span class="quick-title">Payroll</span>
                        <span class="quick-sub">Run Salary</span>
                    </div>
                </a>
            </div>

            <!-- 4. Leaves -->
            <div class="col-4 col-md-4 col-xl-2">
                <a href="{{ route('leaveList') }}" class="quick-action-card">
                    <div class="quick-icon-box quick-icon-leave">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <span class="quick-title">Leaves</span>
                        <span class="quick-sub">Approvals</span>
                    </div>
                </a>
            </div>

            <!-- 5. Tasks -->
            <div class="col-4 col-md-4 col-xl-2">
                <a href="{{ route('taskList') }}" class="quick-action-card">
                    <div class="quick-icon-box quick-icon-task">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <span class="quick-title">Tasks</span>
                        <span class="quick-sub">Team Board</span>
                    </div>
                </a>
            </div>

            <!-- 6. Reports -->
            <div class="col-4 col-md-4 col-xl-2">
                <a href="{{ route('download-report.index') }}" class="quick-action-card">
                    <div class="quick-icon-box quick-icon-reports">
                        <i class="fa-solid fa-file-arrow-down"></i>
                    </div>
                    <div>
                        <span class="quick-title">Reports</span>
                        <span class="quick-sub">Downloads</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Key Metrics / KPI Cards -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        
        <!-- Total Employees -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-primary">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Total Workforce</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <h3 class="kpi-value mb-2">{{ $employeeCount }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.8125rem;">
                    <span>Active in organization</span>
                    <a href="{{ route('employee.index') }}" class="text-primary text-decoration-none fw-semibold">
                        View all <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Present Today -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-success">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Present Today</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="kpi-value text-success mb-0">{{ $presentCount }}</h3>
                    @if($employeeCount > 0)
                        @php $presentPercent = round(($presentCount / $employeeCount) * 100); @endphp
                        <span class="badge-stafo badge-stafo-success">{{ $presentPercent }}% rate</span>
                    @endif
                </div>
                <div class="ratio-progress-bar">
                    <div class="ratio-progress-fill" style="width: {{ $employeeCount > 0 ? min(100, round(($presentCount / $employeeCount) * 100)) : 0 }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 0.8125rem;">
                    <span>Marked in today</span>
                    <a href="{{ route('attendance.index') }}" class="text-success text-decoration-none fw-semibold">Attendance logs</a>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-danger">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Absent Today</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="kpi-value text-danger mb-0">{{ $absentCount }}</h3>
                    @if($employeeCount > 0 && $absentCount > 0)
                        <span class="badge-stafo badge-stafo-danger">{{ round(($absentCount / $employeeCount) * 100) }}% off</span>
                    @endif
                </div>
                <div class="ratio-progress-bar">
                    <div class="ratio-progress-fill" style="background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); width: {{ $employeeCount > 0 ? min(100, round(($absentCount / $employeeCount) * 100)) : 0 }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 0.8125rem;">
                    <span>Unmarked employees</span>
                    <span class="text-muted fw-semibold">Today</span>
                </div>
            </div>
        </div>

        <!-- Approved Leaves This Month -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-purple">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Leaves Approved</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <h3 class="kpi-value kpi-purple-val mb-2">{{ $employeesOnLeave->count() }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.8125rem;">
                    <span>This month ({{ date('M Y') }})</span>
                    <a href="{{ route('leaveList') }}" class="link-purple-action fw-semibold">
                        Leave list <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- HRMS Visual Analytics Row (Donut & Pie Charts) -->
    <div class="row g-3 mb-4">
        
        <!-- Chart 1: Today's Attendance Distribution (Donut Chart) -->
        <div class="col-12 col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-card-title">
                        <i class="fa-solid fa-chart-pie text-success me-2"></i> Attendance Today
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                        <span class="live-pulse-dot"></span> Live
                    </span>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container-relative">
                        <canvas id="attendanceChartCanvas"></canvas>
                        <div class="chart-center-metric">
                            @php
                                $totalAtt = $employeeCount > 0 ? $employeeCount : 18;
                                $presRate = $employeeCount > 0 ? round(($presentCount / max(1, $employeeCount)) * 100) : 89;
                            @endphp
                            <span class="chart-center-val text-success">{{ $presRate }}%</span>
                            <span class="chart-center-sub">Present Rate</span>
                        </div>
                    </div>
                    
                    <!-- Attendance Legend Details -->
                    <ul class="chart-legend-list">
                        <li class="chart-legend-item">
                            <div class="d-flex align-items-center gap-2">
                                <span class="chart-legend-indicator" style="background-color: #10b981;"></span>
                                <span class="fw-medium text-dark">Present</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-0.5 rounded">
                                    {{ $fullPresentCount > 0 ? $fullPresentCount : ($presentCount > 0 ? $presentCount : ($employeeCount > 0 ? 0 : 16)) }}
                                </span>
                                <small class="text-muted" style="min-width: 36px; text-align: right;">
                                    {{ $employeeCount > 0 ? round((($fullPresentCount > 0 ? $fullPresentCount : $presentCount) / max(1, $employeeCount)) * 100) : 89 }}%
                                </small>
                            </div>
                        </li>

                        @if($halfDayCount > 0 || $employeeCount == 0)
                        <li class="chart-legend-item">
                            <div class="d-flex align-items-center gap-2">
                                <span class="chart-legend-indicator" style="background-color: #f59e0b;"></span>
                                <span class="fw-medium text-dark">Half Day</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-0.5 rounded">
                                    {{ $employeeCount > 0 ? $halfDayCount : 1 }}
                                </span>
                                <small class="text-muted" style="min-width: 36px; text-align: right;">
                                    {{ $employeeCount > 0 ? round(($halfDayCount / max(1, $employeeCount)) * 100) : 5 }}%
                                </small>
                            </div>
                        </li>
                        @endif

                        <li class="chart-legend-item">
                            <div class="d-flex align-items-center gap-2">
                                <span class="chart-legend-indicator" style="background-color: #8b5cf6;"></span>
                                <span class="fw-medium text-dark">On Leave</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-purple-theme fw-bold px-2 py-0.5 rounded">
                                    {{ $employeeCount > 0 ? $todayLeavesCount : 1 }}
                                </span>
                                <small class="text-muted" style="min-width: 36px; text-align: right;">
                                    {{ $employeeCount > 0 ? round(($todayLeavesCount / max(1, $employeeCount)) * 100) : 6 }}%
                                </small>
                            </div>
                        </li>

                        <li class="chart-legend-item">
                            <div class="d-flex align-items-center gap-2">
                                <span class="chart-legend-indicator" style="background-color: #ef4444;"></span>
                                <span class="fw-medium text-dark">Absent</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-0.5 rounded">
                                    {{ $employeeCount > 0 ? $absentCount : 0 }}
                                </span>
                                <small class="text-muted" style="min-width: 36px; text-align: right;">
                                    {{ $employeeCount > 0 ? round(($absentCount / max(1, $employeeCount)) * 100) : 0 }}%
                                </small>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="chart-card-footer">
                    <span class="text-muted">Total: <strong>{{ $employeeCount > 0 ? $employeeCount : 18 }} employees</strong></span>
                    <a href="{{ route('attendance.index') }}" class="text-success fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                        Attendance Logs <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Chart 2: Workforce by Department (Donut / Pie Chart) -->
        <div class="col-12 col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-card-title">
                        <i class="fa-solid fa-sitemap text-primary me-2"></i> Department Share
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                        {{ count($departmentStats) > 0 ? count($departmentStats) . ' Depts' : '5 Depts' }}
                    </span>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container-relative">
                        <canvas id="departmentChartCanvas"></canvas>
                        <div class="chart-center-metric">
                            <span class="chart-center-val text-primary">{{ $employeeCount > 0 ? $employeeCount : 48 }}</span>
                            <span class="chart-center-sub">Total Staff</span>
                        </div>
                    </div>

                    <!-- Department Legend Details -->
                    @php
                        $deptPalette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];
                        $deptDisplay = count($departmentStats) > 0 
                            ? $departmentStats 
                            : ['Engineering' => 18, 'Operations' => 14, 'Sales & Marketing' => 9, 'Human Resources' => 5, 'Finance' => 2];
                        $deptTotal = array_sum($deptDisplay);
                    @endphp
                    <ul class="chart-legend-list">
                        @php $colorIdx = 0; @endphp
                        @foreach(array_slice($deptDisplay, 0, 4, true) as $deptName => $deptNum)
                            <li class="chart-legend-item">
                                <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 180px;">
                                    <span class="chart-legend-indicator" style="background-color: {{ $deptPalette[$colorIdx % count($deptPalette)] }};"></span>
                                    <span class="fw-medium text-dark text-truncate">{{ $deptName }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-0.5 rounded">
                                        {{ $deptNum }}
                                    </span>
                                    <small class="text-muted" style="min-width: 36px; text-align: right;">
                                        {{ $deptTotal > 0 ? round(($deptNum / $deptTotal) * 100) : 0 }}%
                                    </small>
                                </div>
                            </li>
                            @php $colorIdx++; @endphp
                        @endforeach
                    </ul>
                </div>
                <div class="chart-card-footer">
                    <span class="text-muted">Teams: <strong>{{ count($deptDisplay) }} units</strong></span>
                    <a href="{{ route('departments.index') }}" class="text-primary fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                        Departments <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Chart 3: Leave Utilization & Request Status (Pie / Donut Chart) -->
        <div class="col-12 col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-card-title">
                        <i class="fa-solid fa-calendar-check text-purple me-2"></i> Leave Utilization
                    </h6>
                    <span class="badge badge-purple-theme px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                        {{ date('Y') }} Annual
                    </span>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container-relative">
                        <canvas id="leaveTypeChartCanvas"></canvas>
                        <div class="chart-center-metric">
                            <span class="chart-center-val title-purple">{{ $employeesOnLeave->count() > 0 ? $employeesOnLeave->count() : 15 }}</span>
                            <span class="chart-center-sub">Leaves Logged</span>
                        </div>
                    </div>

                    <!-- Leave Legend Details -->
                    @php
                        $leavePalette = ['#8b5cf6', '#06b6d4', '#f59e0b', '#10b981', '#ef4444'];
                        $leaveDisplay = count($leaveTypeStats) > 0 
                            ? $leaveTypeStats 
                            : ['Casual Leave' => 8, 'Sick Leave' => 4, 'Privileged Leave' => 2, 'Comp Off' => 1];
                        $leaveTotal = array_sum($leaveDisplay);
                    @endphp
                    <ul class="chart-legend-list">
                        @php $leaveColorIdx = 0; @endphp
                        @foreach(array_slice($leaveDisplay, 0, 4, true) as $leaveName => $leaveNum)
                            <li class="chart-legend-item">
                                <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 180px;">
                                    <span class="chart-legend-indicator" style="background-color: {{ $leavePalette[$leaveColorIdx % count($leavePalette)] }};"></span>
                                    <span class="fw-medium text-dark text-truncate">{{ $leaveName }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-0.5 rounded">
                                        {{ $leaveNum }}
                                    </span>
                                    <small class="text-muted" style="min-width: 36px; text-align: right;">
                                        {{ $leaveTotal > 0 ? round(($leaveNum / $leaveTotal) * 100) : 0 }}%
                                    </small>
                                </div>
                            </li>
                            @php $leaveColorIdx++; @endphp
                        @endforeach
                    </ul>
                </div>
                <div class="chart-card-footer">
                    <span class="text-muted">Approved this month: <strong>{{ $employeesOnLeave->count() }}</strong></span>
                    <a href="{{ route('leaveList') }}" class="link-purple-action fw-semibold d-inline-flex align-items-center gap-1">
                        Manage Leaves <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Approved Leaves Table -->
    <div class="stafo-card mb-4">
        <div class="stafo-card-header">
            <h5 class="stafo-card-title">
                <i class="fa-solid fa-plane-departure text-primary"></i> Approved Leaves This Month
            </h5>
            <a href="{{ route('leaveList') }}" class="btn btn-sm btn-outline-primary px-3 fw-semibold" style="border-radius: 8px;">
                View All Requests
            </a>
        </div>
        <div class="table-responsive">
            <table class="table-modern align-middle">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Reason</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employeesOnLeave as $leave)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white bg-primary" style="width: 34px; height: 34px; font-size: 0.8125rem;">
                                        {{ strtoupper(substr($leave->employeeBasicInfo->name ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $leave->employeeBasicInfo->name ?? 'Employee #' . $leave->employee_id }}</span>
                                        <small class="text-muted">ID: {{ $leave->employeeBasicInfo->emp_id ?? $leave->employee_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-info">{{ $leave->leave_type ?? 'Casual' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($leave->from_date)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($leave->to_date)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $leave->reason ?? '-' }}</span>
                            </td>
                            <td class="text-end">
                                <span class="badge-stafo badge-stafo-success">
                                    <i class="fa-solid fa-check-circle me-1"></i> Approved
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-clipboard-check fs-3 mb-2 d-block text-secondary opacity-50"></i>
                                No approved leaves scheduled for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Festive Celebrations: Birthdays & Work Anniversaries -->
    <div class="row g-4 mb-4">
        
        <!-- Birthdays Carousel -->
        <div class="col-lg-6">
            <div class="stafo-card h-100 mb-0">
                <div class="stafo-card-header">
                    <h5 class="stafo-card-title title-pink">
                        <i class="fa-solid fa-cake-candles me-2"></i> Birthdays This Month
                    </h5>
                    <span class="badge text-white px-2 py-1 rounded-pill" style="background-color: #db2777; font-size: 0.75rem;">
                        {{ $birthday->count() }} Celebrating
                    </span>
                </div>
                <div class="p-3">
                    @if ($birthday->count() > 0)
                        <div id="birthdayCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($birthday->chunk(2) as $chunkIndex => $chunk)
                                    <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                        <div class="row g-3">
                                            @foreach ($chunk as $emp)
                                                <div class="col-12 col-sm-6">
                                                    <div class="celebration-card celebration-birthday">
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>

                                                        @if ($emp->image && file_exists(public_path('uploads/employees/' . $emp->image)))
                                                             <img src="{{ asset('uploads/employees/' . $emp->image) }}" alt="{{ $emp->name }}" class="celebration-avatar">
                                                        @else
                                                            <div class="celebration-initials">
                                                                {{ strtoupper(substr($emp->name ?? 'E', 0, 2)) }}
                                                            </div>
                                                        @endif

                                                        <h5 class="fw-bold text-white mb-1">{{ $emp->name }}</h5>
                                                        <p class="text-white-50 mb-2" style="font-size: 0.8125rem;">
                                                            {{ \Carbon\Carbon::parse($emp->date_of_birth)->format('F j') }}
                                                        </p>
                                                        <span class="celebration-badge celebration-badge-birthday fw-bold px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                                                            🎉 Happy Birthday!
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($birthday->count() > 2)
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button class="carousel-nav-btn" type="button" data-bs-target="#birthdayCarousel" data-bs-slide="prev">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <button class="carousel-nav-btn" type="button" data-bs-target="#birthdayCarousel" data-bs-slide="next">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="celebration-empty-state">
                            <i class="fa-solid fa-cake-candles fs-2 mb-2 d-block text-muted opacity-50"></i>
                            <p class="mb-0 fw-medium">No employee birthdays this month</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Work Anniversaries Carousel & All Employees Tracker -->
        <div class="col-lg-6">
            <div class="stafo-card h-100 mb-0">
                <div class="stafo-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="stafo-card-title title-purple mb-0">
                        <i class="fa-solid fa-award me-2"></i> Employee Work Anniversaries
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-white px-2.5 py-1 rounded-pill" style="background-color: #7c3aed; font-size: 0.75rem;">
                            {{ $anniversary->count() }} Employees
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs" 
                            data-bs-toggle="modal" data-bs-target="#allAnniversaryModal" 
                            style="background: #7c3aed; color: #ffffff; border-color: #7c3aed; font-size: 0.72rem;">
                            <i class="fa-solid fa-users me-1"></i> View All ({{ $anniversary->count() + $pendingDojEmployees->count() }})
                        </button>
                    </div>
                </div>
                <div class="p-3">
                    @if ($anniversary->count() > 0)
                        <div id="anniversaryCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($anniversary->chunk(2) as $chunkIndex => $chunk)
                                    <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                        <div class="row g-3">
                                            @foreach ($chunk as $emp)
                                                <div class="col-12 col-sm-6">
                                                    <div class="celebration-card celebration-anniversary">
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>
                                                        <div class="confetti-piece"></div>

                                                        @if ($emp->image && file_exists(public_path('uploads/employees/' . $emp->image)))
                                                            <img src="{{ asset('uploads/employees/' . $emp->image) }}" alt="{{ $emp->name }}" class="celebration-avatar">
                                                        @else
                                                            <div class="celebration-initials">
                                                                {{ strtoupper(substr($emp->name ?? 'E', 0, 2)) }}
                                                            </div>
                                                        @endif

                                                        <h5 class="fw-bold text-white mb-0">{{ $emp->name }}</h5>
                                                        <small class="text-white-50 d-block mb-1.5" style="font-size: 0.72rem;">
                                                            {{ $emp->emp_id }} • {{ $emp->department->name ?? 'Staff' }}
                                                        </small>

                                                        <p class="text-white mb-1.5" style="font-size: 0.78rem; opacity: 0.95;">
                                                            <i class="fa-regular fa-calendar-check me-1"></i> Joined: <strong>{{ \Carbon\Carbon::parse($emp->date_of_joining)->format('d M Y') }}</strong>
                                                            <span class="d-block text-white-50 mt-0.5" style="font-size: 0.72rem;">({{ $emp->days_since_joining }} days completed)</span>
                                                        </p>

                                                        <!-- Kitne din baad work anniversary hai -->
                                                        <div class="mb-2">
                                                            @if($emp->days_until_anniversary === 0)
                                                                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="font-size: 0.8rem;">
                                                                    🎉 Work Anniversary Today! ({{ $emp->years_completing }} Year)
                                                                </span>
                                                            @elseif($emp->days_until_anniversary <= 60)
                                                                <span class="badge bg-white text-dark px-2.5 py-1.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="color: #6b21a8 !important; font-size: 0.78rem;">
                                                                    <i class="fa-solid fa-hourglass-half text-warning"></i>
                                                                    Anniversary in <strong class="text-danger">{{ $emp->days_until_anniversary }} days</strong> ({{ $emp->next_anniversary_date->format('d M') }})
                                                                </span>
                                                            @else
                                                                <span class="badge bg-white bg-opacity-95 text-dark px-2.5 py-1.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="color: #6b21a8 !important; font-size: 0.78rem;">
                                                                    <i class="fa-regular fa-calendar text-primary"></i>
                                                                    Anniversary in <strong>{{ $emp->days_until_anniversary }} days</strong> ({{ $emp->next_anniversary_date->format('d M Y') }})
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <span class="celebration-badge celebration-badge-anniversary fw-bold px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                                                            🎊 Completing Year {{ $emp->years_completing }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($anniversary->count() > 2)
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button class="carousel-nav-btn" type="button" data-bs-target="#anniversaryCarousel" data-bs-slide="prev">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <button class="carousel-nav-btn" type="button" data-bs-target="#anniversaryCarousel" data-bs-slide="next">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="celebration-empty-state">
                            <i class="fa-solid fa-champagne-glasses fs-2 mb-2 d-block text-muted opacity-50"></i>
                            <p class="mb-0 fw-medium">No work anniversaries recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal: All Employees Work Anniversary & DOJ Tracker -->
<div class="modal fade" id="allAnniversaryModal" tabindex="-1" aria-labelledby="allAnniversaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background: linear-gradient(135deg, #f8fafc 0%, #ede9fe 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 text-white d-flex align-items-center justify-content-center" style="background-color: #7c3aed; width: 38px; height: 38px;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="allAnniversaryModalLabel">All Employees Work Anniversary Tracker</h5>
                        <small class="text-muted">Calculated countdown days and tenure since Date of Joining (DOJ) for all staff</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Search & Quick Summary Stats -->
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="anniversarySearchInput" class="form-control border-start-0" 
                                placeholder="Search employee name, ID or department..." onkeyup="filterAnniversaryTable()">
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-2">
                            <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.76rem; color: #7c3aed !important;">
                                <i class="fa-solid fa-users me-1"></i> Total Tracked: <strong>{{ $anniversary->count() }}</strong>
                            </span>
                            @php
                                $next30Days = $anniversary->where('days_until_anniversary', '<=', 30)->count();
                            @endphp
                            <span class="badge {{ $next30Days > 0 ? 'bg-danger text-white' : 'bg-light text-muted border' }} px-3 py-1.5 rounded-pill" style="font-size: 0.76rem;">
                                <i class="fa-solid fa-hourglass-half me-1"></i> Next 30 Days: <strong>{{ $next30Days }}</strong>
                            </span>
                            @if($pendingDojEmployees->count() > 0)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.76rem;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Missing DOJ: <strong>{{ $pendingDojEmployees->count() }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Table: All Employees Anniversaries -->
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0" id="anniversaryTable">
                        <thead class="table-light">
                            <tr class="text-muted" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                <th class="py-2.5 ps-3">#</th>
                                <th class="py-2.5">Employee</th>
                                <th class="py-2.5">Department</th>
                                <th class="py-2.5">Date of Joining</th>
                                <th class="py-2.5">Completed Tenure</th>
                                <th class="py-2.5">Next Anniversary</th>
                                <th class="py-2.5">Anniversary In (Days)</th>
                                <th class="py-2.5 pe-3">Milestone</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.84rem;">
                            @forelse($anniversary as $index => $emp)
                                <tr class="anniversary-row">
                                    <td class="ps-3 text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($emp->image && file_exists(public_path('uploads/employees/' . $emp->image)))
                                                <img src="{{ asset('uploads/employees/' . $emp->image) }}" alt="{{ $emp->name }}" class="rounded-circle border" style="width: 34px; height: 34px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; background-color: #7c3aed; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($emp->name ?? 'E', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark emp-name">{{ $emp->name }}</div>
                                                <small class="text-muted emp-id">{{ $emp->emp_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="emp-dept text-muted">{{ $emp->department->name ?? 'General Operations' }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($emp->date_of_joining)->format('d M Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill">
                                            {{ $emp->days_since_joining }} days ({{ $emp->years_completed }} yr {{ floor(($emp->days_since_joining % 365) / 30) }} mo)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $emp->next_anniversary_date->format('d M Y') }}</span>
                                    </td>
                                    <td>
                                        @if($emp->days_until_anniversary === 0)
                                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold">
                                                🎉 Today!
                                            </span>
                                        @elseif($emp->days_until_anniversary <= 30)
                                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-bold">
                                                In {{ $emp->days_until_anniversary }} Days
                                            </span>
                                        @elseif($emp->days_until_anniversary <= 60)
                                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold">
                                                In {{ $emp->days_until_anniversary }} Days
                                            </span>
                                        @else
                                            <span class="badge text-white px-2.5 py-1 rounded-pill fw-semibold" style="background-color: #7c3aed;">
                                                In {{ $emp->days_until_anniversary }} Days
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        <span class="badge bg-light text-purple border px-2.5 py-1 rounded-pill fw-semibold" style="color: #7c3aed !important;">
                                            Completing Year {{ $emp->years_completing }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No employee work anniversaries recorded.</td>
                                </tr>
                            @endforelse

                            <!-- Employees with Pending DOJ -->
                            @if($pendingDojEmployees->count() > 0)
                                @foreach($pendingDojEmployees as $pIndex => $pEmp)
                                    <tr class="anniversary-row table-light text-muted opacity-75">
                                        <td class="ps-3">{{ $anniversary->count() + $pIndex + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle text-muted bg-white border d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($pEmp->name ?? 'E', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold emp-name">{{ $pEmp->name }}</div>
                                                    <small class="emp-id">{{ $pEmp->emp_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="emp-dept">{{ $pEmp->department->name ?? 'General Operations' }}</td>
                                        <td colspan="4" class="text-warning">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i> Date of Joining not configured in employee profile
                                        </td>
                                        <td class="pe-3">
                                            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.72rem;">Set DOJ</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer border-top px-4 py-2.5 bg-light">
                <span class="text-muted small me-auto" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Anniversaries are sorted chronologically by nearest upcoming countdown days.
                </span>
                <button type="button" class="btn btn-secondary btn-sm px-3 rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function filterAnniversaryTable() {
        var input = document.getElementById("anniversarySearchInput");
        var filter = input.value.toLowerCase();
        var rows = document.querySelectorAll("#anniversaryTable .anniversary-row");

        rows.forEach(function(row) {
            var name = row.querySelector(".emp-name") ? row.querySelector(".emp-name").textContent.toLowerCase() : "";
            var id = row.querySelector(".emp-id") ? row.querySelector(".emp-id").textContent.toLowerCase() : "";
            var dept = row.querySelector(".emp-dept") ? row.querySelector(".emp-dept").textContent.toLowerCase() : "";

            if (name.includes(filter) || id.includes(filter) || dept.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
</script>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    if (typeof Chart === 'undefined') {
        document.write('<script src="{{ asset('lib/chart/chart.min.js') }}"><\/script>');
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Universal Donut Chart Config
    const baseChartConfig = {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        animation: {
            duration: 1000,
            animateRotate: true,
            animateScale: true
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.92)',
                titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + Number(b), 0);
                        const val = context.parsed || context.raw;
                        const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                        return ` ${context.label}: ${val} (${pct}%)`;
                    }
                }
            }
        }
    };

    let attChart = null;
    let deptChart = null;
    let leaveChart = null;

    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const chartBorderColor = (currentTheme === 'dark') ? '#111c30' : '#ffffff';

    // 1. Attendance Today Donut Chart
    const attCanvas = document.getElementById('attendanceChartCanvas');
    if (attCanvas) {
        @php
            $hasRealAtt = ($employeeCount > 0);
            $attPresent = $hasRealAtt ? ($fullPresentCount > 0 ? $fullPresentCount : $presentCount) : 16;
            $attHalf = $hasRealAtt ? $halfDayCount : 1;
            $attLeave = $hasRealAtt ? $todayLeavesCount : 1;
            $attAbsent = $hasRealAtt ? $absentCount : 0;
        @endphp

        attChart = new Chart(attCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Half Day', 'On Leave', 'Absent'],
                datasets: [{
                    data: [{{ $attPresent }}, {{ $attHalf }}, {{ $attLeave }}, {{ $attAbsent }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                    hoverBackgroundColor: ['#059669', '#d97706', '#7c3aed', '#dc2626'],
                    borderWidth: 2,
                    borderColor: chartBorderColor,
                    hoverOffset: 4
                }]
            },
            options: baseChartConfig
        });
    }

    // 2. Department Share Donut Chart
    const deptCanvas = document.getElementById('departmentChartCanvas');
    if (deptCanvas) {
        @php
            $deptLabels = !empty($departmentStats) ? array_keys($departmentStats) : ['Engineering', 'Operations', 'Sales & Marketing', 'Human Resources', 'Finance'];
            $deptValues = !empty($departmentStats) ? array_values($departmentStats) : [18, 14, 9, 5, 2];
        @endphp

        const deptLabels = {!! json_encode($deptLabels) !!};
        const deptValues = {!! json_encode($deptValues) !!};
        const deptColors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

        deptChart = new Chart(deptCanvas, {
            type: 'doughnut',
            data: {
                labels: deptLabels,
                datasets: [{
                    data: deptValues,
                    backgroundColor: deptColors.slice(0, deptLabels.length),
                    borderWidth: 2,
                    borderColor: chartBorderColor,
                    hoverOffset: 4
                }]
            },
            options: baseChartConfig
        });
    }

    // 3. Leave Utilization Donut Chart
    const leaveCanvas = document.getElementById('leaveTypeChartCanvas');
    if (leaveCanvas) {
        @php
            $leaveLabels = !empty($leaveTypeStats) ? array_keys($leaveTypeStats) : ['Casual Leave', 'Sick Leave', 'Privileged Leave', 'Comp Off'];
            $leaveValues = !empty($leaveTypeStats) ? array_values($leaveTypeStats) : [8, 4, 2, 1];
        @endphp

        const leaveLabels = {!! json_encode($leaveLabels) !!};
        const leaveValues = {!! json_encode($leaveValues) !!};
        const leaveColors = ['#8b5cf6', '#06b6d4', '#f59e0b', '#10b981', '#ef4444'];

        leaveChart = new Chart(leaveCanvas, {
            type: 'doughnut',
            data: {
                labels: leaveLabels,
                datasets: [{
                    data: leaveValues,
                    backgroundColor: leaveColors.slice(0, leaveLabels.length),
                    borderWidth: 2,
                    borderColor: chartBorderColor,
                    hoverOffset: 4
                }]
            },
            options: baseChartConfig
        });
    }

    // Dynamic Chart Re-coloring on Theme Toggle
    function updateChartsTheme(theme) {
        const isDark = (theme === 'dark');
        const newBorderColor = isDark ? '#111c30' : '#ffffff';
        [attChart, deptChart, leaveChart].forEach(chart => {
            if (chart && chart.data && chart.data.datasets) {
                chart.data.datasets.forEach(ds => {
                    ds.borderColor = newBorderColor;
                });
                chart.update();
            }
        });
    }

    window.addEventListener('stafoThemeChanged', function(e) {
        updateChartsTheme(e.detail.theme);
    });
});
</script>
@endsection