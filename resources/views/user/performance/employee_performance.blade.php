@extends('user.layouts.app')

@section('title', $employee->name . ' - Performance Track | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">{{ $employee->name }}'s Performance History</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-user me-1"></i> Employee #{{ $employee->id }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Track monthly KPI evaluations, ratings breakdown, and appraisal trends</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('employeeRankList') }}" class="btn btn-outline-secondary px-3 py-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Rankings
                </a>
                <a href="{{ route('employeePerformanceAdd', $employee->id) }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add Evaluation
                </a>
            </div>
        </div>

        @php
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];

            // Group by year and month
            $groupedPerformances = $employeePerformances->groupBy(function($item) {
                $y = $item->year ?? date('Y');
                return $y . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

            $totalReviews = $groupedPerformances->count();
            $allMarks = $employeePerformances->pluck('marks')->filter();
            $avgMarks = $allMarks->count() > 0 ? round($allMarks->avg(), 1) : 0;
            $maxMarks = $allMarks->count() > 0 ? round($allMarks->max(), 1) : 0;
        @endphp

        <!-- Quick KPI Summary Strip -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="p-3 bg-light rounded-4 border d-flex align-items-center gap-3">
                    <div class="bg-white rounded-3 p-3 border shadow-xs text-primary">
                        <i class="fa-solid fa-clipboard-check fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Monthly Reviews</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $totalReviews }} {{ Str::plural('Month', $totalReviews) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="p-3 bg-light rounded-4 border d-flex align-items-center gap-3">
                    <div class="bg-white rounded-3 p-3 border shadow-xs text-warning">
                        <i class="fa-solid fa-star fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Overall Average Score</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $avgMarks }} <small class="fs-6 text-muted">/ 100</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="p-3 bg-light rounded-4 border d-flex align-items-center gap-3">
                    <div class="bg-white rounded-3 p-3 border shadow-xs text-success">
                        <i class="fa-solid fa-trophy fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Peak KPI Score</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $maxMarks }} <small class="fs-6 text-muted">pts</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Evaluations Grid -->
        @if($groupedPerformances->count() > 0)
            <div class="row g-4">
                @foreach($groupedPerformances as $key => $items)
                    @php
                        $firstItem = $items->first();
                        $monthNum = (int)$firstItem->month;
                        $monthName = $monthNames[$monthNum] ?? ('Month ' . $monthNum);
                        $yearVal = $firstItem->year ?? date('Y');
                        $monthTotal = $items->sum('marks');
                        $monthAvg = round($items->avg('marks'), 1);
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Header of Month Card -->
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-white rounded-3 p-2 border shadow-xs text-primary">
                                            <i class="fa-solid fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">{{ $monthName }} {{ $yearVal }}</h5>
                                            <small class="text-muted">{{ $items->count() }} KPI {{ Str::plural('Metric', $items->count()) }}</small>
                                        </div>
                                    </div>
                                    <span class="badge-stafo badge-stafo-success">
                                        Total: {{ $monthTotal }} pts
                                    </span>
                                </div>

                                <!-- KPI Items Breakdown -->
                                <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach($items as $performance)
                                        <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-circle-dot text-primary small"></i>
                                                <span class="fw-semibold text-dark small">
                                                    {{ $performance->performancetype->name ?? 'KPI Evaluation' }}
                                                </span>
                                            </div>
                                            <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                                <i class="fa-solid fa-star text-warning me-1"></i> {{ $performance->marks }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="pt-2 border-top d-flex align-items-center justify-content-between text-muted small">
                                <span>Monthly Average:</span>
                                <span class="fw-bold text-dark">{{ $monthAvg }} pts</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5 bg-light rounded-4 border">
                <div class="bg-white rounded-circle p-3 d-inline-flex border mb-3 text-muted shadow-xs">
                    <i class="fa-solid fa-chart-line fs-1 text-primary"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Performance Records Yet</h5>
                <p class="text-muted small mb-3">There are no monthly KPI scores recorded for {{ $employee->name }}.</p>
                <a href="{{ route('employeePerformanceAdd', $employee->id) }}" class="btn btn-primary px-4 fw-bold">
                    <i class="fa-solid fa-plus me-1"></i> Record First Evaluation
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
