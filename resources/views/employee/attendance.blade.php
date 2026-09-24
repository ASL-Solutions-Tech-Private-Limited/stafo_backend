@extends('employee.layouts.app')

@section('title', 'My Attendance | STAFO HRMS')

@section('css')
<style>
    /* ========================================================
       STAFO Attendance Calendar & Grid Styles
       ======================================================== */
    .att-view-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .att-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #ffffff;
    }

    /* View Switcher Pill Toggle */
    .view-switcher-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .view-btn {
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .view-btn:hover {
        color: #0f172a;
    }
    .view-btn.active {
        background: #ffffff;
        color: #059669;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* Month Quick Pills Bar */
    .att-month-pills {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding: 10px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        scrollbar-width: none;
    }
    .att-month-pills::-webkit-scrollbar {
        display: none;
    }
    .att-month-pill {
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .att-month-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .att-month-pill.active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border-color: #059669;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);
    }

    /* Legend Bar */
    .att-legend-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        padding: 10px 20px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.78rem;
    }
    .att-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        color: #475569;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Calendar Grid */
    .calendar-week-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }
    .calendar-week-header > div {
        padding: 10px 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .calendar-week-header > div.weekend {
        color: #ef4444;
    }

    .calendar-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #e2e8f0;
        gap: 1px;
    }

    .calendar-day-cell {
        background: #ffffff;
        min-height: 105px;
        padding: 8px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        position: relative;
        cursor: pointer;
        transition: background-color 0.18s ease, transform 0.15s ease;
    }
    .calendar-day-cell:hover {
        background-color: #f8fafc;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .calendar-day-cell.other-month {
        background: #fbfcfd;
        opacity: 0.45;
        cursor: default;
    }

    /* Status Color Accents for Calendar Cells */
    .calendar-day-cell.status-present {
        background: #f0fdf4;
        border-top: 3px solid #10b981;
    }
    .calendar-day-cell.status-present:hover {
        background: #dcfce7;
    }
    .calendar-day-cell.status-halfday {
        background: #fefce8;
        border-top: 3px solid #eab308;
    }
    .calendar-day-cell.status-holiday {
        background: #faf5ff;
        border-top: 3px solid #a855f7;
    }
    .calendar-day-cell.status-leave {
        background: #f0f9ff;
        border-top: 3px solid #0284c7;
    }
    .calendar-day-cell.status-absent {
        background: #fef2f2;
        border-top: 3px solid #ef4444;
    }
    .calendar-day-cell.status-weekend {
        background: #fafbfc;
        border-top: 3px solid #94a3b8;
    }

    .day-cell-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .day-cell-number {
        font-weight: 700;
        font-size: 0.82rem;
        color: #1e293b;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .calendar-day-cell.is-today .day-cell-number {
        background: #10b981;
        color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }

    .badge-present {
        background-color: rgba(16, 185, 129, 0.15);
        color: #059669;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        width: fit-content;
    }
    .badge-halfday {
        background-color: rgba(234, 179, 8, 0.15);
        color: #b45309;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        width: fit-content;
    }
    .badge-holiday {
        background-color: rgba(168, 85, 247, 0.15);
        color: #7e22ce;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        max-width: 100%;
    }
    .badge-leave {
        background-color: rgba(2, 132, 199, 0.15);
        color: #0369a1;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        max-width: 100%;
    }
    .badge-absent {
        background-color: rgba(239, 68, 68, 0.15);
        color: #dc2626;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        width: fit-content;
    }
    .badge-weekend {
        background-color: #f1f5f9;
        color: #64748b;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        width: fit-content;
    }

    .time-slot-mini {
        font-size: 0.7rem;
        color: #475569;
        line-height: 1.25;
        margin-top: 3px;
    }
    .time-slot-mini strong {
        color: #0f172a;
    }

    /* Dark Mode Adjustments */
    [data-theme="dark"] .att-view-card {
        background: #111c30 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
    [data-theme="dark"] .att-card-header,
    [data-theme="dark"] .att-legend-bar {
        background: #132038 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .view-switcher-group {
        background: #0d1527 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .view-btn {
        color: #94a3b8 !important;
    }
    [data-theme="dark"] .view-btn.active {
        background: #1e293b !important;
        color: #34d399 !important;
    }
    [data-theme="dark"] .att-month-pills {
        background: #0d1527 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .att-month-pill {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #94a3b8 !important;
    }
    [data-theme="dark"] .calendar-week-header {
        background: #0d1527 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .calendar-days-grid {
        background: #1e293b !important;
    }
    [data-theme="dark"] .calendar-day-cell {
        background: #111c30 !important;
    }
    [data-theme="dark"] .calendar-day-cell:hover {
        background: #16243f !important;
    }
    [data-theme="dark"] .calendar-day-cell.other-month {
        background: #0b1220 !important;
        opacity: 0.35 !important;
    }
    [data-theme="dark"] .day-cell-number {
        color: #e2e8f0 !important;
    }
    [data-theme="dark"] .time-slot-mini {
        color: #cbd5e1 !important;
    }
    [data-theme="dark"] .time-slot-mini strong {
        color: #f8fafc !important;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .calendar-day-cell {
            min-height: 80px;
            padding: 4px;
        }
        .day-cell-number {
            font-size: 0.72rem;
            width: 20px;
            height: 20px;
        }
        .time-slot-mini {
            font-size: 0.62rem;
        }
        .badge-present, .badge-halfday, .badge-holiday, .badge-leave, .badge-absent, .badge-weekend {
            font-size: 0.6rem;
            padding: 2px 4px;
        }
        .att-legend-bar {
            font-size: 0.7rem;
            gap: 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Page Header with Filter -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Attendance & Punches</h4>
            <span class="text-muted small">View your monthly attendance records, calendar overview, and punch timeline</span>
        </div>

        <!-- Month & Year Filter Form -->
        <form action="{{ route('employee.attendance') }}" method="GET" class="d-flex align-items-center gap-2" id="attendanceFilterForm">
            <select name="month" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select form-select-sm" style="width: 100px;" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="fa-solid fa-filter me-1"></i> Filter
            </button>
        </form>
    </div>

    <!-- Attendance & Shift Guidelines Marquee Ticker -->
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-business-time"></i>
            <span>Shift Guidelines</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                <span class="marquee-chip chip-attendance">
                    <i class="fa-solid fa-fingerprint text-success"></i>
                    <strong>Dual Punch:</strong> Remember to log both your Punch IN at shift start and Punch OUT when leaving.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-policy">
                    <i class="fa-solid fa-location-dot text-primary"></i>
                    <strong>GPS Geofence:</strong> Mobile and kiosk punches require location access to verify valid office coordinates.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-holiday">
                    <i class="fa-solid fa-clock text-warning"></i>
                    <strong>Grace Period:</strong> Shifts include a standard grace window for arrival before late-mark calculation.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-payroll">
                    <i class="fa-solid fa-file-signature text-info"></i>
                    <strong>Regularization:</strong> Missed a punch due to outdoor duty or glitch? Request regularization within 48 hours.
                </span>
            </marquee>
        </div>
        <div class="d-none d-md-flex align-items-center text-muted small ps-2 border-start" style="font-size: 0.72rem; white-space: nowrap;">
            <i class="fa-solid fa-hand-pointer text-warning me-1"></i> Hover to pause
        </div>
    </div>

    {{-- Interactive Live Attendance Punch Card & Terminal --}}
    @include('employee.partials.attendance_punch')

    <!-- Monthly Summary Metric Stat Badges -->
    <div class="row g-3 mb-4">
        <!-- 1. Present -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border shadow-sm rounded-4 p-3 h-100" style="border-color: #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Present</span>
                    <span class="badge bg-success-subtle text-success rounded-circle p-2">
                        <i class="fa-solid fa-user-check"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-success mb-0">{{ $presentCount }}</h4>
                <small class="text-muted" style="font-size: 0.7rem;">Days Logged</small>
            </div>
        </div>

        <!-- 2. Half-days -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border shadow-sm rounded-4 p-3 h-100" style="border-color: #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Half-days</span>
                    <span class="badge bg-warning-subtle text-warning rounded-circle p-2">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-warning mb-0">{{ $halfDayCount }}</h4>
                <small class="text-muted" style="font-size: 0.7rem;">Partial Shifts</small>
            </div>
        </div>

        <!-- 3. Absent -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border shadow-sm rounded-4 p-3 h-100" style="border-color: #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Absent</span>
                    <span class="badge bg-danger-subtle text-danger rounded-circle p-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-danger mb-0">{{ $absentCount }}</h4>
                <small class="text-muted" style="font-size: 0.7rem;">Working Days</small>
            </div>
        </div>

        <!-- 4. Holidays -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border shadow-sm rounded-4 p-3 h-100" style="border-color: #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Holidays</span>
                    <span class="badge rounded-circle p-2" style="background-color: rgba(168, 85, 247, 0.15); color: #9333ea;">
                        <i class="fa-solid fa-umbrella-beach"></i>
                    </span>
                </div>
                <h4 class="fw-bold mb-0" style="color: #9333ea;">{{ count($holidayDates) }}</h4>
                <small class="text-muted" style="font-size: 0.7rem;">Official Offs</small>
            </div>
        </div>

        <!-- 5. Approved Leaves -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border shadow-sm rounded-4 p-3 h-100" style="border-color: #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Leaves</span>
                    <span class="badge bg-info-subtle text-info rounded-circle p-2">
                        <i class="fa-solid fa-calendar-check"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-info mb-0">{{ count($leaveDates) }}</h4>
                <small class="text-muted" style="font-size: 0.7rem;">Approved</small>
            </div>
        </div>

        <!-- 6. Weekly Offs -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border shadow-sm rounded-4 p-3 h-100" style="border-color: #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Weekends</span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-circle p-2">
                        <i class="fa-solid fa-bed"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-secondary mb-0">{{ $weekendCount }}</h4>
                <small class="text-muted" style="font-size: 0.7rem;">Sundays</small>
            </div>
        </div>
    </div>

    <!-- Main View Control Card (Houses Calendar and/or Table View) -->
    <div class="att-view-card">
        <!-- Card Header Toolbar & View Toggle Switcher -->
        <div class="att-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-white shadow-xs" style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-calendar-days fs-6"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" id="viewTitleText">
                            Monthly Attendance Roster
                        </h6>
                        <small class="text-muted" style="font-size: 0.74rem;">
                            {{ date('F Y', mktime(0, 0, 0, (int)$month, 1, $year)) }}
                        </small>
                    </div>
                </div>

                @php
                    $curMonthObj = \Carbon\Carbon::createFromDate($year, (int)$month, 1);
                    $prevMonthObj = $curMonthObj->copy()->subMonth();
                    $nextMonthObj = $curMonthObj->copy()->addMonth();
                @endphp
                <div class="btn-group btn-group-sm ms-2">
                    <a href="{{ route('employee.attendance', ['month' => $prevMonthObj->format('m'), 'year' => $prevMonthObj->format('Y')]) }}" 
                       class="btn btn-outline-secondary btn-sm" title="Previous Month: {{ $prevMonthObj->format('F Y') }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <span class="btn btn-light btn-sm fw-bold px-2 pointer-events-none text-dark">
                        {{ $curMonthObj->format('M Y') }}
                    </span>
                    <a href="{{ route('employee.attendance', ['month' => $nextMonthObj->format('m'), 'year' => $nextMonthObj->format('Y')]) }}" 
                       class="btn btn-outline-secondary btn-sm" title="Next Month: {{ $nextMonthObj->format('F Y') }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- View Mode Switcher: Calendar / Table / Both -->
            <div class="view-switcher-group">
                <button type="button" class="view-btn active" id="btnViewCalendar" onclick="switchAttendanceView('calendar')">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Calendar View</span>
                </button>
                <button type="button" class="view-btn" id="btnViewTable" onclick="switchAttendanceView('table')">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Table View</span>
                </button>
                <button type="button" class="view-btn" id="btnViewBoth" onclick="switchAttendanceView('both')">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Both</span>
                </button>
            </div>
        </div>

        <!-- Month Quick Jump Pills -->
        <div class="att-month-pills">
            @for($m = 1; $m <= 12; $m++)
                @php
                    $mStr = sprintf('%02d', $m);
                    $isActive = ($month == $mStr);
                @endphp
                <a href="{{ route('employee.attendance', ['month' => $mStr, 'year' => $year]) }}" 
                   class="att-month-pill {{ $isActive ? 'active' : '' }}">
                    {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                </a>
            @endfor
        </div>

        <!-- Legend Bar -->
        <div class="att-legend-bar">
            <span class="text-muted fw-bold me-1" style="font-size: 0.72rem;">LEGEND:</span>
            <div class="att-legend-item">
                <span class="legend-dot" style="background: #10b981;"></span>
                <span>Present</span>
            </div>
            <div class="att-legend-item">
                <span class="legend-dot" style="background: #eab308;"></span>
                <span>Half Day</span>
            </div>
            <div class="att-legend-item">
                <span class="legend-dot" style="background: #ef4444;"></span>
                <span>Absent</span>
            </div>
            <div class="att-legend-item">
                <span class="legend-dot" style="background: #a855f7;"></span>
                <span>Holiday</span>
            </div>
            <div class="att-legend-item">
                <span class="legend-dot" style="background: #0284c7;"></span>
                <span>Leave</span>
            </div>
            <div class="att-legend-item">
                <span class="legend-dot" style="background: #94a3b8;"></span>
                <span>Weekly Off</span>
            </div>
        </div>

        <!-- ========================================================
             1. CALENDAR VIEW CONTAINER
             ======================================================== -->
        <div id="attendanceCalendarSection">
            <!-- 7-Day Header -->
            <div class="calendar-week-header">
                <div class="weekend">Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>

            @php
                $firstDayOfMonth = \Carbon\Carbon::createFromDate($year, (int)$month, 1);
                $dayOfWeekOffset = $firstDayOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday ... 6 = Saturday
                $totalDaysInMonth = $firstDayOfMonth->daysInMonth;
                $todayDateStr = date('Y-m-d');
            @endphp

            <!-- Days Grid -->
            <div class="calendar-days-grid">
                <!-- Leading Blank Cells (previous month spill) -->
                @for($b = 0; $b < $dayOfWeekOffset; $b++)
                    <div class="calendar-day-cell other-month">
                        <div class="day-cell-top">
                            <span class="day-cell-number text-muted opacity-50">•</span>
                        </div>
                    </div>
                @endfor

                <!-- Current Month Days -->
                @for($day = 1; $day <= $totalDaysInMonth; $day++)
                    @php
                        $dayDate = \Carbon\Carbon::createFromDate($year, (int)$month, $day);
                        $dateKey = $dayDate->format('Y-m-d');
                        $isToday = ($dateKey === $todayDateStr);
                        $isSunday = $dayDate->isSunday();
                        $isSaturday = $dayDate->isSaturday();
                        $isPastOrToday = $dayDate->lte(\Carbon\Carbon::today());
                        $isFuture = $dayDate->gt(\Carbon\Carbon::today());

                        $attRecord = $monthlyAttendances[$dateKey] ?? null;
                        $holidayRecord = $holidayDates[$dateKey] ?? null;
                        $leaveRecord = $leaveDates[$dateKey] ?? null;
                        $dayPunches = $monthlyPunches[$dateKey] ?? collect();

                        // Determine primary cell status
                        $statusClass = '';
                        $statusBadge = '';
                        $inTimeDisplay = '';
                        $outTimeDisplay = '';
                        $workHoursDisplay = '';

                        if ($holidayRecord) {
                            $statusClass = 'status-holiday';
                            $statusBadge = '<span class="badge-holiday" title="' . e($holidayRecord->title) . '"><i class="fa-solid fa-umbrella-beach"></i> ' . Str::limit($holidayRecord->title, 12) . '</span>';
                        } elseif ($leaveRecord) {
                            $statusClass = 'status-leave';
                            $leaveName = $leaveRecord->leavetype->name ?? 'Approved Leave';
                            $statusBadge = '<span class="badge-leave" title="' . e($leaveName) . '"><i class="fa-solid fa-calendar-check"></i> ' . Str::limit($leaveName, 12) . '</span>';
                        } elseif ($attRecord) {
                            if ($attRecord->halfday == 1) {
                                $statusClass = 'status-halfday';
                                $statusBadge = '<span class="badge-halfday"><i class="fa-solid fa-hourglass-half"></i> Half Day</span>';
                            } else {
                                $statusClass = 'status-present';
                                $statusBadge = '<span class="badge-present"><i class="fa-solid fa-circle-check"></i> Present</span>';
                            }

                            if ($attRecord->in_time) {
                                $inTimeDisplay = \Carbon\Carbon::parse($attRecord->in_time)->format('h:i A');
                            }
                            if ($attRecord->out_time) {
                                $outTimeDisplay = \Carbon\Carbon::parse($attRecord->out_time)->format('h:i A');
                            }
                            if ($attRecord->in_time && $attRecord->out_time) {
                                $inC = \Carbon\Carbon::parse($attRecord->in_time);
                                $outC = \Carbon\Carbon::parse($attRecord->out_time);
                                $diffH = $inC->diffInHours($outC);
                                $diffM = $inC->diffInMinutes($outC) % 60;
                                $workHoursDisplay = $diffH . 'h ' . $diffM . 'm';
                            }
                        } elseif ($isSunday) {
                            $statusClass = 'status-weekend';
                            $statusBadge = '<span class="badge-weekend"><i class="fa-solid fa-bed"></i> Weekly Off</span>';
                        } elseif ($isPastOrToday && $dayDate->lt(\Carbon\Carbon::today())) {
                            $statusClass = 'status-absent';
                            $statusBadge = '<span class="badge-absent"><i class="fa-solid fa-circle-xmark"></i> Absent</span>';
                        }
                    @endphp

                    <div class="calendar-day-cell {{ $statusClass }} {{ $isToday ? 'is-today' : '' }}" 
                         onclick="openDayDetailModal('{{ $dateKey }}', '{{ $dayDate->format('l, d F Y') }}', '{{ $statusClass }}', '{{ addslashes($inTimeDisplay) }}', '{{ addslashes($outTimeDisplay) }}', '{{ addslashes($workHoursDisplay) }}', '{{ $holidayRecord ? addslashes($holidayRecord->title) : '' }}', '{{ $leaveRecord ? addslashes($leaveRecord->leavetype->name ?? 'Leave') : '' }}', {{ json_encode($dayPunches) }})"
                         title="Click to view full details for {{ $dayDate->format('d M, Y') }}">
                        
                        <!-- Top Row: Date Number & Today Indicator -->
                        <div class="day-cell-top">
                            <span class="day-cell-number {{ $isSunday ? 'text-danger' : '' }}">
                                {{ $day }}
                            </span>
                            @if($isToday)
                                <span class="badge bg-success text-white px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem; letter-spacing: 0.3px;">
                                    TODAY
                                </span>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        @if($statusBadge)
                            <div class="mb-1">
                                {!! $statusBadge !!}
                            </div>
                        @endif

                        <!-- Punch IN / OUT Details -->
                        @if($inTimeDisplay)
                            <div class="time-slot-mini text-truncate">
                                <span class="text-success"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i>In:</span>
                                <strong>{{ $inTimeDisplay }}</strong>
                            </div>
                        @endif
                        @if($outTimeDisplay)
                            <div class="time-slot-mini text-truncate">
                                <span class="text-danger"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i>Out:</span>
                                <strong>{{ $outTimeDisplay }}</strong>
                            </div>
                        @endif

                        <!-- Total Working Hours Tag -->
                        @if($workHoursDisplay)
                            <div class="mt-auto pt-1">
                                <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.62rem;">
                                    <i class="fa-solid fa-stopwatch me-1 text-primary"></i>{{ $workHoursDisplay }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endfor

                <!-- Trailing Blank Cells -->
                @php
                    $trailingBlanks = (7 - (($dayOfWeekOffset + $totalDaysInMonth) % 7)) % 7;
                @endphp
                @for($tb = 0; $tb < $trailingBlanks; $tb++)
                    <div class="calendar-day-cell other-month">
                        <div class="day-cell-top">
                            <span class="day-cell-number text-muted opacity-50">•</span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- ========================================================
             2. TABULAR VIEW CONTAINER (Daily Attendance Records Table)
             ======================================================== -->
        <div id="attendanceTableSection" class="p-0">
            <div class="p-3 bg-light border-top border-bottom d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark small">
                    <i class="fa-solid fa-list-check me-1 text-success"></i> Recorded Punches & Daily Timesheet
                </span>
                <span class="badge bg-white text-muted border small">{{ $attendances->total() }} Logged Records</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Day</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Working Hours</th>
                            <th class="pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $record)
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">{{ Carbon\Carbon::parse($record->date)->format('d M, Y') }}</td>
                                <td class="text-muted small">{{ Carbon\Carbon::parse($record->date)->format('l') }}</td>
                                <td class="small">
                                    @if($record->in_time)
                                        <span class="text-success fw-semibold">
                                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i>{{ Carbon\Carbon::parse($record->in_time)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if($record->out_time)
                                        <span class="text-danger fw-semibold">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>{{ Carbon\Carbon::parse($record->out_time)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if($record->in_time && $record->out_time)
                                        @php
                                            $in = Carbon\Carbon::parse($record->in_time);
                                            $out = Carbon\Carbon::parse($record->out_time);
                                            $diffHours = $in->diffInHours($out);
                                            $diffMins = $in->diffInMinutes($out) % 60;
                                        @endphp
                                        <span class="badge bg-light text-dark border">{{ $diffHours }}h {{ $diffMins }}m</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    @if($record->halfday == 1)
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 px-2.5 py-1">Half Day</span>
                                    @elseif($record->attendance == 'Present' || $record->in_time)
                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-2.5 py-1">Present</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-2.5 py-1">Absent</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">
                                    <div class="my-2">
                                        <i class="fa-solid fa-calendar-xmark text-muted fs-3 mb-2 d-block"></i>
                                        No attendance records found for {{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="p-3 border-top">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Day Attendance Detail Modal -->
<div class="modal fade" id="attendanceDayModal" tabindex="-1" aria-labelledby="attendanceDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalDayDate">
                        Day Details
                    </h6>
                    <small class="text-muted" id="modalDaySub">Attendance breakdown & punches</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Status Badge Banner -->
                <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" id="modalStatusBanner" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div>
                        <span class="text-muted small d-block">Official Status</span>
                        <h6 class="fw-bold mb-0 text-dark" id="modalStatusTitle">Present</h6>
                    </div>
                    <span id="modalStatusBadge"></span>
                </div>

                <!-- Timing Stats -->
                <div class="row g-2 mb-3 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-3 bg-light border">
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Punch IN</small>
                            <span class="fw-bold text-success" id="modalInTime">--</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3 bg-light border">
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Punch OUT</small>
                            <span class="fw-bold text-danger" id="modalOutTime">--</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3 bg-light border">
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Duration</small>
                            <span class="fw-bold text-dark" id="modalWorkHours">--</span>
                        </div>
                    </div>
                </div>

                <!-- Punches Timeline -->
                <div class="border rounded-3 p-3 bg-white">
                    <h6 class="fw-bold text-dark mb-2 small d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i> Recorded Punches</span>
                        <span class="badge bg-light text-muted border" id="modalPunchCount">0 Logs</span>
                    </h6>
                    <div id="modalPunchList" class="small">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm px-3 rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // View Switcher: 'calendar' | 'table' | 'both'
    function switchAttendanceView(mode) {
        const calSection = document.getElementById('attendanceCalendarSection');
        const tableSection = document.getElementById('attendanceTableSection');
        const btnCal = document.getElementById('btnViewCalendar');
        const btnTable = document.getElementById('btnViewTable');
        const btnBoth = document.getElementById('btnViewBoth');
        const titleText = document.getElementById('viewTitleText');

        btnCal.classList.remove('active');
        btnTable.classList.remove('active');
        btnBoth.classList.remove('active');

        if (mode === 'calendar') {
            calSection.style.display = 'block';
            tableSection.style.display = 'none';
            btnCal.classList.add('active');
            titleText.textContent = 'Monthly Attendance Calendar';
        } else if (mode === 'table') {
            calSection.style.display = 'none';
            tableSection.style.display = 'block';
            btnTable.classList.add('active');
            titleText.textContent = 'Daily Attendance Records';
        } else {
            // 'both'
            calSection.style.display = 'block';
            tableSection.style.display = 'block';
            btnBoth.classList.add('active');
            titleText.textContent = 'Attendance Calendar & Detailed Records';
        }

        try {
            localStorage.setItem('stafo_attendance_view_pref', mode);
        } catch (e) {}
    }

    // Restore saved view preference
    document.addEventListener('DOMContentLoaded', function() {
        let savedPref = 'both';
        try {
            savedPref = localStorage.getItem('stafo_attendance_view_pref') || 'both';
        } catch (e) {}
        switchAttendanceView(savedPref);
    });

    // Open Day Detail Modal
    function openDayDetailModal(dateKey, fullDateStr, statusClass, inTime, outTime, workHours, holidayTitle, leaveName, punches) {
        document.getElementById('modalDayDate').textContent = fullDateStr;
        document.getElementById('modalInTime').textContent = inTime || '--';
        document.getElementById('modalOutTime').textContent = outTime || '--';
        document.getElementById('modalWorkHours').textContent = workHours || '--';

        const statusTitleEl = document.getElementById('modalStatusTitle');
        const statusBadgeEl = document.getElementById('modalStatusBadge');

        if (holidayTitle) {
            statusTitleEl.textContent = 'Official Holiday: ' + holidayTitle;
            statusBadgeEl.innerHTML = '<span class="badge bg-purple text-white px-2.5 py-1 rounded-pill" style="background:#9333ea;">Holiday</span>';
        } else if (leaveName) {
            statusTitleEl.textContent = 'Approved Leave: ' + leaveName;
            statusBadgeEl.innerHTML = '<span class="badge bg-info text-white px-2.5 py-1 rounded-pill">Leave</span>';
        } else if (statusClass === 'status-present') {
            statusTitleEl.textContent = 'Present (Shift Completed)';
            statusBadgeEl.innerHTML = '<span class="badge bg-success text-white px-2.5 py-1 rounded-pill">Present</span>';
        } else if (statusClass === 'status-halfday') {
            statusTitleEl.textContent = 'Half Day Marked';
            statusBadgeEl.innerHTML = '<span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">Half Day</span>';
        } else if (statusClass === 'status-weekend') {
            statusTitleEl.textContent = 'Weekly Off / Sunday';
            statusBadgeEl.innerHTML = '<span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill">Weekly Off</span>';
        } else if (statusClass === 'status-absent') {
            statusTitleEl.textContent = 'Absent (No Punch Logged)';
            statusBadgeEl.innerHTML = '<span class="badge bg-danger text-white px-2.5 py-1 rounded-pill">Absent</span>';
        } else {
            statusTitleEl.textContent = 'Future Date';
            statusBadgeEl.innerHTML = '<span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill">Upcoming</span>';
        }

        // Populate Punches
        const punchList = document.getElementById('modalPunchList');
        const punchCountEl = document.getElementById('modalPunchCount');
        punchList.innerHTML = '';

        if (punches && punches.length > 0) {
            punchCountEl.textContent = punches.length + ' Recorded';
            let listHtml = '<ul class="list-group list-group-flush">';
            punches.forEach((p, idx) => {
                const inStr = p.punch_in ? new Date(p.punch_in).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : (p.created_at ? new Date(p.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : '--');
                const outStr = p.punch_out ? new Date(p.punch_out).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : 'Still In';
                
                listHtml += `
                    <li class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge bg-light text-dark border me-1">#${idx + 1}</span>
                            <span class="text-success fw-bold me-2"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i>${inStr}</span>
                            <span class="text-danger fw-bold"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i>${outStr}</span>
                        </div>
                        <span class="badge bg-success-subtle text-success small">Logged</span>
                    </li>
                `;
            });
            listHtml += '</ul>';
            punchList.innerHTML = listHtml;
        } else {
            punchCountEl.textContent = '0 Logs';
            punchList.innerHTML = '<div class="text-muted text-center py-2">No raw biometric/terminal punches found for this day.</div>';
        }

        const modalEl = document.getElementById('attendanceDayModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
</script>
@endsection
@endsection
