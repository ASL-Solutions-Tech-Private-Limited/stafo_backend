<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Missed Punch-Out Notification - {{ $companyName }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding: 30px 15px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05), 0 2px 6px rgba(0, 0, 0, 0.03);
            border: 1px solid #e5e7eb;
        }
        .header-bg {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 36px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            background-color: rgba(255, 255, 255, 0.22);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            line-height: 58px;
        }
        .header-company {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            opacity: 0.9;
            margin-bottom: 4px;
        }
        .header-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }
        .header-subtitle {
            font-size: 14px;
            opacity: 0.95;
            margin: 0;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 16px 0;
            color: #374151;
        }
        .greeting strong {
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 12.5px;
            font-weight: 700;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .details-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 10px 6px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-label {
            color: #6b7280;
            font-weight: 500;
            width: 42%;
        }
        .details-value {
            color: #111827;
            font-weight: 600;
            text-align: right;
            width: 58%;
        }
        .alert-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0 10px 0;
            font-size: 13.5px;
            color: #92400e;
            line-height: 1.55;
        }
        .footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer a {
            color: #d97706;
            text-decoration: none;
        }
        .brand-name {
            font-weight: 700;
            color: #4b5563;
        }
        @media screen and (max-width: 600px) {
            .wrapper {
                padding: 15px 5px;
            }
            .content {
                padding: 24px 18px;
            }
            .header-bg {
                padding: 28px 20px;
            }
            .details-table td {
                font-size: 13px;
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" align="center">
            <!-- Header -->
            <tr>
                <td class="header-bg">
                    <div class="header-icon">
                        ⏰
                    </div>
                    <div class="header-company">{{ $companyName }}</div>
                    <h1 class="header-title">Missed Punch-Out Alert</h1>
                    <p class="header-subtitle">
                        No punch-out record was detected for your shift today.
                    </p>
                </td>
            </tr>

            <!-- Content Area -->
            <tr>
                <td class="content">
                    <p class="greeting">
                        Dear <strong>{{ $employeeName }}</strong>,
                    </p>
                    <p style="margin: 0 0 16px 0; font-size: 14.5px; color: #4b5563; line-height: 1.6;">
                        This is an automated attendance notification from <strong>{{ $companyName }}</strong>. According to our records, you punched in today but forgot to punch out at the end of your shift.
                    </p>

                    <div style="text-align: center; margin: 16px 0;">
                        <span class="badge">
                            ⚠️ Punch-Out Not Recorded
                        </span>
                    </div>

                    <!-- Details Box -->
                    <div class="details-card">
                        <table class="details-table">
                            <tr>
                                <td class="details-label">Employee Name</td>
                                <td class="details-value">{{ $employeeName }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Employee ID</td>
                                <td class="details-value">{{ $employeeCode ?? '#' . $employeeId }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Date</td>
                                <td class="details-value">{{ $formattedDate }}</td>
                            </tr>
                            @if(!empty($inTime))
                            <tr>
                                <td class="details-label">Check-in Time</td>
                                <td class="details-value" style="color: #059669;">{{ $inTime }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="details-label">Check-out Time</td>
                                <td class="details-value" style="color: #dc2626;">Not Recorded (Missing)</td>
                            </tr>
                            @if(!empty($branchName))
                            <tr>
                                <td class="details-label">Branch</td>
                                <td class="details-value">{{ $branchName }}</td>
                            </tr>
                            @endif
                            @if(!empty($departmentName))
                            <tr>
                                <td class="details-label">Department</td>
                                <td class="details-value">{{ $departmentName }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <!-- Action Note -->
                    <div class="alert-box">
                        📌 <strong>What should you do?</strong><br>
                        Please ensure you regularly punch out at the end of every work shift. If this was a genuine oversight or you were on official duty, please reach out to your HR department or supervisor to regularize your attendance record.
                    </div>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p style="margin: 0 0 6px 0;">
                        This email was sent on behalf of <span class="brand-name">{{ $companyName }}</span> via {{ config('app.name', 'Stafo') }} HRMS.
                    </p>
                    @if(!empty($companyEmail))
                    <p style="margin: 0 0 6px 0;">
                        Company Contact: <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>
                    </p>
                    @endif
                    <p style="margin: 0;">
                        If you have questions regarding your shift or attendance, please contact your company's HR manager.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
