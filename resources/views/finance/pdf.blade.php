<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Report - {{ ucfirst($period) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        .subtitle { font-size: 11px; color: #666; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f3f4f6; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-green { color: #22C55E; }
        .text-red { color: #EF4444; }
        .section-title { font-size: 13px; font-weight: bold; margin-top: 20px; margin-bottom: 8px; }
        .summary { margin-top: 20px; padding: 12px; background: #f9fafb; border-radius: 4px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 11px; }
        .footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>Finance Report</h1>
    <p class="subtitle">Period: {{ ucfirst($period) }} | Generated: {{ now()->format('d M Y h:i A') }}</p>

    <div class="section-title">Income by Category</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incomeByCategory as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-right text-green">৳{{ number_format($item->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align:center;padding:15px;color:#999;">No income this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Expense by Category</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenseByCategory as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-right text-red">৳{{ number_format($item->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align:center;padding:15px;color:#999;">No expenses this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row"><strong>Total Income:</strong> <span class="text-green">৳{{ number_format($income, 2) }}</span></div>
        <div class="summary-row"><strong>Total Expense:</strong> <span class="text-red">৳{{ number_format($expense, 2) }}</span></div>
        <hr style="border: none; border-top: 1px solid #ddd; margin: 6px 0;">
        <div class="summary-row"><strong>Net Balance:</strong> <span class="{{ $balance >= 0 ? 'text-green' : 'text-red' }}">৳{{ number_format($balance, 2) }}</span></div>
    </div>

    <div class="footer">DribblingBD - Finance Report</div>
</body>
</html>
