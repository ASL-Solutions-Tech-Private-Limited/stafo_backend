<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
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
        
        <h4 style="text-align: center; margin-top: 10px;">
            Invoice
        </h4>
        

        <!-- Employee Details -->
        <table class="table">
            <tr>
                <th>Transaction ID</th>
                <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Payment Status</th>
                <td>{{ $payment->payment_status ?? 'N/A' }}</td>
                
            </tr>
            <tr>
                <th>Payment Amount</th>
                <td>{{ $payment->payment_amount ?? 'N/A' }}</td>
               
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ $payment->created_at ?? 'N/A' }}</td>
               
            </tr>
           
        </table>

    </div>
</body>

</html>
