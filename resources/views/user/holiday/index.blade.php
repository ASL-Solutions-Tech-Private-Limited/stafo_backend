@extends('user.layouts.app')
@section('title', 'Company Holiday Calendar | STAFO HRMS')

@section('content')
@include('user.layouts.alert')

<style>
    /* ========================================================
       Company Holiday Calendar Styles (Light & Dark Theme)
       ======================================================== */
    .company-holiday-hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 45%, #0284c7 100%);
        border-radius: 20px;
        padding: 1.75rem 2rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.35);
        margin-bottom: 1.5rem;
    }
    .company-holiday-hero::after {
        content: '';
        position: absolute;
        right: -20px;
        bottom: -20px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .holiday-stat-box {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 14px;
        padding: 12px 18px;
        backdrop-filter: blur(8px);
        min-width: 130px;
    }

    /* View Switcher */
    .calendar-view-toggle .btn {
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 7px 16px;
        transition: all 0.2s ease;
    }
    .calendar-view-toggle .btn.active {
        background: var(--stafo-brand-gradient, linear-gradient(135deg, #10b981 0%, #059669 100%)) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.35);
    }

    /* Month Quick Pills Navigation */
    .month-pill-bar {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding: 4px 2px;
        scrollbar-width: none;
    }
    .month-pill-bar::-webkit-scrollbar {
        display: none;
    }
    .month-pill-btn {
        padding: 5px 14px;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .month-pill-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .month-pill-btn.active {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3);
    }

    /* Calendar Grid Layout */
    .calendar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
    }
    .calendar-header-toolbar {
        padding: 1.25rem 1.75rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }

    .calendar-grid-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }
    .calendar-grid-header > div {
        padding: 12px 6px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .calendar-grid-header > div.weekend {
        color: #ef4444;
    }

    .calendar-grid-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #e2e8f0;
        gap: 1px;
    }

    .calendar-day-cell {
        background: #ffffff;
        min-height: 110px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        position: relative;
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .calendar-day-cell:hover {
        background-color: #f8fafc;
    }
    .calendar-day-cell.other-month {
        background: #fafafa;
        color: #cbd5e1;
        opacity: 0.6;
    }
    .calendar-day-cell.weekend-cell {
        background: #fdfdfd;
    }

    .day-number-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .day-number {
        font-weight: 700;
        font-size: 0.85rem;
        color: #1e293b;
        width: 26px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .calendar-day-cell.is-today .day-number {
        background: #10b981;
        color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }
    .calendar-day-cell.other-month .day-number {
        color: #94a3b8;
    }

    /* Highlighted Holiday Date Cell */
    .calendar-day-cell.has-holiday-date {
        background: linear-gradient(180deg, #ecfdf5 0%, #f0fdf4 100%) !important;
        border-top: 3px solid #10b981 !important;
        box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.28) !important;
    }
    .calendar-day-cell.has-holiday-date .day-number {
        background: #059669;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.35);
    }
    .calendar-day-cell.has-holiday-date.is-today .day-number {
        background: #0d9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.35);
    }
    .calendar-day-cell.has-holiday-date:hover {
        background: #e6fcf0 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2), inset 0 0 0 1px #10b981 !important;
    }

    /* Event Chip Inside Calendar Day */
    .holiday-chip {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(2, 132, 199, 0.15) 100%);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-left: 3px solid #10b981;
        border-radius: 6px;
        padding: 4px 6px;
        margin-top: 3px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #065f46;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .holiday-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }
    .holiday-chip i {
        font-size: 0.7rem;
        color: #10b981;
    }

    .holiday-chip.multi-day {
        border-left-color: #0284c7;
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.15) 0%, rgba(14, 165, 233, 0.12) 100%);
        color: #0369a1;
    }
    .holiday-chip.multi-day i {
        color: #0284c7;
    }

    /* ========================================================
       Dark Mode Calendar Overrides
       ======================================================== */
    [data-theme="dark"] .company-holiday-hero {
        background: linear-gradient(135deg, #022c22 0%, #064e3b 45%, #0c4a6e 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }
    [data-theme="dark"] .holiday-stat-box {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
    }

    [data-theme="dark"] .month-pill-btn {
        background: #16243f;
        border-color: rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
    }
    [data-theme="dark"] .month-pill-btn:hover {
        background: #1e3256;
        color: #ffffff;
    }
    [data-theme="dark"] .month-pill-btn.active {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }

    [data-theme="dark"] .calendar-card {
        background: #111c30 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .calendar-header-toolbar {
        background: #132038 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .calendar-grid-header {
        background: #16243f !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .calendar-grid-header > div {
        color: #94a3b8 !important;
    }
    [data-theme="dark"] .calendar-grid-header > div.weekend {
        color: #f87171 !important;
    }

    [data-theme="dark"] .calendar-grid-days {
        background: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .calendar-day-cell {
        background: #111c30 !important;
    }
    [data-theme="dark"] .calendar-day-cell:hover {
        background-color: #16243f !important;
    }
    [data-theme="dark"] .calendar-day-cell.other-month {
        background: #0d1527 !important;
        opacity: 0.45;
    }
    [data-theme="dark"] .calendar-day-cell.weekend-cell {
        background: #10192b !important;
    }
    [data-theme="dark"] .day-number {
        color: #f8fafc !important;
    }
    [data-theme="dark"] .calendar-day-cell.other-month .day-number {
        color: #64748b !important;
    }
    [data-theme="dark"] .calendar-day-cell.has-holiday-date {
        background: linear-gradient(180deg, rgba(16, 185, 129, 0.18) 0%, rgba(6, 78, 59, 0.25) 100%) !important;
        border-top: 3px solid #34d399 !important;
        box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.35) !important;
    }
    [data-theme="dark"] .calendar-day-cell.has-holiday-date .day-number {
        background: #10b981 !important;
        color: #042f2e !important;
        font-weight: 800;
    }
    [data-theme="dark"] .calendar-day-cell.has-holiday-date:hover {
        background: rgba(16, 185, 129, 0.26) !important;
    }

    [data-theme="dark"] .holiday-chip {
        background: rgba(16, 185, 129, 0.22) !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
        border-left: 3px solid #34d399 !important;
        color: #a7f3d0 !important;
    }
    [data-theme="dark"] .holiday-chip i {
        color: #34d399 !important;
    }
    [data-theme="dark"] .holiday-chip.multi-day {
        background: rgba(2, 132, 199, 0.22) !important;
        border-color: rgba(2, 132, 199, 0.4) !important;
        border-left: 3px solid #38bdf8 !important;
        color: #7dd3fc !important;
    }
    [data-theme="dark"] .holiday-chip.multi-day i {
        color: #38bdf8 !important;
    }

    @media (max-width: 768px) {
        .calendar-day-cell {
            min-height: 80px;
            padding: 4px 6px;
        }
        .day-number {
            width: 22px;
            height: 22px;
            font-size: 0.75rem;
        }
        .holiday-chip {
            font-size: 0.65rem;
            padding: 2px 4px;
        }
    }
</style>

@php
    $currentDate = Carbon\Carbon::now();
    $upcomingHolidays = $holidayes->filter(function($h) use ($currentDate) {
        return Carbon\Carbon::parse($h->start_date)->gte($currentDate->copy()->startOfDay());
    })->sortBy('start_date');
    $nextHoliday = $upcomingHolidays->first();
    $totalHolidays = $holidayes->count();
    $upcomingCount = $upcomingHolidays->count();
@endphp

<!-- Top Festive Management Banner -->
<div class="company-holiday-hero">
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(255,255,255,0.2); color: #ffffff; font-size: 0.76rem; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-umbrella-beach me-1 text-warning"></i> Company Leave Observances
                </span>
                <span class="text-white-50 small">• Organization Calendar {{ date('Y') }}</span>
            </div>
            <h3 class="fw-bold text-white mb-2">
                Company Holiday Calendar
            </h3>
            <p class="text-white small mb-0 opacity-90" style="max-width: 580px;">
                Manage corporate holidays, festive leaves, and mandatory company off-days. Changes reflect instantly across all employee dashboards.
            </p>

            <!-- Next Upcoming Holiday Highlight Strip -->
            @if($nextHoliday)
                @php
                    $daysDiff = $currentDate->copy()->startOfDay()->diffInDays(Carbon\Carbon::parse($nextHoliday->start_date), false);
                @endphp
                <div class="d-inline-flex align-items-center gap-3 mt-3 px-3 py-2 rounded-3" style="background: rgba(0, 0, 0, 0.22); border: 1px solid rgba(255, 255, 255, 0.2);">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.35); color: #a7f3d0; font-size: 1.1rem;">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div class="lh-sm">
                        <span class="d-block text-white-50" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Next Upcoming Holiday</span>
                        <strong class="text-white" style="font-size: 0.95rem;">{{ $nextHoliday->title }}</strong>
                        <small class="text-white-50 ms-2">
                            ({{ Carbon\Carbon::parse($nextHoliday->start_date)->format('d M, Y') }} • {{ Carbon\Carbon::parse($nextHoliday->start_date)->format('l') }})
                        </small>
                    </div>
                    <span class="badge rounded-pill ms-auto px-2.5 py-1" style="background: #10b981; color: #ffffff; font-size: 0.75rem;">
                        @if($daysDiff == 0)
                            Today! 🎉
                        @elseif($daysDiff == 1)
                            Tomorrow!
                        @else
                            In {{ $daysDiff }} Days
                        @endif
                    </span>
                </div>
            @endif
        </div>

        <!-- Right: Metrics & Add Holiday Button -->
        <div class="col-lg-5 text-lg-end">
            <div class="d-inline-flex flex-column align-items-lg-end gap-3">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    <div class="holiday-stat-box text-center">
                        <span class="d-block text-white-50 small fw-semibold">Total Holidays</span>
                        <h4 class="fw-bold text-white mb-0">{{ $totalHolidays }}</h4>
                    </div>
                    <div class="holiday-stat-box text-center">
                        <span class="d-block text-white-50 small fw-semibold">Upcoming</span>
                        <h4 class="fw-bold text-white mb-0">{{ $upcomingCount }}</h4>
                    </div>
                    <div class="holiday-stat-box text-center">
                        <span class="d-block text-white-50 small fw-semibold">Year</span>
                        <h4 class="fw-bold text-white mb-0">{{ date('Y') }}</h4>
                    </div>
                </div>

                <a href="{{ route('holiday.create') }}" class="btn btn-light fw-bold px-3 py-2 shadow-sm rounded-3" style="color: #064e3b;">
                    <i class="fa-solid fa-plus-circle me-1 text-success"></i> Add New Holiday
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Official Festive & Holiday Marquee Ticker -->
<div class="stafo-marquee-bar mb-4">
    <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 45%, #b45309 100%);">
        <span class="pulse-dot"></span>
        <i class="fa-solid fa-bullhorn"></i>
        <span>Holiday Ticker</span>
    </div>
    <div class="stafo-marquee-container">
        <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
            @forelse ($holidayes as $h)
                @php
                    $hDate = Carbon\Carbon::parse($h->start_date);
                    $daysLeft = $currentDate->copy()->startOfDay()->diffInDays($hDate, false);
                    $festInfo = $h->festival_data;
                @endphp
                <span class="marquee-chip chip-holiday" onclick="switchViewMode('list')">
                    <span>{{ $festInfo['emoji'] }}</span>
                    <strong>{{ $h->title }}</strong>: {{ $hDate->format('d M, Y') }} ({{ $hDate->format('l') }})
                    @if($daysLeft == 0)
                        <span class="badge bg-danger text-white py-0.5 px-1.5 ms-1" style="font-size: 0.68rem;">Today 🎉</span>
                    @elseif($daysLeft == 1)
                        <span class="badge bg-warning text-dark py-0.5 px-1.5 ms-1" style="font-size: 0.68rem;">Tomorrow</span>
                    @elseif($daysLeft > 1 && $daysLeft <= 30)
                        <span class="badge bg-success text-white py-0.5 px-1.5 ms-1" style="font-size: 0.68rem;">In {{ $daysLeft }} days</span>
                    @endif
                </span>
                <span class="marquee-divider">•</span>
            @empty
                <span class="marquee-chip chip-notice">
                    <i class="fa-solid fa-calendar-check text-primary"></i>
                    <span>Official company holiday calendar {{ date('Y') }} - Click Add New Holiday to declare upcoming observances.</span>
                </span>
            @endforelse
            <span class="marquee-chip chip-policy">
                <i class="fa-solid fa-circle-info text-info"></i>
                <span>Tip: Hover mouse to pause ticker • Click any holiday chip to view in list</span>
            </span>
        </marquee>
    </div>
</div>

<!-- Main Calendar / List Management Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 calendar-card">
    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 calendar-header-toolbar">
        <!-- Left: Month Heading -->
        <div class="d-flex align-items-center gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="currentMonthYearHeading">
                    <i class="fa-solid fa-calendar-days text-primary"></i>
                    <span>Loading Calendar...</span>
                </h5>
                <small class="text-muted" id="currentMonthSummaryText">Showing holidays for current month</small>
            </div>
        </div>

        <!-- Right: Controls (Month Nav, View Mode, Add Button) -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Navigation Prev/Today/Next -->
            <div class="btn-group btn-group-sm shadow-xs" role="group">
                <button type="button" class="btn btn-outline-secondary px-2.5 py-1.5" onclick="changeMonth(-1)" title="Previous Month">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary px-3 py-1.5 fw-semibold" onclick="goToToday()">
                    Today
                </button>
                <button type="button" class="btn btn-outline-secondary px-2.5 py-1.5" onclick="changeMonth(1)" title="Next Month">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- View Switcher (Calendar vs List) -->
            <div class="btn-group btn-group-sm calendar-view-toggle p-1 rounded-3 bg-light border" role="group">
                <button type="button" class="btn active" id="btnCalendarView" onclick="switchViewMode('calendar')">
                    <i class="fa-solid fa-calendar-week me-1"></i> Calendar
                </button>
                <button type="button" class="btn text-muted" id="btnListView" onclick="switchViewMode('list')">
                    <i class="fa-solid fa-list-ul me-1"></i> List View
                </button>
            </div>

            <a href="{{ route('holiday.create') }}" class="btn btn-primary btn-sm rounded-3 py-1.5 px-3 fw-semibold">
                <i class="fa-solid fa-plus me-1"></i> Add Holiday
            </a>
        </div>
    </div>

    <!-- Month Quick Jump Navigation Strip -->
    <div class="px-3 px-md-4 py-2.5 border-bottom bg-light bg-opacity-50">
        <div class="month-pill-bar" id="monthPillContainer">
            <!-- Populated dynamically via JS -->
        </div>
    </div>

    <!-- VIEW 1: Interactive Monthly Calendar Grid -->
    <div id="calendarViewContainer">
        <!-- Weekday Headers -->
        <div class="calendar-grid-header">
            <div class="weekend">Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div class="weekend">Sat</div>
        </div>

        <!-- Calendar Days Grid Cells -->
        <div class="calendar-grid-days" id="calendarDaysGrid">
            <!-- Populated dynamically via JavaScript -->
        </div>

        <!-- Calendar Bottom Legend Strip -->
        <div class="p-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3 text-muted small bg-light bg-opacity-25">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex align-items-center gap-1.5">
                    <span class="d-inline-block rounded-circle bg-success" style="width: 10px; height: 10px;"></span>
                    <span>Company Holiday</span>
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <span class="d-inline-block rounded-circle bg-primary" style="width: 10px; height: 10px;"></span>
                    <span>Today</span>
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <span class="d-inline-block rounded-circle bg-secondary" style="width: 10px; height: 10px;"></span>
                    <span>Weekend</span>
                </div>
            </div>
            <span class="text-muted small">Tip: Click on any holiday chip to view, edit, or delete</span>
        </div>
    </div>

    <!-- VIEW 2: Full Annual List View Table -->
    <div id="listViewContainer" style="display: none;">
        <div class="p-3 p-md-4 border-bottom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
            <span class="text-muted small fw-semibold">Official company holiday calendar and festive observances</span>
            <div style="max-width: 280px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control border-start-0" id="holidaySearchInput" placeholder="Search holidays..." onkeyup="filterHolidaysList()">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="holidaysTable">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 60px;" class="text-center">S.No</th>
                        <th style="width: 260px;">Holiday Title</th>
                        <th>Description</th>
                        <th style="width: 150px;">Start Date</th>
                        <th style="width: 150px;">End Date</th>
                        <th style="width: 110px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holidayes as $index => $holiday)
                        @php
                            $sDate = Carbon\Carbon::parse($holiday->start_date);
                            $eDate = $holiday->end_date ? Carbon\Carbon::parse($holiday->end_date) : null;
                            $isUpcoming = $sDate->isFuture() || $sDate->isToday();
                            $daysDiff = $currentDate->copy()->startOfDay()->diffInDays($sDate, false);
                            $durationDays = $eDate ? $sDate->diffInDays($eDate) + 1 : 1;
                            $fest = $holiday->festival_data;
                        @endphp
                        <tr class="holiday-list-row {{ $isUpcoming ? 'table-success bg-opacity-10' : '' }}">
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm" 
                                         style="width: 38px; height: 38px; font-size: 0.95rem; flex-shrink: 0; background: {{ $fest['bg'] }};">
                                        <i class="{{ $fest['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block holiday-title-text">{{ $fest['emoji'] }} {{ $holiday->title }}</strong>
                                        <div class="d-flex align-items-center gap-1 mt-0.5">
                                            <span class="badge bg-light text-muted border py-0.5 px-1.5" style="font-size: 0.65rem;">{{ $fest['category'] }}</span>
                                            @if($durationDays > 1)
                                                <span class="badge bg-info text-info py-0.5 px-1.5" style="font-size: 0.65rem;">{{ $durationDays }} Days</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small" style="max-width: 250px; display: inline-block;">
                                    {{ $holiday->description ?? 'Official declared holiday' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ $sDate->format('d M, Y') }}
                                </span>
                                <small class="d-block text-muted">{{ $sDate->format('l') }}</small>
                            </td>
                            <td>
                                <span class="text-muted small fw-semibold">
                                    <i class="fa-regular fa-calendar-check me-1"></i>
                                    {{ $eDate ? $eDate->format('d M, Y') : $sDate->format('d M, Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('holiday.edit', $holiday->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Holiday" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Holiday"
                                        onclick="confirmDelete(event, {{ $holiday->id }})" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    <form id="delete-form-{{ $holiday->id }}"
                                        action="{{ route('holiday.destroy', $holiday->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block opacity-50"></i>
                                No holidays found. Click <strong>Add New Holiday</strong> to add an observance.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Interactive Company Holiday Details Modal (With Edit / Delete Actions) -->
<div class="modal fade" id="companyHolidayDetailModal" tabindex="-1" aria-labelledby="companyHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header py-3" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%); color: #ffffff;">
                <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="companyHolidayModalLabel">
                    <i class="fa-solid fa-champagne-glasses text-warning" id="modalHeaderIcon"></i>
                    <span id="modalHolidayTitle">Holiday Details</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3 bg-light border">
                    <div id="modalHolidayIconBox" class="rounded-circle d-flex align-items-center justify-content-center text-white shadow" 
                         style="width: 58px; height: 58px; flex-shrink: 0; background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 1.65rem;">
                        <i id="modalHolidayIcon" class="fa-solid fa-champagne-glasses"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span id="modalHolidayEmoji" style="font-size: 1.25rem;">🎉</span>
                            <span id="modalHolidayName">Holiday Name</span>
                        </h5>
                        <span class="badge mt-1 py-1 px-2 fw-semibold" id="modalHolidayBadge" style="background: rgba(16, 185, 129, 0.15); color: #065f46; font-size: 0.74rem;">Company Holiday</span>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">Start Date</small>
                            <span class="fw-bold text-dark" id="modalHolidayStartDate">--</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">End Date</small>
                            <span class="fw-bold text-primary" id="modalHolidayEndDate">--</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-semibold mb-1">Description / Notes:</label>
                    <div class="p-3 rounded-3 border bg-light small text-dark lh-sm" id="modalHolidayDesc">
                        No additional details provided.
                    </div>
                </div>

                <!-- Admin Action Buttons in Modal -->
                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <button type="button" class="btn btn-secondary rounded-3 px-3 py-2 fw-semibold" data-bs-dismiss="modal">
                        Close
                    </button>
                    <a href="#" id="modalEditBtn" class="btn btn-warning rounded-3 px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Holiday
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Parse Backend Holidays Data
    const holidaysData = @json($holidayes);

    // Map holidays by 'YYYY-MM-DD'
    const holidaysMap = {};
    holidaysData.forEach(h => {
        if (!h.start_date) return;
        const startDate = new Date(h.start_date + 'T00:00:00');
        const endDate = h.end_date ? new Date(h.end_date + 'T00:00:00') : startDate;

        let cur = new Date(startDate);
        while (cur <= endDate) {
            const y = cur.getFullYear();
            const m = String(cur.getMonth() + 1).padStart(2, '0');
            const d = String(cur.getDate()).padStart(2, '0');
            const key = `${y}-${m}-${d}`;

            if (!holidaysMap[key]) holidaysMap[key] = [];
            holidaysMap[key].push(h);

            cur.setDate(cur.getDate() + 1);
        }
    });

    // 2. Calendar View State
    let viewDate = new Date();
    const today = new Date();

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // 3. Quick Month Navigation Pills
    function renderMonthPills() {
        const container = document.getElementById('monthPillContainer');
        if (!container) return;

        container.innerHTML = '';
        monthNames.forEach((name, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'month-pill-btn' + (idx === viewDate.getMonth() ? ' active' : '');
            btn.textContent = name.substring(0, 3);
            btn.onclick = () => {
                viewDate.setMonth(idx);
                renderCalendar();
            };
            container.appendChild(btn);
        });
    }

    // 4. Render Calendar Grid
    function renderCalendar() {
        renderMonthPills();

        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();

        const heading = document.getElementById('currentMonthYearHeading');
        const summary = document.getElementById('currentMonthSummaryText');
        if (heading) {
            heading.innerHTML = `<i class="fa-solid fa-calendar-days text-primary"></i> <span>${monthNames[month]} ${year}</span>`;
        }

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        const startingDayOfWeek = firstDayOfMonth.getDay();
        const totalDaysInMonth = lastDayOfMonth.getDate();
        const prevMonthLastDay = new Date(year, month, 0).getDate();

        const grid = document.getElementById('calendarDaysGrid');
        if (!grid) return;
        grid.innerHTML = '';

        let monthHolidaysCount = 0;

        // Spillover from previous month
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
            const dayNum = prevMonthLastDay - i;
            const prevMonthDate = new Date(year, month - 1, dayNum);
            grid.appendChild(createDayCell(prevMonthDate, true));
        }

        // Current Month Days
        for (let d = 1; d <= totalDaysInMonth; d++) {
            const dateObj = new Date(year, month, d);
            grid.appendChild(createDayCell(dateObj, false));

            const key = formatDateKey(dateObj);
            if (holidaysMap[key]) {
                monthHolidaysCount += holidaysMap[key].length;
            }
        }

        // Spillover to next month
        const totalCells = startingDayOfWeek + totalDaysInMonth;
        const totalGridSlots = totalCells > 35 ? 42 : 35;
        const remainingSlots = totalGridSlots - totalCells;

        for (let nextD = 1; nextD <= remainingSlots; nextD++) {
            const nextMonthDate = new Date(year, month + 1, nextD);
            grid.appendChild(createDayCell(nextMonthDate, true));
        }

        if (summary) {
            summary.textContent = `${monthHolidaysCount} ${monthHolidaysCount === 1 ? 'holiday' : 'holidays'} scheduled in ${monthNames[month]} ${year}`;
        }
    }

    function createDayCell(dateObj, isOtherMonth) {
        const cell = document.createElement('div');
        cell.className = 'calendar-day-cell';
        if (isOtherMonth) cell.classList.add('other-month');

        const dayOfWeek = dateObj.getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            cell.classList.add('weekend-cell');
        }

        const isToday = isSameDay(dateObj, today);
        if (isToday && !isOtherMonth) {
            cell.classList.add('is-today');
        }

        const dateKey = formatDateKey(dateObj);
        const dayHolidays = holidaysMap[dateKey] || [];

        if (dayHolidays.length > 0 && !isOtherMonth) {
            cell.classList.add('has-holiday-date');
        }

        const header = document.createElement('div');
        header.className = 'day-number-header';

        const num = document.createElement('span');
        num.className = 'day-number';
        num.textContent = dateObj.getDate();
        header.appendChild(num);

        if (dayHolidays.length > 0 && !isOtherMonth) {
            const hBadge = document.createElement('span');
            hBadge.className = 'badge rounded-pill bg-success text-white px-2 py-0.5 d-inline-flex align-items-center gap-1 shadow-sm';
            hBadge.style.fontSize = '0.62rem';
            hBadge.innerHTML = '<i class="fa-solid fa-umbrella-beach"></i> Holiday';
            header.appendChild(hBadge);
        } else if (dayOfWeek === 0 && !isOtherMonth) {
            const wTag = document.createElement('span');
            wTag.className = 'small fw-bold text-danger';
            wTag.style.fontSize = '0.62rem';
            wTag.textContent = 'SUN';
            header.appendChild(wTag);
        } else if (dayOfWeek === 6 && !isOtherMonth) {
            const wTag = document.createElement('span');
            wTag.className = 'small fw-bold text-warning';
            wTag.style.fontSize = '0.62rem';
            wTag.textContent = 'SAT';
            header.appendChild(wTag);
        }

        cell.appendChild(header);

        // Render Holiday Event Chips
        dayHolidays.forEach(h => {
            const chip = document.createElement('div');
            chip.className = 'holiday-chip';
            if (h.end_date && h.end_date !== h.start_date) {
                chip.classList.add('multi-day');
            }

            const fest = h.festival_data || getFestivalIconData(h.title);

            chip.innerHTML = `<i class="${fest.icon}"></i> <span>${fest.emoji ? fest.emoji + ' ' : ''}${h.title}</span>`;
            chip.title = `${fest.emoji ? fest.emoji + ' ' : ''}${h.title} (${fest.category}) - Click to view/edit`;
            chip.onclick = (e) => {
                e.stopPropagation();
                openHolidayDetailModal(h);
            };

            cell.appendChild(chip);
        });

        if (dayHolidays.length > 0) {
            cell.onclick = () => {
                openHolidayDetailModal(dayHolidays[0]);
            };
        }

        return cell;
    }

    function formatDateKey(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function isSameDay(d1, d2) {
        return d1.getFullYear() === d2.getFullYear() &&
               d1.getMonth() === d2.getMonth() &&
               d1.getDate() === d2.getDate();
    }

    function changeMonth(delta) {
        viewDate.setMonth(viewDate.getMonth() + delta);
        renderCalendar();
    }

    function goToToday() {
        viewDate = new Date();
        renderCalendar();
    }

    function switchViewMode(mode) {
        const calContainer = document.getElementById('calendarViewContainer');
        const listContainer = document.getElementById('listViewContainer');
        const btnCal = document.getElementById('btnCalendarView');
        const btnList = document.getElementById('btnListView');

        if (mode === 'calendar') {
            if (calContainer) calContainer.style.display = 'block';
            if (listContainer) listContainer.style.display = 'none';
            if (btnCal) btnCal.className = 'btn active';
            if (btnList) btnList.className = 'btn text-muted';
        } else {
            if (calContainer) calContainer.style.display = 'none';
            if (listContainer) listContainer.style.display = 'block';
            if (btnCal) btnCal.className = 'btn text-muted';
            if (btnList) btnList.className = 'btn active';
        }
    }

    // Helper: Dynamic festival icon and theme resolver
    function getFestivalIconData(title) {
        const t = (title || '').toLowerCase();
        if (t.includes('new year')) {
            return { icon: 'fa-solid fa-champagne-glasses', emoji: '🎉', color: '#8b5cf6', bg: 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)', category: 'New Year Celebration' };
        }
        if (t.includes('republic') || t.includes('gantantra')) {
            return { icon: 'fa-solid fa-flag', emoji: '🇮🇳', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', category: 'National Holiday' };
        }
        if (t.includes('independence') || t.includes('swatantrata')) {
            return { icon: 'fa-solid fa-flag', emoji: '🇮🇳', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', category: 'National Holiday' };
        }
        if (t.includes('diwali') || t.includes('deepavali') || t.includes('deepawali')) {
            return { icon: 'fa-solid fa-fire-flame-curved', emoji: '🪔', color: '#d97706', bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', category: 'Festival of Lights' };
        }
        if (t.includes('holi') || t.includes('dhuleti') || t.includes('rang')) {
            return { icon: 'fa-solid fa-palette', emoji: '🎨', color: '#db2777', bg: 'linear-gradient(135deg, #f43f5e 0%, #db2777 100%)', category: 'Festival of Colors' };
        }
        if (t.includes('christmas') || t.includes('xmas') || t.includes('x-mas')) {
            return { icon: 'fa-solid fa-tree', emoji: '🎄', color: '#059669', bg: 'linear-gradient(135deg, #10b981 0%, #047857 100%)', category: 'Christmas Celebration' };
        }
        if (t.includes('eid') || t.includes('ramadan') || t.includes('ramzan') || t.includes('bakrid') || t.includes('muharram')) {
            return { icon: 'fa-solid fa-moon', emoji: '🌙', color: '#0d9488', bg: 'linear-gradient(135deg, #14b8a6 0%, #0f766e 100%)', category: 'Islamic Observance' };
        }
        if (t.includes('gandhi')) {
            return { icon: 'fa-solid fa-dove', emoji: '🕊️', color: '#475569', bg: 'linear-gradient(135deg, #64748b 0%, #475569 100%)', category: 'National Observance' };
        }
        if (t.includes('rakhi') || t.includes('raksha') || t.includes('bhai dooj') || t.includes('bhai tika')) {
            return { icon: 'fa-solid fa-gift', emoji: '🎁', color: '#e11d48', bg: 'linear-gradient(135deg, #f43f5e 0%, #be123c 100%)', category: 'Festive Celebration' };
        }
        if (t.includes('dussehra') || t.includes('vijayadashami') || t.includes('dashahara')) {
            return { icon: 'fa-solid fa-shield-halved', emoji: '🏹', color: '#b45309', bg: 'linear-gradient(135deg, #d97706 0%, #92400e 100%)', category: 'Festive Observance' };
        }
        if (t.includes('janmashtami') || t.includes('krishna')) {
            return { icon: 'fa-solid fa-om', emoji: '🦚', color: '#2563eb', bg: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)', category: 'Festive Observance' };
        }
        if (t.includes('shivratri') || t.includes('shiva')) {
            return { icon: 'fa-solid fa-om', emoji: '🔱', color: '#0284c7', bg: 'linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%)', category: 'Spiritual Observance' };
        }
        if (t.includes('ganesh') || t.includes('vinayaka')) {
            return { icon: 'fa-solid fa-om', emoji: '🐘', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #c2410c 100%)', category: 'Festive Observance' };
        }
        if (t.includes('durga') || t.includes('navratri') || t.includes('navaratri') || t.includes('puja') || t.includes('saptami') || t.includes('astami') || t.includes('ashtami') || t.includes('navami') || t.includes('dashami')) {
            return { icon: 'fa-solid fa-bell', emoji: '🌺', color: '#dc2626', bg: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)', category: 'Festive Observance' };
        }
        if (t.includes('good friday') || t.includes('easter')) {
            return { icon: 'fa-solid fa-cross', emoji: '✝️', color: '#6366f1', bg: 'linear-gradient(135deg, #818cf8 0%, #4f46e5 100%)', category: 'Christian Observance' };
        }
        if (t.includes('buddha') || t.includes('guru') || t.includes('gurpurab') || t.includes('baisakhi') || t.includes('vaisakhi') || t.includes('mahavir')) {
            return { icon: 'fa-solid fa-dharmachakra', emoji: '☸️', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', category: 'Spiritual Observance' };
        }
        if (t.includes('sankranti') || t.includes('pongal') || t.includes('lohri') || t.includes('bihu') || t.includes('chhath') || t.includes('chhat')) {
            return { icon: 'fa-solid fa-sun', emoji: '🪁', color: '#f59e0b', bg: 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)', category: 'Harvest Festival' };
        }
        if (t.includes('labour') || t.includes('may day') || t.includes('worker')) {
            return { icon: 'fa-solid fa-hammer', emoji: '⚒️', color: '#0284c7', bg: 'linear-gradient(135deg, #38bdf8 0%, #0284c7 100%)', category: 'International Observance' };
        }
        if (t.includes('women')) {
            return { icon: 'fa-solid fa-venus', emoji: '👩', color: '#db2777', bg: 'linear-gradient(135deg, #f472b6 0%, #db2777 100%)', category: 'Special Observance' };
        }
        if (t.includes('anniversary') || t.includes('birthday') || t.includes('foundation')) {
            return { icon: 'fa-solid fa-cake-candles', emoji: '🎂', color: '#ec4899', bg: 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)', category: 'Corporate Observance' };
        }
        return { icon: 'fa-solid fa-umbrella-beach', emoji: '🏖️', color: '#059669', bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', category: 'Company Holiday' };
    }

    function openHolidayDetailModal(holiday) {
        if (!holiday) return;

        const fest = holiday.festival_data || getFestivalIconData(holiday.title);

        document.getElementById('modalHolidayTitle').textContent = holiday.title;
        document.getElementById('modalHolidayName').textContent = holiday.title;
        const emojiEl = document.getElementById('modalHolidayEmoji');
        if (emojiEl) emojiEl.textContent = fest.emoji || '🎉';
        document.getElementById('modalHolidayDesc').textContent = holiday.description || 'Official company holiday announced by management.';

        // Update festival icon and styling in modal
        const iconBox = document.getElementById('modalHolidayIconBox');
        const iconEl = document.getElementById('modalHolidayIcon');
        const headerIcon = document.getElementById('modalHeaderIcon');
        const badgeEl = document.getElementById('modalHolidayBadge');

        if (iconBox && fest.bg) {
            iconBox.style.background = fest.bg;
        }
        if (iconEl && fest.icon) {
            iconEl.className = fest.icon;
        }
        if (headerIcon && fest.icon) {
            headerIcon.className = fest.icon + ' text-warning';
        }
        if (badgeEl) {
            badgeEl.textContent = fest.category || 'Company Holiday';
            badgeEl.style.background = fest.color ? `${fest.color}22` : 'rgba(16, 185, 129, 0.15)';
            badgeEl.style.color = fest.color || '#065f46';
        }

        const sDate = new Date(holiday.start_date + 'T00:00:00');
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('modalHolidayStartDate').textContent = sDate.toLocaleDateString('en-US', options);

        if (holiday.end_date) {
            const eDate = new Date(holiday.end_date + 'T00:00:00');
            document.getElementById('modalHolidayEndDate').textContent = eDate.toLocaleDateString('en-US', options);
        } else {
            document.getElementById('modalHolidayEndDate').textContent = sDate.toLocaleDateString('en-US', options);
        }

        // Set edit link
        const editBtn = document.getElementById('modalEditBtn');
        if (editBtn) {
            editBtn.href = `/company/holiday/${holiday.id}/edit`;
        }

        const modalEl = document.getElementById('companyHolidayDetailModal');
        if (typeof bootstrap !== 'undefined' && modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function filterHolidaysList() {
        const query = (document.getElementById('holidaySearchInput')?.value || '').toLowerCase();
        const rows = document.querySelectorAll('.holiday-list-row');

        rows.forEach(row => {
            const title = row.querySelector('.holiday-title-text')?.textContent.toLowerCase() || '';
            const allText = row.textContent.toLowerCase();
            if (title.includes(query) || allText.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function confirmDelete(event, holidayId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the company holiday record across all dashboards!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${holidayId}`).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderCalendar();
    });
</script>
@endsection