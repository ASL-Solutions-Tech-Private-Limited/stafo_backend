@extends('employee.layouts.app')

@section('title', 'My Dashboard | STAFO HRMS')

@section('css')
<style>
    .emp-hero-banner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #0f766e 70%, #0284c7 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.3);
        margin-bottom: 24px;
    }
    .emp-hero-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    @media (max-width: 767.98px) {
        .emp-hero-banner {
            padding: 16px 18px !important;
            margin-bottom: 14px !important;
            border-radius: 12px !important;
        }
        .emp-hero-banner h2 {
            font-size: 1.2rem !important;
        }
        .emp-hero-banner p {
            font-size: 0.82rem !important;
        }
    }

    .emp-stat-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 20px 24px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .emp-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }
    .emp-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .emp-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .emp-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .stafo-marquee-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 8px 14px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    }

    @media (max-width: 767.98px) {
        .emp-stat-card {
            padding: 14px 16px !important;
            border-radius: 12px !important;
        }
        .emp-card {
            border-radius: 12px !important;
            margin-bottom: 14px !important;
        }
        .emp-card-header {
            padding: 12px 16px !important;
        }
        .stafo-marquee-bar {
            margin-bottom: 14px !important;
            padding: 6px 12px !important;
            border-radius: 12px !important;
        }
    }

    .punch-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Dark Mode Styling */
    [data-theme="dark"] .emp-hero-banner {
        background: linear-gradient(135deg, #022c22 0%, #064e3b 45%, #0c4a6e 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.45) !important;
    }
    [data-theme="dark"] .emp-stat-card,
    [data-theme="dark"] .emp-card {
        background: #111c30 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
    [data-theme="dark"] .emp-card-header {
        background: #132038 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
    [data-theme="dark"] .text-dark {
        color: #f8fafc !important;
    }
    [data-theme="dark"] .bg-light {
        background-color: #0d1527 !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .table thead th,
    [data-theme="dark"] .table-light,
    [data-theme="dark"] thead.table-light,
    [data-theme="dark"] thead.table-light th,
    [data-theme="dark"] .table-light th {
        background-color: #16243f !important;
        color: #f8fafc !important;
        --bs-table-color: #f8fafc !important;
        --bs-table-bg: #16243f !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .table td {
        color: #e2e8f0 !important;
        --bs-table-color: #e2e8f0 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] #geoTrackingRequestCard {
        background: linear-gradient(135deg, #2d1e02 0%, #451a03 100%) !important;
        border-color: rgba(245, 158, 11, 0.4) !important;
        color: #fef3c7 !important;
    }
    [data-theme="dark"] #geoTrackingRequestCard h5,
    [data-theme="dark"] #geoTrackingRequestCard p {
        color: #fef3c7 !important;
    }
    [data-theme="dark"] #geoTrackingActiveCard {
        background: linear-gradient(135deg, #022c22 0%, #064e3b 100%) !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
        color: #d1fae5 !important;
    }
    [data-theme="dark"] #geoTrackingActiveCard span,
    [data-theme="dark"] #geoTrackingActiveCard small {
        color: #d1fae5 !important;
    }
</style>
@endsection

@section('content')
@php
    $dashEmp = Auth::guard('employee')->user() ?? $employee_info;
    $canPunch = !$dashEmp || $dashEmp->hasPermission('attendance.punch');
    $canAttView = !$dashEmp || $dashEmp->hasPermission('attendance.view');
    $canLeavesView = !$dashEmp || $dashEmp->hasPermission('leaves.view');
    $canLeavesApply = !$dashEmp || $dashEmp->hasPermission('leaves.apply');
    $canTasksView = !$dashEmp || $dashEmp->hasPermission('tasks.view');
@endphp
<div class="container-fluid p-0">

    <!-- Hero Welcome Banner -->
    <div class="emp-hero-banner">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="emp-portal-pill">
                        <i class="fa-solid fa-sparkles me-1 text-warning"></i> Employee Portal
                    </span>
                    <span class="text-white-50 small">• {{ date('l, F j, Y') }}</span>
                    <span class="emp-banner-chip font-monospace">
                        ID: {{ $employee_info->emp_id ?? 'N/A' }}
                    </span>
                </div>
                <h2 class="text-white mb-2 fw-bold">Welcome back, {{ $employee_info->name }}! 👋</h2>
                <p class="text-white mb-0 small opacity-90" style="max-width: 620px;">
                    Here is your personal attendance, leave balances, assigned tasks, and company updates.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                    @if($employee_info->position)
                        <span class="emp-banner-chip">
                            <i class="fa-solid fa-briefcase me-1 text-info"></i> {{ $employee_info->position }}
                        </span>
                    @endif
                    @if($employee_info->department)
                        <span class="emp-banner-chip">
                            <i class="fa-solid fa-layer-group me-1 text-warning"></i> {{ $employee_info->department->name }}
                        </span>
                    @endif
                    @if($employee_info->company)
                        <span class="emp-banner-chip">
                            <i class="fa-solid fa-building me-1 text-success"></i> {{ $employee_info->company->company_name }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-inline-flex flex-column align-items-lg-end gap-2">
                    @if($canAttView)
                        @if($todayAttendance || $firstPunch)
                            <div class="punch-pill punch-pill-success">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Punched In Today</span>
                                @if($firstPunch)
                                    <small class="opacity-75">({{ Carbon\Carbon::parse($firstPunch->created_at)->format('h:i A') }})</small>
                                @endif
                            </div>
                        @else
                            <div class="punch-pill punch-pill-warning">
                                <i class="fa-solid fa-clock"></i>
                                <span>Not Punched In Yet</span>
                            </div>
                        @endif
                    @endif

                    <div class="d-flex align-items-center gap-2 mt-1">
                        @if($canPunch)
                            @if(isset($isPunchedIn) && $isPunchedIn)
                                <button type="button" onclick="executeWebPunch('punch_out')" class="btn btn-danger btn-sm fw-bold px-3 py-2 shadow-sm rounded-3">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Punch OUT
                                </button>
                            @else
                                <button type="button" onclick="executeWebPunch('punch_in')" class="btn btn-success btn-sm fw-bold px-3 py-2 shadow-sm rounded-3">
                                    <i class="fa-solid fa-fingerprint me-1"></i> Punch IN
                                </button>
                            @endif
                        @endif
                        @if($canLeavesApply)
                        <a href="{{ route('employee.leaves') }}" class="btn emp-hero-btn-light btn-sm px-3 py-2 shadow-sm rounded-3">
                            <i class="fa-solid fa-plus-circle me-1 text-success"></i> Apply Leave
                        </a>
                        @endif
                        <a href="{{ route('employee.profile') }}" class="btn btn-outline-light btn-sm fw-bold px-3 py-2 rounded-3">
                            <i class="fa-solid fa-user me-1"></i> Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Real-Time Geo Tracking Request Alert (When Company requested tracking - Step 1) --}}
    @if((string)$employee_info->geo_status === '1')
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" id="geoTrackingRequestCard" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 40%, #fed7aa 100%); border-left: 6px solid #f59e0b !important;">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle text-white shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fa-solid fa-location-dot fs-4 fa-bounce"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold small">
                                <i class="fa-solid fa-bell me-1"></i> Tracking Request
                            </span>
                            <span class="badge bg-white text-dark border px-2 py-0.5 rounded-pill small">
                                Shift Hours Only
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Company Has Requested Real-Time Location Tracking</h5>
                        <p class="text-secondary mb-0 small" style="max-width: 700px;">
                            <strong>{{ $employee_info->company->company_name ?? 'Your Company' }}</strong> has requested permission to track your live GPS movement during your assigned shift hours. Please accept or decline this request.
                        </p>
                    </div>
                    <div class="col-12 col-md-auto text-md-end">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" id="btnAcceptGeo" onclick="respondGeoTracking('accept')">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Accept & Start Tracking</span>
                            </button>
                            <button type="button" class="btn btn-outline-danger fw-semibold px-3 py-2 rounded-3" id="btnDeclineGeo" onclick="respondGeoTracking('decline')">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Decline
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif((string)$employee_info->geo_status === '2')
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="geoTrackingActiveCard" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-left: 6px solid #10b981 !important;">
            <div class="card-body p-3 px-md-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-location-crosshairs fa-spin" style="animation-duration: 4s;"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success text-white px-2 py-0.5 rounded-pill small fw-bold">
                                    <i class="fa-solid fa-circle text-white me-1 fa-beat" style="font-size: 7px;"></i> Tracking Active
                                </span>
                                <span class="small fw-semibold text-dark">Real-Time Location Tracking Enabled</span>
                            </div>
                            <small class="text-muted">Your real-time field route is active and sharing with {{ $employee_info->company->company_name ?? 'Company' }} during your shift window.</small>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-3" onclick="respondGeoTracking('decline')" title="Turn off real-time location tracking">
                            <i class="fa-solid fa-power-off me-1 text-danger"></i> Stop Tracking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Live Announcements & Team Pulse Marquee Ticker -->
    @php
        $dashNotifs = collect();
        if ($dashEmp && !empty($dashEmp->company_id)) {
            try {
                $dashNotifs = \App\Models\Notification::where(function($q) use ($dashEmp) {
                        $q->where('employee_id', $dashEmp->id)
                          ->orWhere('company_id', $dashEmp->company_id);
                    })
                    ->latest()
                    ->take(3)
                    ->get();
            } catch (\Throwable $e) {}
        }
    @endphp
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #10b981 0%, #059669 45%, #0284c7 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-bullhorn"></i>
            <span>Notice Board</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                {{-- Active Dynamic Notifications --}}
                @if($dashNotifs && $dashNotifs->count() > 0)
                    @foreach($dashNotifs as $notif)
                        <span class="marquee-chip chip-policy">
                            <i class="fa-solid fa-bell text-warning"></i>
                            <strong>Update:</strong> {{ $notif->message }}
                        </span>
                        <span class="marquee-divider">•</span>
                    @endforeach
                @endif

                {{-- Upcoming Holiday Announcement --}}
                @if(isset($upcomingHolidays) && $upcomingHolidays->count() > 0)
                    @foreach($upcomingHolidays as $uHol)
                        <span class="marquee-chip chip-holiday">
                            <i class="fa-solid fa-gift text-warning"></i>
                            <strong>Upcoming Holiday:</strong> {{ $uHol->title }} ({{ Carbon\Carbon::parse($uHol->start_date)->format('d M, l') }})
                        </span>
                        <span class="marquee-divider">•</span>
                    @endforeach
                @endif

                {{-- Teammate Birthdays This Month --}}
                @if(isset($birthdays) && $birthdays->count() > 0)
                    @foreach($birthdays as $bday)
                        <span class="marquee-chip chip-birthday">
                            <i class="fa-solid fa-cake-candles text-danger"></i>
                            <strong>Birthday:</strong> Happy Birthday to <strong>{{ $bday->name }}</strong> on {{ Carbon\Carbon::parse($bday->date_of_birth)->format('d M') }}! 🎂
                        </span>
                        <span class="marquee-divider">•</span>
                    @endforeach
                @endif

                {{-- Teammate Work Anniversaries This Month --}}
                @if(isset($anniversaries) && $anniversaries->count() > 0)
                    @foreach($anniversaries as $anni)
                        @php
                            $yearsJoined = Carbon\Carbon::parse($anni->date_of_joining)->diffInYears(now());
                        @endphp
                        <span class="marquee-chip chip-holiday">
                            <i class="fa-solid fa-medal text-warning"></i>
                            <strong>Anniversary:</strong> Celebrating <strong>{{ $anni->name }}</strong> ({{ $yearsJoined > 0 ? $yearsJoined . ' Year' . ($yearsJoined > 1 ? 's' : '') : 'Joining Year' }} at STAFO) 👏
                        </span>
                        <span class="marquee-divider">•</span>
                    @endforeach
                @endif

                {{-- Assigned Tasks Pulse --}}
                @if(isset($pendingTasksCount) && $pendingTasksCount > 0)
                    <span class="marquee-chip chip-task">
                        <i class="fa-solid fa-list-check text-danger"></i>
                        <strong>Tasks Queue:</strong> You have <strong>{{ $pendingTasksCount }}</strong> active task{{ $pendingTasksCount > 1 ? 's' : '' }} assigned in your workspace.
                    </span>
                    <span class="marquee-divider">•</span>
                @endif

                {{-- General Workplace Positive Energy --}}
                <span class="marquee-chip chip-attendance">
                    <i class="fa-solid fa-smile-beam text-success"></i>
                    <strong>Have a Productive Day:</strong> Stay hydrated, keep your tasks organized, and enjoy your work!
                </span>
            </marquee>
        </div>
        <div class="d-none d-md-flex align-items-center text-muted small ps-2 border-start" style="font-size: 0.72rem; white-space: nowrap;">
            <i class="fa-solid fa-hand-pointer text-warning me-1"></i> Hover to pause
        </div>
    </div>

    @php
        $dashCanEmployees = $dashEmp && $dashEmp->hasPermission('employees.view');
        $dashCanLeaves = $dashEmp && $dashEmp->hasPermission('leaves.view');
        $dashCanAttAll = $dashEmp && $dashEmp->hasPermission('attendance.view');
        $dashCanTasks = $dashEmp && $dashEmp->hasPermission('tasks.view');
        $dashCanPayroll = $dashEmp && $dashEmp->hasPermission('payroll.view');
        $dashCanReports = $dashEmp && $dashEmp->hasPermission('reports.view');

        $dashHasMgmt = $dashCanEmployees || $dashCanLeaves || $dashCanAttAll || $dashCanTasks || $dashCanPayroll || $dashCanReports;

        $mgmtStaffCount = 0;
        $mgmtPendingLeaves = 0;
        $mgmtPresentToday = 0;
        $mgmtActiveTasks = 0;

        if ($dashHasMgmt && $dashEmp && !empty($dashEmp->company_id)) {
            try {
                if ($dashCanEmployees) {
                    $mgmtStaffCount = \App\Models\Employee::where('company_id', $dashEmp->company_id)->where('status', '1')->count();
                }
                if ($dashCanLeaves) {
                    $mgmtPendingLeaves = \App\Models\EmployeeLeave::where('company_id', $dashEmp->company_id)->where('status', 'pending')->count();
                }
                if ($dashCanAttAll) {
                    $mgmtPresentToday = \App\Models\Attendance::where('company_id', $dashEmp->company_id)->where('date', date('Y-m-d'))->where('attendance', 1)->count();
                }
                if ($dashCanTasks) {
                    $mgmtActiveTasks = \App\Models\Task::where('company_id', $dashEmp->company_id)->where('status', '!=', 'completed')->count();
                }
            } catch (\Exception $e) {
                // safe fallback
            }
        }
    @endphp

    @if($dashHasMgmt)
    <!-- Role Management Console (Empowered by Designation Permissions) -->
    <div class="emp-card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(30, 58, 138, 0.04) 0%, rgba(15, 118, 110, 0.04) 100%); border: 1px solid rgba(14, 165, 233, 0.2) !important;">
        <div class="emp-card-header bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-2 py-3 px-4" style="border-bottom: 1px solid rgba(14, 165, 233, 0.15);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb, #0d9488);">
                    <i class="fa-solid fa-shield-halved fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">Role Management Console</h5>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-user-gear me-1"></i> {{ $dashEmp->designation->name ?? 'Assigned Role' }}
                        </span>
                    </div>
                    <small class="text-muted">You hold company administrative privileges granted to your designation.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                    <i class="fa-solid fa-circle-check me-1"></i> Management Rights Active
                </span>
            </div>
        </div>

        <div class="p-3 p-md-4">
            <div class="row g-3">
                @if($dashCanEmployees)
                <div class="col-sm-6 col-lg-3">
                    <div class="p-3 rounded-3 bg-white border border-slate-200 shadow-2xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                    STAFF DIRECTORY
                                </span>
                                <i class="fa-solid fa-users text-primary fs-5"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $mgmtStaffCount }}</h3>
                            <small class="text-muted">Active Company Employees</small>
                        </div>
                        <a href="{{ route('employee.management.employees') }}" class="btn btn-sm btn-outline-primary fw-bold mt-3 rounded-2 w-100">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Manage Directory
                        </a>
                    </div>
                </div>
                @endif

                @if($dashCanLeaves)
                <div class="col-sm-6 col-lg-3">
                    <div class="p-3 rounded-3 bg-white border border-slate-200 shadow-2xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                    LEAVE APPROVALS
                                </span>
                                <i class="fa-solid fa-calendar-check text-purple fs-5"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-dark {{ $mgmtPendingLeaves > 0 ? 'text-danger' : '' }}">{{ $mgmtPendingLeaves }}</h3>
                            <small class="text-muted">Pending Review Requests</small>
                        </div>
                        <a href="{{ route('employee.management.leaves') }}" class="btn btn-sm {{ $mgmtPendingLeaves > 0 ? 'btn-danger' : 'btn-outline-primary' }} fw-bold mt-3 rounded-2 w-100">
                            <i class="fa-solid fa-check-double me-1"></i> Review Requests
                        </a>
                    </div>
                </div>
                @endif

                @if($dashCanAttAll)
                <div class="col-sm-6 col-lg-3">
                    <div class="p-3 rounded-3 bg-white border border-slate-200 shadow-2xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                    TEAM ATTENDANCE
                                </span>
                                <i class="fa-solid fa-clock-rotate-left text-success fs-5"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $mgmtPresentToday }}</h3>
                            <small class="text-muted">Present Staff Today</small>
                        </div>
                        <a href="{{ route('employee.management.attendance') }}" class="btn btn-sm btn-outline-success fw-bold mt-3 rounded-2 w-100">
                            <i class="fa-solid fa-list-check me-1"></i> View Attendance Log
                        </a>
                    </div>
                </div>
                @endif

                @if($dashCanTasks)
                <div class="col-sm-6 col-lg-3">
                    <div class="p-3 rounded-3 bg-white border border-slate-200 shadow-2xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                    TEAM TASKS
                                </span>
                                <i class="fa-solid fa-list-check text-info fs-5"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $mgmtActiveTasks }}</h3>
                            <small class="text-muted">Active Company Tasks</small>
                        </div>
                        <a href="{{ route('employee.management.tasks') }}" class="btn btn-sm btn-outline-info fw-bold mt-3 rounded-2 w-100">
                            <i class="fa-solid fa-plus-circle me-1"></i> Delegate & Oversee
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top border-slate-200">
                <small class="text-muted fw-bold me-2" style="font-size: 0.75rem;">DIRECT SHORTCUTS:</small>
                @if($dashCanEmployees)
                    <a href="{{ route('employee.management.employees') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-sans small">
                        <i class="fa-solid fa-users text-primary me-1"></i> Staff Directory
                    </a>
                @endif
                @if($dashCanLeaves)
                    <a href="{{ route('employee.management.leaves') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-sans small">
                        <i class="fa-solid fa-calendar-check text-purple me-1"></i> Leave Desk
                    </a>
                @endif
                @if($dashCanAttAll)
                    <a href="{{ route('employee.management.attendance') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-sans small">
                        <i class="fa-solid fa-clock-rotate-left text-success me-1"></i> Daily Attendance
                    </a>
                @endif
                @if($dashCanTasks)
                    <a href="{{ route('employee.management.tasks') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-sans small">
                        <i class="fa-solid fa-list-check text-info me-1"></i> Team Tasks
                    </a>
                @endif
                @if($dashCanPayroll)
                    <a href="{{ route('employee.management.payroll') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-sans small">
                        <i class="fa-solid fa-file-invoice-dollar text-warning me-1"></i> Staff Payroll
                    </a>
                @endif
                @if($dashCanReports)
                    <a href="{{ route('employee.management.reports') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-sans small">
                        <i class="fa-solid fa-chart-line text-danger me-1"></i> HRMS Reports
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Live Web Attendance Terminal -->
    @if($canPunch)
        @include('employee.partials.attendance_punch')
    @endif

    <!-- Metric Stat Cards Row -->
    <div class="row g-3 mb-4">
        @if($canAttView)
        <!-- 1. Today Attendance Status -->
        <div class="col-sm-6 col-xl-3">
            <div class="emp-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Today's Status</span>
                    <div class="emp-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    @if($todayAttendance || $firstPunch)
                        <span class="text-success">Present</span>
                    @else
                        <span class="text-warning">Pending</span>
                    @endif
                </h4>
                <small class="text-muted">
                    @if($firstPunch)
                        In: {{ Carbon\Carbon::parse($firstPunch->created_at)->format('h:i A') }}
                        @if($lastPunch && $lastPunch->id !== $firstPunch->id)
                            • Out: {{ Carbon\Carbon::parse($lastPunch->created_at)->format('h:i A') }}
                        @endif
                    @else
                        No punch recorded yet
                    @endif
                </small>
            </div>
        </div>

        <!-- 2. Month Attendance -->
        <div class="col-sm-6 col-xl-3">
            <div class="emp-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Month Attendance</span>
                    <div class="emp-stat-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $presentDays }} Days</h4>
                <div class="progress my-2" style="height: 5px; border-radius: 999px;">
                    @php
                        $attendancePct = min(100, round(($presentDays / max(1, date('j'))) * 100));
                    @endphp
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $attendancePct }}%"></div>
                </div>
                <small class="text-muted">{{ $halfDays }} Half-days • {{ $approvedLeavesThisMonth }} Leaves</small>
            </div>
        </div>
        @endif

        @if($canLeavesView)
        <!-- 3. Remaining Leaves -->
        <div class="col-sm-6 col-xl-3">
            <div class="emp-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Available Leaves</span>
                    <div class="emp-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                        <i class="fa-solid fa-umbrella-beach"></i>
                    </div>
                </div>
                @php
                    $totalRemaining = $remainingCasual + $remainingSick + $remainingPrivileged;
                @endphp
                <h4 class="fw-bold text-dark mb-1">{{ $totalRemaining }} Days</h4>
                <small class="text-muted">CL: {{ $remainingCasual }} • SL: {{ $remainingSick }} • PL: {{ $remainingPrivileged }}</small>
            </div>
        </div>
        @endif

        @if($canTasksView)
        <!-- 4. Assigned Tasks -->
        <div class="col-sm-6 col-xl-3">
            <div class="emp-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Assigned Tasks</span>
                    <div class="emp-stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $pendingTasksCount }} Pending</h4>
                <small class="text-muted"><a href="{{ route('employee.tasks') }}" class="text-decoration-none">View all assigned tasks &rarr;</a></small>
            </div>
        </div>
        @endif
    </div>

    @if($canAttView || $canLeavesView)
    <!-- Middle Section: Today's Punches & Leave Balances -->
    <div class="row g-3 mb-4">
        @if($canAttView)
        <!-- Today's Punch Timeline -->
        <div class="{{ $canLeavesView ? 'col-lg-7' : 'col-12' }}">
            <div class="emp-card h-100 mb-0">
                <div class="emp-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Today's Punch Activity & Shift</h6>
                    </div>
                    <span class="badge bg-light text-muted border small">{{ date('d M Y') }}</span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <!-- Shift info pill -->
                    @if($employee_info->shift)
                        <div class="p-3 rounded-3 bg-light border mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Assigned Shift</small>
                                <h6 class="fw-bold text-dark mb-0">{{ $employee_info->shift->name ?? 'Standard Shift' }}</h6>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary text-white px-2 py-1">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    {{ $employee_info->shift->start_time ?? '09:00' }} - {{ $employee_info->shift->end_time ?? '18:00' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($todayPunches->count() > 0)
                        <h6 class="text-muted fw-semibold small mb-3">Recorded Punches Today ({{ $todayPunches->count() }}):</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Location / Device</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayPunches as $index => $punch)
                                        <tr>
                                            <td class="fw-bold">{{ $index + 1 }}</td>
                                            <td>
                                                <span class="fw-semibold text-dark">
                                                    {{ Carbon\Carbon::parse($punch->created_at)->format('h:i:s A') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($index === 0)
                                                    <span class="badge bg-success bg-opacity-10 text-success">Punch IN</span>
                                                @elseif($index === $todayPunches->count() - 1)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">Latest Punch</span>
                                                @else
                                                    <span class="badge bg-info bg-opacity-10 text-info">Mid Punch</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">
                                                {{ $punch->address ?: 'Office Geo-fence' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-2 text-muted" style="font-size: 2.5rem;">
                                <i class="fa-regular fa-clock opacity-50"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No punches recorded today</h6>
                            <p class="text-muted small mb-0">Use the STAFO Mobile App or biometric kiosk to log your in-time today.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($canLeavesView)
        <!-- Leave Balance Overview Widget -->
        <div class="{{ $canAttView ? 'col-lg-5' : 'col-12' }}">
            <div class="emp-card h-100 mb-0">
                <div class="emp-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-scale-balanced text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Leave Balance ({{ date('Y') }})</h6>
                    </div>
                    @if($canLeavesApply)
                    <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-outline-primary py-1 px-2.5" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Apply
                    </a>
                    @endif
                </div>
                <div class="card-body p-3 p-md-4">
                    <!-- Casual Leave -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark small">Casual Leave (CL)</span>
                            <span class="small text-muted"><strong>{{ $remainingCasual }}</strong> / {{ $totalCasual }} Available</span>
                        </div>
                        <div class="progress" style="height: 7px; border-radius: 999px;">
                            @php $clPct = $totalCasual > 0 ? min(100, round(($remainingCasual / $totalCasual) * 100)) : 0; @endphp
                            <div class="progress-bar bg-success" style="width: {{ $clPct }}%"></div>
                        </div>
                    </div>

                    <!-- Sick Leave -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark small">Sick Leave (SL)</span>
                            <span class="small text-muted"><strong>{{ $remainingSick }}</strong> / {{ $totalSick }} Available</span>
                        </div>
                        <div class="progress" style="height: 7px; border-radius: 999px;">
                            @php $slPct = $totalSick > 0 ? min(100, round(($remainingSick / $totalSick) * 100)) : 0; @endphp
                            <div class="progress-bar bg-warning" style="width: {{ $slPct }}%"></div>
                        </div>
                    </div>

                    <!-- Privileged Leave -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark small">Privileged Leave (PL)</span>
                            <span class="small text-muted"><strong>{{ $remainingPrivileged }}</strong> / {{ $totalPrivileged }} Available</span>
                        </div>
                        <div class="progress" style="height: 7px; border-radius: 999px;">
                            @php $plPct = $totalPrivileged > 0 ? min(100, round(($remainingPrivileged / $totalPrivileged) * 100)) : 0; @endphp
                            <div class="progress-bar bg-info" style="width: {{ $plPct }}%"></div>
                        </div>
                    </div>

                    <!-- Recent Leaves Mini Table -->
                    <h6 class="text-muted fw-semibold small mb-2">Recent Requests:</h6>
                    @if($myLeaves->count() > 0)
                        <div class="list-group list-group-flush border-top border-bottom">
                            @foreach($myLeaves as $leave)
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent">
                                    <div>
                                        <div class="fw-semibold small text-dark">{{ $leave->leave_type ?: 'Leave' }} ({{ $leave->days }}d)</div>
                                        <small class="text-muted">{{ Carbon\Carbon::parse($leave->from_date)->format('d M') }} - {{ Carbon\Carbon::parse($leave->to_date)->format('d M') }}</small>
                                    </div>
                                    <div>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success bg-opacity-10 text-success">Approved</span>
                                        @elseif($leave->status === 'rejected')
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No recent leave applications submitted.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    @php
        $hasRightBottom = $canTasksView;
    @endphp
    @if($canAttView || $hasRightBottom)
    <!-- Bottom Section: Recent Attendance & Tasks -->
    <div class="row g-3">
        @if($canAttView)
        <!-- Recent Attendance History Table -->
        <div class="{{ $hasRightBottom ? 'col-lg-7' : 'col-12' }}">
            <div class="emp-card mb-0">
                <div class="emp-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-line text-info fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Recent Attendance Logs</h6>
                    </div>
                    <a href="{{ route('employee.attendance') }}" class="btn btn-sm btn-link text-decoration-none small">View Full Roster &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Date</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendances as $att)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ Carbon\Carbon::parse($att->date)->format('d M, Y') }}</span>
                                            <small class="text-muted d-block">{{ Carbon\Carbon::parse($att->date)->format('l') }}</small>
                                        </td>
                                        <td class="small">
                                            {{ $att->in_time ? Carbon\Carbon::parse($att->in_time)->format('h:i A') : '--' }}
                                        </td>
                                        <td class="small">
                                            {{ $att->out_time ? Carbon\Carbon::parse($att->out_time)->format('h:i A') : '--' }}
                                        </td>
                                        <td>
                                            @if($att->halfday == 1)
                                                <span class="badge bg-warning bg-opacity-10 text-warning">Half Day</span>
                                            @elseif($att->attendance == 'Present' || $att->in_time)
                                                <span class="badge bg-success bg-opacity-10 text-success">Present</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">Absent</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No recent attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($hasRightBottom)
        <!-- Assigned Tasks -->
        <div class="{{ $canAttView ? 'col-lg-5' : 'col-12' }}">
            @if($canTasksView)
            <!-- Tasks Card -->
            <div class="emp-card mb-0">
                <div class="emp-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Assigned Tasks</h6>
                    </div>
                    <a href="{{ route('employee.tasks') }}" class="btn btn-sm btn-link text-decoration-none small">All Tasks &rarr;</a>
                </div>
                <div class="card-body p-3">
                    @forelse($assignedTasks as $task)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border mb-2">
                            <div>
                                <h6 class="fw-semibold text-dark mb-1 small">{{ Str::limit($task->title, 32) }}</h6>
                                <small class="text-muted">
                                    <i class="fa-regular fa-clock me-1"></i> Due: {{ $task->end_date ? Carbon\Carbon::parse($task->end_date)->format('d M') : 'No deadline' }}
                                </small>
                            </div>
                            <div>
                                @if($task->status === 'completed')
                                    <span class="badge bg-success bg-opacity-15 text-success">Done</span>
                                @elseif($task->status === 'in_progress')
                                    <span class="badge bg-info bg-opacity-15 text-info">In Progress</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-15 text-secondary">Pending</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-2 mb-0">No active tasks assigned to you.</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

</div>
@endsection

@section('js')
<script>
    function respondGeoTracking(action) {
        const btnAccept = document.getElementById('btnAcceptGeo');
        const btnDecline = document.getElementById('btnDeclineGeo');
        if (btnAccept) btnAccept.disabled = true;
        if (btnDecline) btnDecline.disabled = true;

        Swal.fire({
            title: action === 'accept' ? 'Accept Location Tracking?' : (action === 'decline' ? 'Decline / Turn Off Tracking?' : 'Confirm Action'),
            text: action === 'accept' 
                ? 'Your real-time GPS coordinates will be shared with the company during shift hours.' 
                : 'Company will not be able to track your real-time location.',
            icon: action === 'accept' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'accept' ? '#10b981' : '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: action === 'accept' ? '<i class="fa-solid fa-check me-1"></i> Yes, Accept' : '<i class="fa-solid fa-xmark me-1"></i> Yes, Turn Off',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) {
                if (btnAccept) btnAccept.disabled = false;
                if (btnDecline) btnDecline.disabled = false;
                return;
            }

            Swal.fire({
                title: 'Updating status...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('employee.geoTracking.respond') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    // If accepted and browser supports geolocation, send initial GPS coordinate
                    if (action === 'accept' && navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(pos) {
                            fetch("{{ route('storeGeoLocation') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    employee_id: {{ $employee_info->id }},
                                    latitude: pos.coords.latitude,
                                    longitude: pos.coords.longitude
                                })
                            }).catch(() => {});
                        }, function() {}, { enableHighAccuracy: true });
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    if (btnAccept) btnAccept.disabled = false;
                    if (btnDecline) btnDecline.disabled = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Something went wrong.'
                    });
                }
            })
            .catch(err => {
                if (btnAccept) btnAccept.disabled = false;
                if (btnDecline) btnDecline.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not communicate with the server. Please try again.'
                });
            });
        });
    }
</script>
@endsection
