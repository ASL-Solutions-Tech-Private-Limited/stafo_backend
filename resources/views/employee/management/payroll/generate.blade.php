@extends('employee.layouts.app')

@section('title', 'Run Payroll & Generate Salary | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-calculator me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Payroll Studio</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Run & Generate Salary</h3>
            <p class="text-muted small mb-0">Compute accurate attendance-linked compensation, statutory deductions, and issue verified staff payslips.</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="{{ route('employee.management.payroll.records', ['month' => $currentMonth ?? date('m'), 'year' => $currentYear ?? date('Y')]) }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                <i class="fa-solid fa-table-list me-1"></i> Payroll Register
            </a>
            <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm" id="all_employee">
                <i class="fa-solid fa-bolt me-1"></i> Generate for All Staff
            </button>
        </div>
    </div>

    <!-- Sub-Module Pill Tabs -->
    <ul class="nav nav-pills gap-2 mb-4 bg-light p-2 rounded-4 border">
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-3 fw-semibold text-dark" href="{{ route('employee.management.payroll.records') }}">
                <i class="fa-solid fa-receipt me-2"></i> Monthly Salary Records
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active px-4 py-2 rounded-3 fw-semibold" href="{{ route('employee.management.payroll.generate') }}">
                <i class="fa-solid fa-calculator me-2"></i> Run / Generate Salary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-3 fw-semibold text-dark" href="{{ route('employee.management.payroll.components') }}">
                <i class="fa-solid fa-sliders me-2"></i> Salary Components
            </a>
        </li>
    </ul>

    <!-- Payroll Cycle Progress Summary Bar -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="p-3 rounded-4 bg-light border d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Active Workforce</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalEmployees ?? count($employees) }} <span class="fs-6 fw-normal text-muted">Employees</span></h4>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                    <i class="fa-solid fa-users fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-success small fw-semibold d-block">Processed This Month</span>
                    <h4 class="fw-bold text-success mb-0">{{ $processedCount ?? 0 }} <span class="fs-6 fw-normal text-success">Generated</span></h4>
                </div>
                <div class="rounded-circle bg-success text-white p-3">
                    <i class="fa-solid fa-circle-check fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-warning-emphasis small fw-semibold d-block">Pending Payrolls</span>
                    <h4 class="fw-bold text-warning-emphasis mb-0">{{ $pendingCount ?? 0 }} <span class="fs-6 fw-normal text-warning-emphasis">Awaiting</span></h4>
                </div>
                <div class="rounded-circle bg-warning text-dark p-3">
                    <i class="fa-solid fa-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Parameter Selection Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">
            <form action="{{ route('employee.management.payroll.saveEmployeeSalary') }}" method="POST" id="salaryForm">
                @csrf

                <div class="p-4 rounded-4 border mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label for="payroll_month" class="form-label fw-bold text-dark mb-1">
                                <i class="fa-regular fa-calendar-days text-primary me-1"></i> Payroll Month <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-primary-subtle py-2" name="month" id="payroll_month" required>
                                @php
                                    $selectedM = $currentMonth ?? (int)date('m');
                                @endphp
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $selectedM == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="payroll_year" class="form-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-calendar text-primary me-1"></i> Payroll Year <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-primary-subtle py-2" name="year" id="payroll_year" required>
                                @php
                                    $selectedY = $currentYear ?? (int)date('Y');
                                @endphp
                                @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $selectedY == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="employee" class="form-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-user-tie text-primary me-1"></i> Select Employee <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-primary-subtle py-2" name="employee" required id="employee">
                                <option value="">-- Choose Employee to Review & Calculate --</option>
                                @if (!$employees->isEmpty())
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}" data-department="{{ $emp->department->name ?? 'General' }}">
                                            {{ $emp->name }} @if(!empty($emp->emp_id)) (ID: {{ $emp->emp_id }}) @endif - {{ $emp->department->name ?? 'General' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Salary Calculation Section (AJAX Populated) -->
                <div class="row g-3" id="salary_section">
                    <div class="col-12 text-center py-5 bg-light rounded-4 border border-dashed text-muted">
                        <i class="fa-solid fa-calculator fs-1 mb-3 text-primary opacity-50"></i>
                        <h5 class="fw-semibold text-dark">Select an Employee to Load Payroll Studio</h5>
                        <p class="small text-muted mb-0">The system will pull active attendance records, calculate LOP deductions, statutory components, and preview the payslip.</p>
                    </div>
                </div>

                <!-- Submit Action Footer -->
                <div class="text-center mt-4 pt-3 border-top" id="save_button_container" style="display: none;">
                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                        <i class="fa-solid fa-check-double me-2"></i> Save & Confirm Payroll Disbursement
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    
    function loadSalaryDetails(isManualRecalc = false) {
        var employeeId = $('#employee').val();
        var month = $('#payroll_month').val();
        var year = $('#payroll_year').val();
        
        if (employeeId && month) {
            $('#salary_section').html(`
                <div class="col-12 text-center py-5 bg-light rounded-4 border">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <h6 class="fw-bold text-dark">Analyzing Attendance & Calculating Payroll...</h6>
                    <p class="text-muted small mb-0">Scanning attendance records, shift timing, and approved leaves.</p>
                </div>
            `);
            $('#save_button_container').hide();
            
            var requestData = { 
                month: month,
                year: year
            };
            if (isManualRecalc && $('#basic_salary').length && $('#basic_salary').val() !== '') {
                requestData.basic_salary = $('#basic_salary').val();
            }
            
            $.ajax({
                url: '{{ route('employee.management.payroll.getEmployeeSalary', '') }}/' + employeeId,
                type: 'GET',
                dataType: 'json',
                data: requestData,
                success: function(response) {
                    if (response && response.section) {
                        $('#salary_section').html(response.section);
                        $('#save_button_container').show();
                        bindLiveCalculations();
                    } else {
                        $('#salary_section').html(`
                            <div class="col-12">
                                <div class="alert alert-warning py-3 rounded-4">
                                    <i class="fa-solid fa-circle-exclamation me-2"></i> No payroll data could be constructed for this employee.
                                </div>
                            </div>
                        `);
                        $('#save_button_container').hide();
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    $('#salary_section').html(`
                        <div class="col-12">
                            <div class="alert alert-danger py-3 rounded-4">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> Error loading salary breakdown. Please try again or refresh.
                            </div>
                        </div>
                    `);
                    $('#save_button_container').hide();
                }
            });
        } else {
            resetPayrollSection();
        }
    }

    function resetPayrollSection() {
        $('#salary_section').html(`
            <div class="col-12 text-center py-5 bg-light rounded-4 border border-dashed text-muted">
                <i class="fa-solid fa-calculator fs-1 mb-3 text-primary opacity-50"></i>
                <h5 class="fw-semibold text-dark">Select an Employee to Load Payroll Studio</h5>
                <p class="small text-muted mb-0">The system will pull active attendance records, calculate LOP deductions, statutory components, and preview the payslip.</p>
            </div>
        `);
        $('#save_button_container').hide();
    }

    function bindLiveCalculations() {
        $(document).off('input keyup change', '#basic_salary, .salary_type_amount');
        
        $(document).on('input keyup change', '#basic_salary, .salary_type_amount', function() {
            recalculateLivePayroll();
        });
    }

    function recalculateLivePayroll() {
        var basic = parseFloat($('#basic_salary').val()) || 0;
        $('.basic-salary-display').text('₹' + basic.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        
        var workingDays = parseFloat($('input[name="working_days"]').val()) || 26;
        var dailySalary = basic > 0 ? (basic / 30) : 0;
        
        var absentDays = parseFloat($('input[name="absent_days"]').val()) || 0;
        var lateDeduction = parseFloat($('input[name="late_deduction"]').val()) || 0;
        var absentDeduction = parseFloat((absentDays * dailySalary).toFixed(2));
        var lopDeduction = parseFloat((absentDeduction + lateDeduction).toFixed(2));
        
        $('#other_deduction_input').val(lopDeduction);
        $('input[name="absent_deduction"]').val(absentDeduction);
        $('#lop_display').text('₹' + lopDeduction.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        
        var earnings = basic;
        var deductions = lopDeduction;
        
        $('.salary_type_amount').each(function() {
            var val = parseFloat($(this).val()) || 0;
            var type = $(this).data('paymenttype');
            if (type === 'Earning') {
                earnings += val;
            } else if (type === 'Deduction') {
                deductions += val;
            }
        });

        var reimbursement = parseFloat($('#reimbursement_input').val()) || 0;
        earnings += reimbursement;

        var netSalary = Math.max(0, parseFloat((earnings - deductions).toFixed(2)));

        $('#gross_amount').val(netSalary);
        $('#total_earnings_display').text('₹' + earnings.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#total_deductions_display').text('₹' + deductions.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#net_salary_display').text('₹' + netSalary.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    $('#payroll_month, #payroll_year').change(function() {
        if ($('#employee').val()) {
            loadSalaryDetails(false);
        }
    });

    $('#employee').change(function() {
        loadSalaryDetails(false);
    });

    // 1-Click Batch Payroll Generation for All Staff
    $('#all_employee').click(function() {
        var month = $('#payroll_month').val();
        var year = $('#payroll_year').val();
        var monthName = $('#payroll_month option:selected').text();
        
        if (!month) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Payroll Month',
                text: 'Please select a payroll month before running batch generation!'
            });
            return;
        }

        Swal.fire({
            title: 'Generate Full Workforce Payroll?',
            html: `You are about to run batch payroll calculation for <strong>ALL active employees</strong> for <strong>${monthName} ${year}</strong>.<br><br><small class="text-muted">Attendance, shifts, grace time, and statutory components will be computed automatically.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-bolt me-1"></i> Yes, Run Batch Payroll',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing Payroll...',
                    html: `Generating payroll and verified salary summaries for all staff in <strong>${monthName} ${year}</strong>.<br>Please wait a moment.`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('employee.management.payroll.generateAllSalary') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: { 
                        month: month,
                        year: year 
                    },
                    success: function(res) {
                        Swal.close();
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payroll Generated!',
                                text: res.message,
                                timer: 2500,
                                showConfirmButton: true,
                                confirmButtonText: 'View Payroll Register'
                            }).then(() => {
                                if (res.redirect) {
                                    window.location.href = res.redirect;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Notice',
                                text: res.message || 'Unable to complete batch processing.'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        var errMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred during payroll calculation.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Batch Error',
                            text: errMessage
                        });
                    }
                });
            }
        });
    });

    if ($('#employee').val() && $('#payroll_month').val()) {
        loadSalaryDetails();
    }
});
</script>
@endsection
