<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $monthArray[$salaryMonth - 1] }} {{ $salaryYear }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }

        .salary-box {
            width: 100%;
            max-width: 750px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #ffffff;
            page-break-inside: avoid;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 12px;
            text-align: center;
            margin-bottom: 15px;
        }

        .section-header {
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 5px 8px;
            font-size: 12px;
        }

        .gray-bg {
            background-color: #f5f5f5;
        }

        .net-pay {
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
        }

        .footer-note {
            font-size: 11px;
            margin-top: 20px;
            text-align: left;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="salary-box">
        <div class="title">{{ $company->company_name }}</div>
        <div class="subtitle">{{ $company->address }}</div>
        <h4 style="text-align: center; margin-top: 10px;">
            Pay Slip of {{ $monthArray[$salaryMonth - 1] }} {{ $salaryYear }}
        </h4>
        <p style="text-align: right; font-size: 11px;"><em>All amounts in INR</em></p>

        <!-- Employee Details -->
        <table class="table">
            <tr>
                <th>Emp Code</th>
                <td>{{ $employee->emp_id ?? 'N/A' }}</td>
                <th>Emp Name</th>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                <th>Designation</th>
                <td>{{ $employee->position ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Grade</th>
                <td>{{ $employee->grade ?? 'N/A' }}</td>
                <th>Gender</th>
                <td>{{ $employee->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>DOB</th>
                <td>{{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') }}</td>
                <th>DOJ</th>
                <td>
                    {{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A' }}
                </td>
            </tr>
            <tr>
                <th>Payable Days</th>
                <td>{{ $employee->payable_days ?? 'N/A' }}</td>
                <th>Location</th>
                <td>{{ $employee->city->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Bank IFSC</th>
                <td>{{ $employee->bankAccount->ifsc_code ?? 'N/A' }}</td>
                <th>Bank A/c</th>
                <td>{{ $employee->bankAccount->account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fixed Salary</th>
                <td colspan="3">₹{{ number_format($employee->salary, 2) ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- Dynamic Salary Groups -->
        @foreach ($salaryGroups as $groupName => $items)
            <div class="section-header">{{ $groupName }}</div>
            <table class="table">
                <thead class="gray-bg">
                    <tr>
                        <th>Description</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $entry)
                        <tr>
                            <td>{{ $entry['label'] }}</td>
                            <td>{{ number_format($entry['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="gray-bg">
                        <th>Total {{ $groupName }}</th>
                        <th>{{ number_format($salaryTotals[$groupName], 2) }}</th>
                    </tr>
                </tbody>
            </table>
        @endforeach

        <!-- Net Pay Calculation -->
        @php
            $fixedSalary = (float) $employee->salary ?? 0;
            $totalEarnings = $salaryTotals['Basic'] ?? 0;

            $totalDeductions = $salaryTotals['Deduction'] ?? 0;

            // Net Pay = Fixed Salary + Earnings - Deductions
            $netPay = $fixedSalary + $totalEarnings - $totalDeductions;
        @endphp

        <div class="net-pay">Net Pay: ₹{{ number_format($netPay, 2) }}</div>
        <p><strong>In Words:</strong> {{ ucwords(\App\Helpers\Helper::convert($netPay)) }} Only</p>

        <p class="footer-note">* This is a system-generated payslip and does not require a signature.</p>
    </div>
</body>

</html>
