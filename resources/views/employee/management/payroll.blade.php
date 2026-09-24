@extends('employee.layouts.app')

@section('title', 'Company Payroll | Management Portal')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1">
                    <i class="fa-solid fa-file-invoice-dollar me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Staff Payroll</span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">Staff Payroll Summaries & Slips</h3>
            <p class="text-muted small mb-0">View monthly company staff salary calculations and payslips.</p>
        </div>

        <!-- Month & Year Filter Form -->
        <form action="{{ route('employee.management.payroll') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="month" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mVal }}" {{ $month == $mVal ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>

            <select name="year" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Payroll Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--bs-card-bg, #ffffff);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Processed Payslips</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ $processedCount }}</h3>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                        <i class="fa-solid fa-receipt fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--bs-card-bg, #ffffff);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Net Payout</small>
                        <h3 class="fw-bold mb-0 text-success">₹{{ number_format($totalPayout, 2) }}</h3>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success">
                        <i class="fa-solid fa-indian-rupee-sign fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bs-card-bg, #ffffff);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-uppercase text-muted small" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th class="ps-4">Employee</th>
                        <th>Department</th>
                        <th>Working Days</th>
                        <th>Present / Absent</th>
                        <th>Gross Earning</th>
                        <th>Deductions</th>
                        <th class="text-end pe-4">Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $sal)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $sal->employee_name }}</div>
                                <small class="text-muted" style="font-size: 0.74rem;">Emp ID: {{ $sal->employee_id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $sal->department_name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $sal->total_working_days ?? $sal->working_days }}</span>
                            </td>
                            <td>
                                <span class="text-success fw-bold">{{ $sal->present_days }}</span> / <span class="text-danger fw-bold">{{ $sal->absent_days }}</span>
                            </td>
                            <td>
                                <span class="text-dark">₹{{ number_format($sal->total_earning, 2) }}</span>
                            </td>
                            <td>
                                <span class="text-danger">₹{{ number_format($sal->total_deduction, 2) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 px-2.5 py-1">
                                    ₹{{ number_format($sal->net_salary, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-invoice-dollar mb-2" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                <h6 class="fw-bold text-dark mb-1">No Salary Records Found</h6>
                                <p class="text-muted small mb-0">No payroll summaries generated for this month and year.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salaries->hasPages())
            <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-center">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
