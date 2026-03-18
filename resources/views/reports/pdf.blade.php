<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FlowTimeUp Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .summary-box {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FlowTimeUp Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <h3>Summary</h3>
        <table>
            <tr>
                <th>Total Time</th>
                <td>{{ $summary['total_time'] }}</td>
                <th>Total Sessions</th>
                <td>{{ $summary['total_sessions'] }}</td>
            </tr>
            <tr>
                <th>Billable Time</th>
                <td>{{ $summary['billable_time'] }}</td>
                <th>Total Earnings</th>
                <td>{{ number_format($summary['total_earnings'], 2) }}</td>
            </tr>
        </table>
    </div>

    <h3>Sessions</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Task</th>
                <th>Project</th>
                <th>Duration</th>
                <th class="text-right">Earnings</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($session['start_time'])->format('Y-m-d') }}</td>
                    <td>{{ $session['task'] }}</td>
                    <td>{{ $session['project'] }}</td>
                    <td>{{ $session['duration'] }}</td>
                    <td class="text-right">
                        @if($session['is_billable'])
                            {{ number_format($session['earnings'], 2) }} {{ $session['currency'] }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>FlowTimeUp &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
