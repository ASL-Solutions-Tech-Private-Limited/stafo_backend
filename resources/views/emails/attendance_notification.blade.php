<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $type == 'punch_in' ? 'Punch In Confirmation' : 'Punch Out Confirmation' }}</title>
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
            background: {{ $type == 'punch_in' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)' }};
            padding: 36px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            line-height: 56px;
        }
        .header-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }
        .header-subtitle {
            font-size: 14px;
            opacity: 0.92;
            margin: 0;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px 0;
            color: #374151;
        }
        .greeting strong {
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: {{ $type == 'punch_in' ? '#d1fae5' : '#ede9fe' }};
            color: {{ $type == 'punch_in' ? '#065f46' : '#5b21b6' }};
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
            width: 40%;
        }
        .details-value {
            color: #111827;
            font-weight: 600;
            text-align: right;
            width: 60%;
        }
        .highlight-box {
            background-color: {{ $type == 'punch_in' ? '#ecfdf5' : '#f5f3ff' }};
            border-left: 4px solid {{ $type == 'punch_in' ? '#10b981' : '#6366f1' }};
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 22px 0 10px 0;
            font-size: 13.5px;
            color: {{ $type == 'punch_in' ? '#065f46' : '#4c1d95' }};
            line-height: 1.5;
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
            color: #6366f1;
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
                        {{ $type == 'punch_in' ? '☀️' : '🌙' }}
                    </div>
                    <h1 class="header-title">
                        {{ $type == 'punch_in' ? 'Punch-In Successful!' : 'Punch-Out Successful!' }}
                    </h1>
                    <p class="header-subtitle">
                        {{ $type == 'punch_in' ? 'Your attendance has been recorded for today.' : 'Your shift logout has been recorded successfully.' }}
                    </p>
                </td>
            </tr>

            <!-- Content Area -->
            <tr>
                <td class="content">
                    <p class="greeting">
                        Hello <strong>{{ $employeeName }}</strong>,
                    </p>
                    <p style="margin: 0 0 16px 0; font-size: 14.5px; color: #4b5563; line-height: 1.6;">
                        Your attendance punch has been registered successfully on <strong>{{ config('app.name', 'Stafo') }}</strong>. Below are the summary details:
                    </p>

                    <div style="text-align: center; margin: 16px 0;">
                        <span class="badge">
                            {{ $type == 'punch_in' ? '✅ Punch In' : '👋 Punch Out' }}
                        </span>
                    </div>

                    <!-- Details Box -->
                    <div class="details-card">
                        <table class="details-table">
                            <tr>
                                <td class="details-label">Employee ID</td>
                                <td class="details-value">{{ $employeeCode ?? '#' . $employeeId }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Date</td>
                                <td class="details-value">{{ $punchDate }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Time</td>
                                <td class="details-value" style="color: {{ $type == 'punch_in' ? '#059669' : '#4f46e5' }}; font-size: 15px;">
                                    <strong>{{ $punchTime }}</strong>
                                </td>
                            </tr>
                            @if(!empty($method))
                            <tr>
                                <td class="details-label">Punch Mode</td>
                                <td class="details-value">{{ $method }}</td>
                            </tr>
                            @endif
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
                            <tr>
                                <td class="details-label">Attendance Status</td>
                                <td class="details-value" style="color: #059669;">
                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; margin-right: 4px;"></span>
                                    Present
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Motivational note -->
                    <div class="highlight-box">
                        @if($type == 'punch_in')
                            🚀 <strong>Tip:</strong> Wishing you a productive and successful work day ahead!
                        @else
                            🌟 <strong>Good job!</strong> Thank you for your dedication today.!
                        @endif
                    </div>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p style="margin: 0 0 6px 0;">
                        This is an automated attendance notification sent by <span class="brand-name">{{ config('app.name', 'Stafo') }}</span> HRMS.
                    </p>
                    <p style="margin: 0;">
                        If you believe this punch was recorded in error, please contact your HR or Department Administrator immediately.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
