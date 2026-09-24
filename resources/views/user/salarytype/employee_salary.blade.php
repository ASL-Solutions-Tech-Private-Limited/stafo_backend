@extends('user.layouts.app')
@section('title', 'Payroll Register & Disbursement Logs | STAFO HRMS')

@section('content')
@include('user.layouts.alert')

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-primary bg-opacity-10 text-primary fw-bold small mb-2">
                    <i class="fa-solid fa-receipt"></i> Monthly Payroll Register
                </div>
                <h3 class="fw-bold text-dark mb-1">Payroll & Salary Disbursement Register</h3>
                <p class="text-muted small mb-0">Executive summary of monthly payroll commitments, compensation breakdown, and employee payslips.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form id="salary_export" action="{{ route('salary-export') }}" method="GET" class="d-inline">
                    <input type="hidden" name="selected_month" id="selected_month" value="{{ $month }}">
                    <input type="hidden" name="selected_year" id="selected_year" value="{{ $year }}">
                    <button type="button" id="export" class="btn btn-outline-success px-3 py-2 rounded-3 fw-semibold">
                        <i class="fa-solid fa-file-excel me-1"></i> Export Payroll
                    </button>
                </form>
                <a href="{{ route('generateSalary') }}" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm">
                    <i class="fa-solid fa-calculator me-1"></i> Generate Payroll
                </a>
            </div>
        </div>

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
                            <span class="text-white-50 small fw-bold text-uppercase letter-spacing">Allowances Added</span>
                            <h3 class="fw-bold text-white mb-0 mt-1">₹{{ number_format($totalEarnings ?? 0, 2) }}</h3>
                            <small class="text-white-50">HRA, DA, Reimbursements</small>
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

        <!-- Filter Bar -->
        <div class="p-3 bg-light rounded-4 border mb-4">
            <form action="{{ route('employeeSalaryList') }}" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-2">
                        <label class="small text-muted fw-semibold mb-1">Month</label>
                        <select name="month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @if ($month == $m) selected @endif>{{ $monthArray[$m - 1] }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="small text-muted fw-semibold mb-1">Year</label>
                        <select name="year" class="form-select">
                            @for ($yr = date('Y') - 1; $yr <= date('Y') + 1; $yr++)
                                <option value="{{ $yr }}" @if ($year == $yr) selected @endif>{{ $yr }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="small text-muted fw-semibold mb-1">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @if(isset($departments) && !$departments->isEmpty())
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" @if(($department_id ?? '') == $dept->id) selected @endif>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="small text-muted fw-semibold mb-1">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All Employees</option>
                            @if (!$employees->isEmpty())
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @if ($employee_id == $employee->id) selected @endif>
                                        {{ $employee->name }} @if(!empty($employee->emp_id)) ({{ $employee->emp_id }}) @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="small text-muted fw-semibold mb-1 d-none d-md-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3">
                                <i class="fa-solid fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('employeeSalaryList') }}" class="btn btn-outline-secondary py-2 rounded-3" title="Reset Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Payroll Register Table -->
        <div class="table-responsive rounded-4 border overflow-hidden">
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
                        <th style="min-width: 120px;" class="py-3 text-center">Actions</th>
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($summary->employee_name ?? ($summary->employee->name ?? 'E'), 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $summary->employee_name ?? ($summary->employee->name ?? 'N/A') }}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!empty($summary->employee->emp_id))
                                                <small class="text-muted fw-semibold">{{ $summary->employee->emp_id }}</small>
                                                <small class="text-muted">&bull;</small>
                                            @endif
                                            <small class="badge bg-light text-secondary border">{{ $summary->department_name ?? ($summary->employee->department->name ?? 'General') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ $monthArray[($summary->salary_month ?? $month) - 1] ?? $month }}, {{ $summary->salary_year ?? $year }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-success fw-bold">{{ $summary->present_days ?? 0 }}d</span> Present
                                    @if(($summary->absent_days ?? 0) > 0)
                                        <br><span class="text-danger fw-semibold">{{ $summary->absent_days }}d</span> Absent / LOP
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="fw-semibold text-muted">₹{{ number_format((float)($summary->basic_salary ?? 0), 2) }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-6">₹{{ number_format((float)($summary->net_salary ?? $summary->gross_salary ?? 0), 2) }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $status = $summary->status ?? 'Generated';
                                @endphp
                                <span class="badge {{ $status == 'Paid' ? 'bg-success text-white' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} px-3 py-1 rounded-pill">
                                    <i class="fa-solid {{ $status == 'Paid' ? 'fa-circle-check' : 'fa-clock' }} me-1"></i> {{ $status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('employeeSalaryDetails', $summary->employee_id) }}?month={{ $summary->salary_month ?? $month }}&year={{ $summary->salary_year ?? $year }}"
                                       class="btn btn-sm btn-outline-primary px-2 py-1 rounded-3" 
                                       title="View Official Payslip">
                                        <i class="fa-solid fa-eye me-1"></i> View
                                    </a>
                                    <a href="{{ route('salaryPDF') }}?emp_id={{ $summary->employee_id }}&month={{ $summary->salary_month ?? $month }}&year={{ $summary->salary_year ?? $year }}"
                                       class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-3" 
                                       title="Download PDF Salary Slip">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('deleteSalary', $summary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this salary record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-3" title="Delete Salary Record">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-receipt fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-semibold text-dark">No Payroll Records Found</h6>
                                <p class="small text-muted mb-3">No salary disbursement records have been generated for {{ $monthArray[$month - 1] }} {{ $year }}.</p>
                                <a href="{{ route('generateSalary') }}" class="btn btn-sm btn-primary px-3 py-2 rounded-3">
                                    <i class="fa-solid fa-calculator me-1"></i> Generate Payroll for this Cycle
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($salarySummaries, 'hasPages') && $salarySummaries->hasPages())
            <div class="mt-4 d-flex justify-content-end">
                {{ $salarySummaries->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>
@endsection

@section('js')
<script>
    $('#export').click(function() {
        var month = $('select[name="month"]').val();
        var year = $('select[name="year"]').val();
        $('#selected_month').val(month);
        $('#selected_year').val(year);
        $('#salary_export').submit();
    });
</script>
@endsection
