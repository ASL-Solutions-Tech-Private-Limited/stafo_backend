<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Salary Slip - {{ $monthArray[$salaryMonth - 1] }} {{ $salaryYear }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        
        .salary-slip {
            width: 100%;
            max-width: 100%;
            background: white;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3a6b92;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 9px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .slip-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            background: #ecf0f1;
            display: inline-block;
            padding: 3px 15px;
            border-radius: 15px;
        }
        
        .month-year {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
            color: #3a6b92;
        }
        
        /* Employee Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        
        .info-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 50%;
            background: #fafafa;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            min-width: 100px;
            display: inline-block;
        }
        
        .bank-details {
            font-size: 9px;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dotted #ddd;
        }
        
        /* Attendance Box */
        .attendance-box {
            background: #f0f3f8;
            padding: 6px;
            border-radius: 4px;
            margin-top: 5px;
        }
        
        .attendance-item {
            display: inline-block;
            width: 49%;
            font-size: 9px;
            margin: 2px 0;
        }
        
        /* Salary Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .salary-table th {
            background: #3a6b92;
            color: white;
            padding: 6px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #2c5a7a;
        }
        
        .salary-table td {
            border: 1px solid #ddd;
            padding: 5px;
            vertical-align: top;
        }
        
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .inner-table tr {
            border-bottom: 1px solid #eee;
        }
        
        .inner-table td {
            border: none;
            padding: 4px 2px;
        }
        
        .inner-table td:first-child {
            text-align: left;
        }
        
        .inner-table td:last-child {
            text-align: right;
        }
        
        .total-row {
            background: #e8edf5;
            font-weight: bold;
        }
        
        .total-row td {
            padding: 5px;
            font-weight: bold;
        }
        
        .gross-row {
            background: #d4edda;
        }
        
        .net-row {
            background: #e8f0fe;
        }
        
        .net-row td {
            padding: 8px;
        }
        
        .amount-word {
            font-size: 9px;
            color: #666;
            font-style: italic;
            margin-top: 3px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-paid {
            background: #28a745;
            color: white;
        }
        
        .status-generated {
            background: #ffc107;
            color: #333;
        }
        
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            text-align: center;
            color: #95a5a6;
        }
        
        .signature {
            margin-top: 8px;
            padding-top: 8px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        hr {
            margin: 6px 0;
            border: 0;
            border-top: 1px solid #ddd;
        }
        
        /* Ensure everything fits on one page */
        @page {
            margin: 0.5cm;
            size: A4;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .salary-slip {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="salary-slip">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $company->company_name }}</div>
            <div class="company-address">{{ $company->address }}, {{ $company->pin }}</div>
            <div class="slip-title">SALARY SLIP</div>
            <div class="month-year">{{ $monthArray[$salaryMonth - 1] }} {{ $salaryYear }}</div>
        </div>
        
        <!-- Employee Information -->
        <table class="info-table">
            <tr>
                <td>
                    <span class="info-label">Employee Name:</span> {{ $salarySummary->employee_name ?? $employee->name }}<br>
                    <span class="info-label">Employee Code:</span> {{ $employee->emp_id }}<br>
                    <span class="info-label">Designation:</span> {{ $employee->designation ?? 'N/A' }}<br>
                    <span class="info-label">Department:</span> {{ $employee->department->name ?? 'N/A' }}<br>
                    <span class="info-label">DOJ:</span> {{ date('d/m/Y', strtotime($employee->date_of_joining)) }}<br>
                    
                    @if($employee->bankAccount)
                        <div class="bank-details">
                            <span class="info-label">A/C No:</span> {{ $employee->bankAccount->account_number }}<br>
                            <span class="info-label">Bank:</span> {{ $employee->bankAccount->bank_name }}<br>
                            <span class="info-label">IFSC:</span> {{ $employee->bankAccount->ifsc_code }}
                        </div>
                    @endif
                </td>
                <td>
                    <span class="info-label">PF Number:</span> {{ $employee->pf_number ?? 'N/A' }}<br>
                    <span class="info-label">ESI Number:</span> {{ $employee->esi_number ?? 'N/A' }}<br>
                    <span class="info-label">UAN Number:</span> {{ $employee->uan_number ?? 'N/A' }}<br>
                    
                    <div class="attendance-box">
                        <div style="font-weight: bold; margin-bottom: 4px;">Attendance</div>
                        <div class="attendance-item">📅 Working Days: <strong>{{ $salarySummary->working_days ?? 0 }}</strong></div>
                        <div class="attendance-item">🎉 Holidays: <strong>{{ $salarySummary->holiday_count ?? 0 }}</strong></div>
                        <div class="attendance-item">❌ Absent: <strong>{{ $salarySummary->absent_days ?? 0 }}</strong></div>
                        <div class="attendance-item">✅ Present: <strong>{{ ($salarySummary->working_days ?? 0) - ($salarySummary->absent_days ?? 0) }}</strong></div>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Salary Details -->
        <table class="salary-table">
            <thead>
                <tr>
                    <th width="50%">EARNINGS</th>
                    <th width="50%">DEDUCTIONS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $earnings_list = '';
                    $deductions_list = '';
                    $total_earning = 0;
                    $total_deduction = 0;
                    
                    // Basic Salary
                    $basic_salary = $salarySummary->basic_salary ?? 0;
                    $earnings_list .= '<tr><td>Basic Salary</td><td class="text-right">₹ ' . number_format($basic_salary, 2) . '</td></tr>';
                    $total_earning += $basic_salary;
                    
                    // Process salary components
                    foreach($employeeSalaries as $salary){
                        if($salary->salarytype->payment_type == 'Earning'){
                            $earnings_list .= '<tr><td>' . $salary->salarytype->salary_type . '</td><td class="text-right">₹ ' . number_format($salary->amount, 2) . '</td></tr>';
                            $total_earning += $salary->amount;
                        }
                        if($salary->salarytype->payment_type == 'Deduction'){
                            $deductions_list .= '<tr><td>' . $salary->salarytype->salary_type . '</td><td class="text-right">₹ ' . number_format($salary->amount, 2) . '</td></tr>';
                            $total_deduction += $salary->amount;
                        }
                    }
                    
                    // Reimbursement
                    $reimbursement = $salarySummary->reimbursement ?? 0;
                    if($reimbursement > 0){
                        $earnings_list .= '<tr><td>Reimbursement</td><td class="text-right">₹ ' . number_format($reimbursement, 2) . '</td></tr>';
                        $total_earning += $reimbursement;
                    }
                    
                    // Other Deduction
                    $other_deduction = $salarySummary->other_deduction ?? 0;
                    if($other_deduction > 0){
                        $deductions_list .= '<tr><td>Other Deduction</td><td class="text-right">₹ ' . number_format($other_deduction, 2) . '</td></tr>';
                        $total_deduction += $other_deduction;
                    }
                    
                    $gross_earning = $total_earning;
                    $net_salary = $salarySummary->net_salary ?? ($gross_earning - $total_deduction);
                @endphp
                
                <tr>
                    <td style="vertical-align: top;">
                        <table class="inner-table">
                            {!! $earnings_list !!}
                            @if(empty($earnings_list))
                                <tr><td colspan="2" class="text-center">- No Earnings -</td></tr>
                            @endif
                        </table>
                    </td>
                    <td style="vertical-align: top;">
                        <table class="inner-table">
                            {!! $deductions_list !!}
                            @if(empty($deductions_list))
                                <tr><td colspan="2" class="text-center">- No Deductions -</td></tr>
                            @endif
                        </table>
                    </td>
                </tr>
                
                <!-- Total Row -->
                <tr class="total-row">
                    <td><strong>Total Earnings</strong></td>
                    <td><strong>Total Deductions</strong></td>
                </tr>
                <tr class="total-row">
                    <td class="text-right"><strong>₹ {{ number_format($gross_earning, 2) }}</strong></td>
                    <td class="text-right"><strong>₹ {{ number_format($total_deduction, 2) }}</strong></td>
                </tr>
                
                <!-- Gross Salary -->
                <tr class="gross-row">
                    <td colspan="2">
                        <strong>Gross Salary: ₹ {{ number_format($gross_earning, 2) }}</strong>
                        <div class="amount-word">{{ ucwords(\App\Helpers\Helper::convert($gross_earning)) }} Only</div>
                    </td>
                </tr>
                
                <!-- Net Salary -->
                <tr class="net-row">
                    <td colspan="2">
                        <table width="100%">
                            <tr>
                                <td width="70%"><strong>Net Salary Payable:</strong></td>
                                <td width="30%" class="text-right"><strong style="font-size: 14px;">₹ {{ number_format($net_salary, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="amount-word">{{ ucwords(\App\Helpers\Helper::convert($net_salary)) }} Only</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <!-- Status -->
                @if($salarySummary && $salarySummary->status)
                <tr>
                    <td colspan="2" style="background: #f9f9f9;">
                        <strong>Status:</strong> 
                        <span class="status-badge {{ $salarySummary->status == 'Paid' ? 'status-paid' : 'status-generated' }}">
                            {{ $salarySummary->status }}
                        </span>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <strong>Generated:</strong> {{ date('d/m/Y', strtotime($salarySummary->generated_date)) }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Payment Info & Signature -->
        <table width="100%" style="margin-top: 8px;">
            <tr>
                <td width="60%">
                    <strong>Payment Mode:</strong> Bank Transfer
                </td>
                <td width="40%" class="text-right">
                    <strong>For {{ $company->company_name }}</strong>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="text-right" style="padding-top: 25px;">
                    <span style="border-top: 1px solid #333; padding-top: 3px; font-size: 9px;">Authorized Signatory</span>
                </td>
            </tr>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            This is a computer generated salary slip | Valid without signature
        </div>
    </div>
</body>
</html>