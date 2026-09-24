@extends('employee.layouts.app')

@section('title', 'Payroll & Salary Records | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-file-invoice-dollar me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Payroll & Compensation</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Payroll & Salary Records</h3>
            <p class="text-muted small mb-0">Manage monthly salary registers, disbursement summaries, and employee payslips.</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <form id="salary_export" action="{{ route('employee.management.payroll.export') }}" method="GET" class="d-inline">
                <input type="hidden" name="selected_month" value="{{ $month }}">
                <input type="hidden" name="selected_year" value="{{ $year }}">
                <button type="submit" class="btn btn-outline-success px-3 py-2 rounded-3 fw-semibold shadow-2xs">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Payroll
                </button>
            </form>
            @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
            <a href="{{ route('employee.management.payroll.generate') }}" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-calculator me-1"></i> Run / Generate Salary
            </a>
            @endif
        </div>
    </div>

    <!-- Sub-Module Pill Tabs -->
    <ul class="nav nav-pills gap-2 mb-4 bg-light p-2 rounded-4 border">
        <li class="nav-item">
            <a class="nav-link active px-4 py-2 rounded-3 fw-semibold" href="{{ route('employee.management.payroll.records') }}">
                <i class="fa-solid fa-receipt me-2"></i> Monthly Salary Records
            </a>
        </li>
        @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-3 fw-semibold text-dark" href="{{ route('employee.management.payroll.generate') }}">
                <i class="fa-solid fa-calculator me-2"></i> Run / Generate Salary
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-3 fw-semibold text-dark" href="{{ route('employee.management.payroll.components') }}">
                <i class="fa-solid fa-sliders me-2"></i> Salary Components
            </a>
        </li>
    </ul>

    <!-- 4 KPI Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-bold text-uppercase letter-spacing">Net Disbursed</span>
                        <h3 class="fw-bold text-white mb-0 mt-1">₹{{ number_format($totalDisbursed ?? 0, 2) }}</h3>
                        <small class="text-white-50">Total Payable for Cycle</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-20 p-3">
                        <i class="fa-solid fa-money-bill-transfer fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); color: white;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-bold text-uppercase letter-spacing">Staff Processed</span>
                        <h3 class="fw-bold text-white mb-0 mt-1">{{ $employeesPaid ?? 0 }} <span class="fs-6 fw-normal text-white-50">Employees</span></h3>
                        <small class="text-white-50">Generated Payslips</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-20 p-3">
                        <i class="fa-solid fa-users-gear fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #475569 0%, #64748b 100%); color: white;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-bold text-uppercase letter-spacing">Average Payout</span>
                        <h3 class="fw-bold text-white mb-0 mt-1">₹{{ number_format($averageSalary ?? 0, 2) }}</h3>
                        <small class="text-white-50">Per Employee Average</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-20 p-3">
                        <i class="fa-solid fa-arrow-trend-up fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small fw-bold text-uppercase letter-spacing">Total Deductions</span>
                        <h3 class="fw-bold text-white mb-0 mt-1">₹{{ number_format($totalDeductions ?? 0, 2) }}</h3>
                        <small class="text-white-50">Statutory & Attendance LOP</small>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-20 p-3">
                        <i class="fa-solid fa-arrow-trend-down fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-3">
            <form action="{{ route('employee.management.payroll.records') }}" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-3">
                        <label class="small text-muted fw-semibold mb-1">Payroll Month</label>
                        <select name="month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @if ($month == $m) selected @endif>
                                    {{ Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="small text-muted fw-semibold mb-1">Payroll Year</label>
                        <select name="year" class="form-select">
                            @for ($yr = date('Y') - 1; $yr <= date('Y') + 1; $yr++)
                                <option value="{{ $yr }}" @if ($year == $yr) selected @endif>{{ $yr }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="small text-muted fw-semibold mb-1">Search Employee / Dept</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name or department..." value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="small text-muted fw-semibold mb-1 d-none d-md-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                                <i class="fa-solid fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('employee.management.payroll.records') }}" class="btn btn-outline-secondary py-2 rounded-3" title="Reset Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payroll Register Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th style="width: 50px;" class="text-center py-3">#</th>
                        <th style="min-width: 220px;" class="py-3">Employee Details</th>
                        <th style="min-width: 140px;" class="py-3">Salary Period</th>
                        <th style="min-width: 130px;" class="py-3">Attendance</th>
                        <th style="min-width: 130px;" class="py-3 text-end">Basic Salary</th>
                        <th style="min-width: 140px;" class="py-3 text-end">Net Take-Home</th>
                        <th style="min-width: 110px;" class="py-3 text-center">Status</th>
                        <th style="min-width: 140px;" class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($salarySummaries as $index => $summary)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ method_exists($salarySummaries, 'firstItem') ? ($salarySummaries->firstItem() + $index) : ($index + 1) }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                        {{ strtoupper(substr($summary->employee_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $summary->employee_name }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">
                                            {{ $summary->department_name ?? 'General' }} &bull; Emp ID: {{ $summary->employee->emp_id ?? $summary->employee_id }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-3 fw-semibold font-sans">
                                    <i class="fa-regular fa-calendar-check text-primary me-1"></i>
                                    {{ Carbon\Carbon::create($summary->salary_year, $summary->salary_month, 1)->format('F Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-success fw-bold">{{ $summary->present_days ?? 0 }} Present</span>
                                    @if(($summary->absent_days ?? 0) > 0)
                                        <span class="text-danger fw-bold ms-1">/ {{ $summary->absent_days }} LOP</span>
                                    @endif
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $summary->working_days ?? 26 }} Working Days</small>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="fw-semibold text-muted">₹{{ number_format($summary->basic_salary ?? 0, 2) }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-6">₹{{ number_format($summary->net_salary ?? 0, 2) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-1" style="font-size: 0.68rem;"></i> Generated
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('employee.management.payroll.details', ['employeeId' => $summary->employee_id, 'month' => $summary->salary_month, 'year' => $summary->salary_year]) }}" 
                                       class="btn btn-sm btn-outline-primary px-2.5 py-1 rounded-2" 
                                       title="View Detailed Payslip">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('employee.management.payroll.downloadSlip', ['emp_id' => $summary->employee_id, 'month' => $summary->salary_month, 'year' => $summary->salary_year]) }}" 
                                       class="btn btn-sm btn-outline-secondary px-2.5 py-1 rounded-2" 
                                       title="Download Payslip PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('employee.management.payroll.deleteSalary', $summary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this salary record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1 rounded-2" title="Delete Salary Record">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="fa-solid fa-receipt fs-1 text-muted opacity-50 mb-3"></i>
                                    <h5 class="fw-bold text-dark mb-1">No Payroll Records Found</h5>
                                    <p class="small text-muted mb-3">No salary calculations generated for {{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}.</p>
                                    @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
                                    <a href="{{ route('employee.management.payroll.generate') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                                        <i class="fa-solid fa-calculator me-1"></i> Generate Payroll for {{ Carbon\Carbon::create($year, $month, 1)->format('F') }}
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salarySummaries->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $salarySummaries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
