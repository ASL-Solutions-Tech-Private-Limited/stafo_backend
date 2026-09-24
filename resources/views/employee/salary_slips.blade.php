@extends('employee.layouts.app')

@section('title', 'My Salary Slips | STAFO HRMS')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Salary & Payslips</h4>
            <span class="text-muted small">View payroll breakdown and download monthly salary slips</span>
        </div>
        <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- Payroll Bulletin Marquee Ticker -->
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Payroll Desk</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                <span class="marquee-chip chip-attendance">
                    <i class="fa-solid fa-calendar-check text-success"></i>
                    <strong>Salary Cycle:</strong> Monthly salaries and electronic payslips are published by the designated company cycle date.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-payroll">
                    <i class="fa-solid fa-file-pdf text-info"></i>
                    <strong>Tax & Verification:</strong> All downloaded PDF payslips are digitally verifiable and compliant for banking / visa documentation.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-holiday">
                    <i class="fa-solid fa-shield-halved text-warning"></i>
                    <strong>Statutory Deductions:</strong> EPF, ESIC, and PT contributions are automatically accounted for per regional Indian labor standards.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-policy">
                    <i class="fa-solid fa-receipt text-primary"></i>
                    <strong>Reimbursements:</strong> Approved travel and expense claims are merged into the respective payroll disbursement.
                </span>
            </marquee>
        </div>
        <div class="d-none d-md-flex align-items-center text-muted small ps-2 border-start" style="font-size: 0.72rem; white-space: nowrap;">
            <i class="fa-solid fa-hand-pointer text-warning me-1"></i> Hover to pause
        </div>
    </div>

    <!-- Salary Overview Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Official Designation & Base</small>
                <h5 class="fw-bold text-dark mb-1">{{ $employee_info->position ?: 'Staff Member' }}</h5>
                <span class="text-muted small">Company: {{ $employee_info->company->company_name ?? 'STAFO Partner' }}</span>
            </div>
            <div class="col-md-6 text-md-end">
                @if($employee_info->salary)
                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Monthly CTC / Gross</small>
                    <h3 class="fw-bold text-primary mb-0">₹ {{ number_format($employee_info->salary, 2) }}</h3>
                @endif
            </div>
        </div>
    </div>

    <!-- Payslips List Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i> Generated Payslips
            </h6>
            <span class="badge bg-light text-muted border small">{{ $salarySummaries->total() }} Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Month / Period</th>
                            <th>Working Days</th>
                            <th>Gross Earnings</th>
                            <th>Total Deductions</th>
                            <th>Net Payable</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salarySummaries as $slip)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ date('F', mktime(0, 0, 0, $slip->month, 1)) }} {{ $slip->year }}
                                    </div>
                                    <small class="text-muted">Generated on {{ Carbon\Carbon::parse($slip->created_at)->format('d M, Y') }}</small>
                                </td>
                                <td class="small">
                                    {{ $slip->working_days ?? $slip->total_working_days ?? 26 }} days ({{ $slip->present_days ?? '--' }} Present)
                                </td>
                                <td class="fw-semibold text-dark small">
                                    ₹ {{ number_format(($slip->basic_salary ?? 0) + ($slip->total_earning ?? 0) + ($slip->reimbursement ?? 0), 2) }}
                                </td>
                                <td class="text-danger small">
                                    ₹ {{ number_format(($slip->total_deduction ?? 0) + ($slip->other_deduction ?? 0), 2) }}
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        ₹ {{ number_format($slip->net_salary ?? 0, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('employee.salarySlip.download', $slip->id) }}" class="btn btn-outline-primary btn-sm px-3">
                                        <i class="fa-solid fa-download me-1"></i> Download Slip
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">No payroll summaries generated for your account yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($salarySummaries->hasPages())
                <div class="p-3 border-top">
                    {{ $salarySummaries->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
