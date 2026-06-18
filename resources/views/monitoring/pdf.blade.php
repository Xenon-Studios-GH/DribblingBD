<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monitoring Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }
        h1 { font-size: 18px; margin-bottom: 4px; color: #111; }
        .subtitle { font-size: 11px; color: #666; margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #f3f4f6;
            text-align: left;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #d1d5db;
        }
        td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer {
            position: fixed;
            bottom: 10px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
        .page-number:before { content: "Page " counter(page); }
    </style>
</head>
<body>
    <h1>Monitoring Report — {{ $filterLabel }}</h1>
    <p class="subtitle">Generated: {{ now()->format('M d, Y H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Module</th>
                <th>Description</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
            <tr>
                <td>{{ $log->user?->name ?? 'Guest' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ ucfirst($log->module) }}</td>
                <td>{{ $log->description ?? '—' }}</td>
                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                    No logs found for the selected filters.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span class="page-number"></span> — {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
