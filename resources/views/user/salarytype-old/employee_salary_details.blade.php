@extends('user.layouts.app')

@section('title', 'Employee Salary Details')

@section('content')

<div class="card mt-4 p-3">
    <div class="container">
        <div class="col-md-12 text-center">
            <h2 class="fw-bold">{{ $company->company_name }}</h2>
            <span>{{ $company->address }}, {{ $company->pin }}</span>
            <h6>Pay Slip of {{ $monthArray[$salarySummary->salary_month - 1] }}, {{ $salarySummary->salary_year }}</h6>
        </div>

        <table class="salary-main-table" width="100%" cellspacing="0" cellpadding="5" border="1">
            <tr style="background-color: #f2f2f2;">
                <td>
                    <b>Employee Name:</b> {{ $salarySummary->employee_name }} <br>
                    <b>Employee Code:</b> {{ $salarySummary->employee->emp_id ?? 'N/A' }} <br>
                    <b>DOJ:</b> {{ date('dS F, Y', strtotime($salarySummary->employee->date_of_joining ?? date('Y-m-d'))) }} <br>
                    
                    <!-- Bank Account Details -->
                    @if($salarySummary->employee && $salarySummary->employee->bankAccount)
                        <b>Account No:</b> {{ $salarySummary->employee->bankAccount->account_number }} <br>
                        <b>Bank Name:</b> {{ $salarySummary->employee->bankAccount->bank_name }} <br>
                        <b>IFSC Code:</b> {{ $salarySummary->employee->bankAccount->ifsc_code }}
                    @else
                        <b>Account No:</b> N/A <br>
                        <b>Bank Details:</b> Not Available
                    @endif
                </td>
                
                <td>
                    <b>PF Number:</b> {{ $salarySummary->employee->pf_number ?? 'N/A' }} <br>
                    <b>ESI Number:</b> {{ $salarySummary->employee->esi_number ?? 'N/A' }} <br>
                    
                    <!-- Attendance Details from Summary Table -->
                    <b>Total Working Days:</b> {{ $salarySummary->total_working_days ?? $salarySummary->working_days }} <br>
                    <b>Holidays:</b> {{ $salarySummary->holiday_count ?? 0 }} <br>
                    <b>Actual Working Days:</b> {{ $salarySummary->working_days }} <br>
                    <b>Absent Days:</b> {{ $salarySummary->absent_days }} <br>
                    <b>Present Days:</b> {{ ($salarySummary->working_days - $salarySummary->absent_days) }} <br>
                </td>
            </tr>
            
            <tr style="background-color: rgb(99, 120, 179); font-weight: bold; color: white;">
                <td width="50%">Earning</td>
                <td width="50%">Deduction</td>
            </tr>
            
            @php
                $earning = '';
                $deduction = '';
                $other_deduction = $salarySummary->other_deduction;
                $total_earning = 0;
                $total_deduction = 0;
                
                // Basic Salary
                $earning .= '<div class="row mb-1">
                            <div class="col-md-6">Basic Salary</div>
                            <div class="col-md-6 text-end">' . number_format($salarySummary->basic_salary, 2, '.', ',') . '</div>
                        </div>';
                $total_earning += $salarySummary->basic_salary;
                
                // Loop through salary components
                foreach($employeeSalaries as $salary){
                    if($salary->salarytype->payment_type == 'Earning'){
                        $earning .= '<div class="row mb-1">
                            <div class="col-md-6">' . $salary->salarytype->salary_type . '</div>
                            <div class="col-md-6 text-end">' . number_format($salary->amount, 2, '.', ',') . '</div>
                        </div>';
                        $total_earning += $salary->amount;
                    }
                    if($salary->salarytype->payment_type == 'Deduction'){
                        $deduction .= '<div class="row mb-1">
                            <div class="col-md-6">' . $salary->salarytype->salary_type . '</div>
                            <div class="col-md-6 text-end">' . number_format($salary->amount, 2, '.', ',') . '</div>
                        </div>';
                        $total_deduction += $salary->amount;
                    }
                }
                
                // Reimbursement
                if($salarySummary->reimbursement > 0){
                    $earning .= '<div class="row mb-1">
                                <div class="col-md-6">Reimbursement</div>
                                <div class="col-md-6 text-end">' . number_format($salarySummary->reimbursement, 2, '.', ',') . '</div>
                            </div>';
                }
                
                // Other Deduction
                if($other_deduction > 0){
                    $deduction .= '<div class="row mb-1">
                                <div class="col-md-6">Other Deduction</div>
                                <div class="col-md-6 text-end">' . number_format($other_deduction, 2, '.', ',') . '</div>
                            </div>';
                }
                
                // Calculate totals
                $gross_earning = $total_earning + $salarySummary->reimbursement;
                $total_deductions = $total_deduction + $other_deduction;
                $net_salary = $gross_earning - $total_deductions;
            @endphp
            
            <tr style="font-size: 12px;">
                <td style="vertical-align: top;">{!! $earning !!}</td>
                <td style="vertical-align: top;">{!! $deduction !!}</td>
            </tr>
            
            <tr style="background-color: rgb(147, 170, 233); font-weight: bold; color: black;">
                <td>
                    <div class="row">
                        <div class="col-md-6">Total Earning</div>
                        <div class="col-md-6 text-end">{{ number_format($gross_earning, 2, '.', ',') }}</div>
                    </div>
                </td>
                <td>
                    <div class="row">
                        <div class="col-md-6">Total Deduction</div>
                        <div class="col-md-6 text-end">{{ number_format($total_deductions, 2, '.', ',') }}</div>
                    </div>
                </td>
            </tr>
            
            <tr style="background-color: #d4edda;">
                <td colspan="2">
                    <b>Gross Salary: {{ number_format($gross_earning, 2, '.', ',') }} 
                    ({{ ucwords(\App\Helpers\Helper::convert($gross_earning)) }} Only)</b>
                </td>
            </tr>
            
            <tr style="background-color: #e8f0fe;">
                <td colspan="2">
                    <b>Net Salary: {{ number_format($salarySummary->net_salary, 2, '.', ',') }} 
                    ({{ ucwords(\App\Helpers\Helper::convert($salarySummary->net_salary)) }} Only)</b>
                </td>
            </tr>
            
            @if($salarySummary->status)
            <tr>
                <td colspan="2">
                    <b>Status:</b> 
                    <span class="badge bg-{{ $salarySummary->status == 'Paid' ? 'success' : 'warning' }}">
                        {{ $salarySummary->status }}
                    </span>
                    <br>
                    <b>Generated Date:</b> {{ date('dS F, Y', strtotime($salarySummary->generated_date)) }}
                </td>
            </tr>
            @endif
         </table>
        
        <div class="text-center mt-4">
          
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn {
            display: none;
        }
        .card {
            margin: 0;
            padding: 0;
        }
        .salary-main-table {
            margin-top: 0;
        }
    }
    .salary-main-table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }
    .salary-main-table td {
        padding: 10px;
        vertical-align: top;
    }
    .row {
        margin-bottom: 5px;
        display: flex;
        justify-content: space-between;
    }
    .col-md-6 {
        flex: 0 0 50%;
    }
    .text-end {
        text-align: right;
    }
    .badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        display: inline-block;
    }
    .bg-success {
        background-color: #28a745;
        color: white;
    }
    .bg-warning {
        background-color: #ffc107;
        color: black;
    }
    .mt-4 {
        margin-top: 1.5rem;
    }
    .text-center {
        text-align: center;
    }
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        margin: 0 5px;
        cursor: pointer;
    }
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
    }
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
    }
    .fas {
        margin-right: 5px;
    }
</style>

@endsection