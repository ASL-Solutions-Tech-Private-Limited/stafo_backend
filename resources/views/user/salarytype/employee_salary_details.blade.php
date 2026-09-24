@extends('user.layouts.app')

@section('title', 'Official Payslip | STAFO HRMS')

@section('content')

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">

        <!-- Top Actions Bar -->
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom d-print-none">
            <a href="{{ route('employeeSalaryList', ['month' => $salarySummary->salary_month, 'year' => $salarySummary->salary_year]) }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Payroll Register
            </a>
            <div class="d-flex align-items-center gap-2">
                <button onclick="window.print()" class="btn btn-outline-primary px-3 py-2 rounded-3">
                    <i class="fa-solid fa-print me-1"></i> Print Payslip
                </button>
                <a href="{{ route('salaryPDF') }}?emp_id={{ $salarySummary->employee_id }}&month={{ $salarySummary->salary_month }}&year={{ $salarySummary->salary_year }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm">
                    <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Official Payslip Container -->
        <div class="payslip-container p-4 p-md-5 border rounded-4 bg-white shadow-xs mx-auto" style="max-width: 900px;">
            
            <!-- Company Letterhead & Payslip Header -->
            <div class="text-center mb-4 pb-4 border-bottom">
                <div class="d-inline-block px-3 py-1 rounded-pill bg-primary bg-opacity-10 text-primary fw-bold small mb-2 text-uppercase letter-spacing">
                    Confidential Salary Slip &bull; {{ $monthArray[$salarySummary->salary_month - 1] ?? '' }} {{ $salarySummary->salary_year }}
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ $company->company_name ?? 'STAFO Partner Organization' }}</h2>
                <p class="text-muted small mb-0">
                    {{ $company->address ?? '' }}
                    @if(!empty($company->pin)), PIN: {{ $company->pin }}@endif
                    @if(!empty($company->mobile_number)) &bull; Tel: {{ $company->mobile_number }}@endif
                </p>
            </div>

            <!-- Employee & Banking Information Grid -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="p-3 bg-light rounded-4 h-100 border">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-id-badge text-primary"></i> Employee Profile
                        </h6>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0" style="width: 130px;">Full Name:</td>
                                <td class="fw-bold text-dark">{{ $salarySummary->employee_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Employee Code:</td>
                                <td class="fw-semibold text-dark">{{ $salarySummary->employee->emp_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Department:</td>
                                <td class="text-dark">{{ $salarySummary->department_name ?? ($salarySummary->employee->department->name ?? 'General') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Designation:</td>
                                <td class="text-dark">{{ $salarySummary->employee->designation->name ?? ($salarySummary->employee->designation ?? 'Staff Member') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Date of Joining:</td>
                                <td class="text-dark">
                                    {{ !empty($salarySummary->employee->date_of_joining) ? date('d M, Y', strtotime($salarySummary->employee->date_of_joining)) : 'N/A' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="p-3 bg-light rounded-4 h-100 border">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-building-columns text-success"></i> Banking & Statutory
                        </h6>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0" style="width: 130px;">Bank Name:</td>
                                <td class="fw-semibold text-dark">{{ $salarySummary->employee->bankAccount->bank_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Account Number:</td>
                                <td class="fw-bold text-dark">{{ $salarySummary->employee->bankAccount->account_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">IFSC Code:</td>
                                <td class="text-dark">{{ $salarySummary->employee->bankAccount->ifsc_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">PF Number:</td>
                                <td class="text-dark">{{ $salarySummary->employee->pf_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">ESI / UAN:</td>
                                <td class="text-dark">{{ $salarySummary->employee->esi_number ?? ($salarySummary->employee->uan_number ?? 'N/A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attendance Metrics Bar -->
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="row g-2 text-center text-md-start align-items-center">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Working Days</small>
                        <span class="fw-bold text-dark fs-6">{{ $salarySummary->working_days ?? $salarySummary->total_working_days ?? 26 }} Days</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Holidays</small>
                        <span class="fw-bold text-primary fs-6">{{ $salarySummary->holiday_count ?? 0 }} Days</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Days Present</small>
                        <span class="fw-bold text-success fs-6">{{ $salarySummary->present_days ?? 0 }} Days</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Loss of Pay (Absent/Late)</small>
                        <span class="fw-bold text-danger fs-6">{{ $salarySummary->absent_days ?? 0 }} Days</span>
                    </div>
                </div>
            </div>

            @php
                $total_earning = (float)($salarySummary->basic_salary ?? 0);
                $total_deduction = 0;
                $other_deduction = (float)($salarySummary->other_deduction ?? 0);
                
                $earningsList = [];
                $deductionsList = [];

                // Basic Salary entry
                $earningsList[] = [
                    'title' => 'Basic Salary',
                    'amount' => (float)$salarySummary->basic_salary
                ];

                // Loop through salary components safely
                if(isset($employeeSalaries)) {
                    foreach($employeeSalaries as $salary) {
                        if ($salary->salary_type_id == 0 || !$salary->salarytype) {
                            continue;
                        }
                        $ptype = $salary->salarytype->payment_type ?? 'Earning';
                        $amt = (float)$salary->amount;
                        if($ptype == 'Earning' && $amt > 0) {
                            $earningsList[] = [
                                'title' => $salary->label ?? $salary->salarytype->salary_type,
                                'amount' => $amt
                            ];
                            $total_earning += $amt;
                        } elseif($ptype == 'Deduction' && $amt > 0) {
                            $deductionsList[] = [
                                'title' => $salary->label ?? $salary->salarytype->salary_type,
                                'amount' => $amt
                            ];
                            $total_deduction += $amt;
                        }
                    }
                }

                // Reimbursement
                if(($salarySummary->reimbursement ?? 0) > 0){
                    $earningsList[] = [
                        'title' => 'Expense Reimbursement',
                        'amount' => (float)$salarySummary->reimbursement
                    ];
                    $total_earning += (float)$salarySummary->reimbursement;
                }

                // Attendance LOP
                if($other_deduction > 0){
                    $deductionsList[] = [
                        'title' => 'Attendance Loss of Pay (LOP)',
                        'amount' => $other_deduction
                    ];
                    $total_deduction += $other_deduction;
                }

                $gross_earning = $total_earning;
                $total_deductions = $total_deduction;
                $net_salary = (float)($salarySummary->net_salary ?? max(0, $gross_earning - $total_deductions));
            @endphp

            <!-- Earnings vs Deductions Split Table -->
            <div class="row g-4 mb-4">
                <!-- Earnings Column -->
                <div class="col-12 col-md-6">
                    <div class="border rounded-4 overflow-hidden h-100">
                        <div class="bg-success bg-gradient text-white p-3 fw-bold d-flex align-items-center justify-content-between">
                            <span><i class="fa-solid fa-arrow-trend-up me-2"></i> Earnings & Allowances</span>
                            <span>Amount (₹)</span>
                        </div>
                        <div class="p-3 bg-white">
                            @foreach($earningsList as $item)
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                                    <span class="text-dark">{{ $item['title'] }}</span>
                                    <span class="fw-semibold text-dark">₹{{ number_format((float)$item['amount'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top fw-bold text-success fs-6">
                                <span>Gross Earnings</span>
                                <span>₹{{ number_format((float)$gross_earning, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="col-12 col-md-6">
                    <div class="border rounded-4 overflow-hidden h-100">
                        <div class="bg-danger bg-gradient text-white p-3 fw-bold d-flex align-items-center justify-content-between">
                            <span><i class="fa-solid fa-arrow-trend-down me-2"></i> Deductions & LOP</span>
                            <span>Amount (₹)</span>
                        </div>
                        <div class="p-3 bg-white">
                            @if(count($deductionsList) > 0)
                                @foreach($deductionsList as $item)
                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                                        <span class="text-dark">{{ $item['title'] }}</span>
                                        <span class="fw-semibold text-dark">₹{{ number_format((float)$item['amount'], 2) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-muted small">No statutory or attendance deductions applied</div>
                            @endif
                            <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top fw-bold text-danger fs-6">
                                <span>Total Deductions</span>
                                <span>₹{{ number_format((float)$total_deductions, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Take-Home Hero Card -->
            <div class="p-4 rounded-4 border mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-7">
                        <span class="text-white-50 small text-uppercase fw-bold letter-spacing d-block mb-1">Net Salary Payable</span>
                        <h2 class="fw-bold text-white mb-1">₹{{ number_format((float)$net_salary, 2) }}</h2>
                        <span class="text-white-50 small">
                            <i class="fa-solid fa-receipt me-1"></i>
                            <strong>Amount in Words:</strong> {{ $salarySummary->net_salary_in_words ?? ucwords(\App\Helpers\Helper::convert($net_salary)) }} Only
                        </span>
                    </div>
                    <div class="col-12 col-md-5 text-md-end">
                        <div class="mb-2">
                            @php
                                $status = $salarySummary->status ?? 'Generated';
                            @endphp
                            <span class="badge {{ $status == 'Paid' ? 'bg-success text-white' : 'bg-warning text-dark' }} fs-6 px-3 py-1 rounded-pill">
                                <i class="fa-solid {{ $status == 'Paid' ? 'fa-circle-check' : 'fa-clock' }} me-1"></i> {{ $status }}
                            </span>
                        </div>
                        @if(!empty($salarySummary->generated_date))
                            <small class="text-white-50 d-block">
                                Generated on: {{ date('d M, Y', strtotime($salarySummary->generated_date)) }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Signatures & Authority -->
            <div class="row mt-5 pt-4 text-center">
                <div class="col-6">
                    <div class="border-top border-dark pt-2 mx-auto" style="max-width: 220px;">
                        <small class="fw-semibold text-dark d-block">Employee Signature</small>
                        <small class="text-muted">Acknowledged & Accepted</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border-top border-dark pt-2 mx-auto" style="max-width: 220px;">
                        <small class="fw-semibold text-dark d-block">Authorized Signatory</small>
                        <small class="text-muted">{{ $company->company_name ?? 'Management' }}</small>
                    </div>
                </div>
            </div>

            <!-- Payslip Footer Note -->
            <div class="text-center pt-4 mt-4 border-top text-muted small">
                <p class="mb-0">This is a system-generated payslip generated via STAFO HRMS. For inquiries, contact HR/Finance.</p>
            </div>

        </div>

    </div>
</div>

<style>
    @media print {
        .d-print-none {
            display: none !important;
        }
        .main-content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .payslip-container {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

@endsection