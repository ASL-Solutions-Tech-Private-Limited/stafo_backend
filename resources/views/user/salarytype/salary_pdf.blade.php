<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PAYSLIP – {{ $monthName }} {{ $salaryYear }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 9mm 13mm 9mm 13mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 8.2pt;
            line-height: 1.35;
            color: #1e293b;
            background: #ffffff;
        }

        /* Utility classes */
        .w-full { width: 100%; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-logo-td {
            width: 16%;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo-td img {
            max-height: 56px;
            max-width: 95px;
        }
        .header-content-td {
            width: 84%;
            vertical-align: middle;
            text-align: center;
            padding-right: 15px;
        }
        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }
        .company-address {
            font-size: 7.8pt;
            color: #334155;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        .company-meta {
            font-size: 7.3pt;
            color: #64748b;
            margin-top: 3px;
        }

        /* Divider */
        .header-divider {
            height: 2.5px;
            background-color: #63121d;
            width: 100%;
            margin-bottom: 8px;
        }

        /* Payslip Title Bar */
        .title-bar {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #63121d;
            margin-bottom: 10px;
        }
        .title-table {
            width: 100%;
            border-collapse: collapse;
        }
        .title-heading {
            width: 44%;
            font-size: 10pt;
            font-weight: bold;
            color: #63121d;
            letter-spacing: 0.5px;
            vertical-align: middle;
            padding: 5px 8px;
        }
        .title-meta {
            width: 56%;
            font-size: 7.8pt;
            color: #334155;
            text-align: right;
            vertical-align: middle;
            padding: 5px 8px;
        }

        /* Section Title */
        .section-header {
            background-color: #f1f5f9;
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4.5px 8px;
            border: 1px solid #cbd5e1;
            border-bottom: none;
        }

        /* Employee Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-bottom: 11px;
        }
        .details-table td {
            padding: 4.5px 8px;
            font-size: 8pt;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
        }
        .details-table .lbl {
            width: 18%;
            color: #475569;
            font-weight: normal;
            background-color: #f8fafc;
        }
        .details-table .val {
            width: 32%;
            color: #0f172a;
            font-weight: bold;
        }

        /* Salary Breakdown Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-bottom: 11px;
        }
        .salary-table th {
            padding: 5px 8px;
            font-size: 8pt;
            font-weight: bold;
            vertical-align: middle;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #0f172a;
        }
        .salary-table td {
            padding: 4.5px 8px;
            font-size: 8pt;
            vertical-align: middle;
            border-left: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }
        .salary-table .border-right-bold {
            border-right: 1.5px solid #cbd5e1;
        }
        .total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 1.5px solid #cbd5e1 !important;
            border-bottom: 1.5px solid #cbd5e1 !important;
            padding: 5px 8px;
            color: #0f172a;
        }

        /* Net Pay Box */
        .net-box {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #63121d;
            background-color: #fef8f8;
            margin-bottom: 11px;
        }
        .net-box td {
            padding: 6px 10px;
            vertical-align: middle;
        }
        .net-title {
            font-size: 8.2pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .net-amount {
            font-size: 13.5pt;
            font-weight: bold;
            color: #63121d;
            margin-top: 2px;
        }
        .net-words {
            font-size: 8pt;
            color: #1e293b;
            margin-top: 2px;
            line-height: 1.3;
        }

        /* Signatures & Footer */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .footer-table td {
            vertical-align: bottom;
            font-size: 7.8pt;
        }
        .disclaimer-text {
            color: #64748b;
            line-height: 1.4;
        }
        .sign-area {
            text-align: right;
        }
        .sign-box {
            display: inline-block;
            text-align: center;
            width: 160px;
        }
        .sign-line {
            border-top: 1px solid #334155;
            padding-top: 4px;
            font-size: 7.8pt;
            color: #334155;
            font-weight: bold;
        }

        .bottom-bar {
            height: 2.5px;
            background-color: #63121d;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Company Header -->
    <table class="header-table">
        <tr>
            @if(!empty($logoBase64))
                <td class="header-logo-td">
                    <img src="{{ $logoBase64 }}" alt="Logo">
                </td>
            @endif
            <td class="header-content-td" @if(empty($logoBase64)) style="width: 100%; padding-right: 0;" @endif>
                <div class="company-name">{{ $company->company_name ?? '' }}</div>
                @if(!empty($regAddress))
                    <div class="company-address"><strong>Registered Office:</strong> {{ $regAddress }}</div>
                @endif
                @if(!empty($branchAddress))
                    <div class="company-address"><strong>Branch Office:</strong> {{ $branchAddress }}</div>
                @endif
                <div class="company-meta">
                    @if(!empty($company->gst_number))
                        <strong>GSTIN:</strong> {{ $company->gst_number }} &nbsp;|&nbsp;
                    @endif
                    @if(!empty($company->pan_number))
                        <strong>PAN:</strong> {{ $company->pan_number }} &nbsp;|&nbsp;
                    @endif
                    @if(!empty($company->email))
                        <strong>Email:</strong> {{ $company->email }}
                    @endif
                    @if(!empty($company->mobile_no))
                        &nbsp;|&nbsp; <strong>Phone:</strong> {{ $company->mobile_no }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="header-divider"></div>

    <!-- Title Bar -->
    <div class="title-bar">
        <table class="title-table">
            <tr>
                <td class="title-heading">
                    PAYSLIP – {{ strtoupper($monthName) }} {{ $salaryYear }}
                </td>
                <td class="title-meta">
                    <strong>Pay Period:</strong> {{ $payPeriod }} &nbsp;&nbsp;|&nbsp;&nbsp;
                    <strong>Pay Date:</strong> {{ $payDate }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Employee Details Table -->
    <div class="section-header">Employee Details</div>
    <table class="details-table">
        <tr>
            <td class="lbl">Employee ID</td>
            <td class="val">{{ $employeeId }}</td>
            <td class="lbl">Employee Name</td>
            <td class="val">{{ $employeeName }}</td>
        </tr>
        <tr>
            <td class="lbl">Designation</td>
            <td class="val">{{ $designationName }}</td>
            <td class="lbl">Department</td>
            <td class="val">{{ $departmentName }}</td>
        </tr>
        <tr>
            <td class="lbl">Date of Joining</td>
            <td class="val">{{ $doj }}</td>
            <td class="lbl">Branch</td>
            <td class="val">{{ $branchName ?: 'Main Branch' }}</td>
        </tr>
        <tr>
            <td class="lbl">Bank Name</td>
            <td class="val">{{ $bankName }}</td>
            <td class="lbl">Bank A/C No.</td>
            <td class="val">{{ $bankAccount }}</td>
        </tr>
        <tr>
            <td class="lbl">IFSC Code</td>
            <td class="val">{{ $ifscCode }}</td>
            <td class="lbl">PAN / UAN</td>
            <td class="val">
                @if(!empty($panNumber) && !empty($uanNumber))
                    PAN: {{ $panNumber }} | UAN: {{ $uanNumber }}
                @elseif(!empty($panNumber))
                    {{ $panNumber }}
                @elseif(!empty($uanNumber))
                    {{ $uanNumber }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">Working Days</td>
            <td class="val">{{ $workingDays }}</td>
            <td class="lbl">Paid Days</td>
            <td class="val">
                {{ $paidDays }}
                @if($absentDays > 0)
                    <span style="color: #b91c1c; font-weight: normal; font-size: 7.5pt;">(Absent: {{ $absentDays }})</span>
                @endif
            </td>
        </tr>
        @if(!empty($pfNumber) || !empty($esiNumber))
        <tr>
            <td class="lbl">PF Number</td>
            <td class="val">{{ $pfNumber ?: 'N/A' }}</td>
            <td class="lbl">ESI Number</td>
            <td class="val">{{ $esiNumber ?: 'N/A' }}</td>
        </tr>
        @endif
    </table>

    <!-- Salary Components (Earnings & Deductions Side-by-Side) -->
    @php
        $maxRows = max(count($earningsList), count($deductionsList));
        if ($maxRows < 3) {
            $maxRows = 3;
        }
    @endphp

    <table class="salary-table">
        <thead>
            <tr>
                <th colspan="2" class="border-right-bold" style="width: 50%;">EARNINGS</th>
                <th colspan="2" style="width: 50%;">DEDUCTIONS</th>
            </tr>
            <tr>
                <th style="width: 33%; font-weight: bold;">Description</th>
                <th class="text-right border-right-bold" style="width: 17%; font-weight: bold;">Amount (₹)</th>
                <th style="width: 33%; font-weight: bold;">Description</th>
                <th class="text-right" style="width: 17%; font-weight: bold;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < $maxRows; $i++)
                @php
                    $earn = $earningsList[$i] ?? null;
                    $ded = $deductionsList[$i] ?? null;
                @endphp
                <tr>
                    <td style="color: #1e293b;">
                        {{ $earn ? $earn['name'] : '' }}
                    </td>
                    <td class="text-right border-right-bold">
                        {{ $earn ? number_format($earn['amount'], 2) : '' }}
                    </td>
                    <td style="color: #1e293b;">
                        {{ $ded ? $ded['name'] : '' }}
                    </td>
                    <td class="text-right">
                        {{ $ded ? number_format($ded['amount'], 2) : '' }}
                    </td>
                </tr>
            @endfor

            <!-- Total Row -->
            <tr class="total-row">
                <td>Total Earnings (Gross)</td>
                <td class="text-right border-right-bold">₹ {{ number_format($grossSalary, 2) }}</td>
                <td>Total Deductions</td>
                <td class="text-right">₹ {{ number_format($totalDeductions, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Net Salary Box -->
    <table class="net-box">
        <tr>
            <td style="width: 60%;">
                <div class="net-title">Net Salary Payable (Monthly In-Hand)</div>
                <div class="net-amount">₹ {{ number_format($netSalary, 2) }}</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div style="font-size: 7.5pt; color: #64748b; text-transform: uppercase; font-weight: bold;">Payment Status</div>
                <div style="font-size: 9pt; font-weight: bold; color: {{ ($salarySummary->status ?? '') === 'Paid' ? '#15803d' : '#0369a1' }}; margin-top: 2px;">
                    {{ $salarySummary->status ?? 'Generated' }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-top: 1px dashed #fca5a5; padding-top: 5px;">
                <div class="net-words">
                    <strong>Amount in Words:</strong> {{ $netSalaryInWords }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Signatures & Notes -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%;">
                <div class="disclaimer-text">
                    <strong>Payment Mode:</strong> Bank Transfer<br>
                    <strong>Note:</strong> This is a computer-generated payslip and does not require a physical signature if generated electronically.
                </div>
            </td>
            <td style="width: 40%;" class="sign-area">
                <div class="sign-box">
                    <div style="font-size: 8pt; font-weight: bold; color: #1e293b; margin-bottom: 30px;">
                        For {{ $company->company_name ?? 'Company' }}
                    </div>
                    <div class="sign-line">
                        Authorized Signatory
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Bottom Accent Bar -->
    <div class="bottom-bar"></div>
</body>
</html>