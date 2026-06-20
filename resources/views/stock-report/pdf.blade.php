<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report - {{ ucfirst($period) }}</title>
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
        .summary { margin-top: 15px; padding: 10px; background: #f9fafb; border-radius: 4px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .section-title { font-size: 13px; font-weight: bold; margin-top: 20px; margin-bottom: 5px; padding-bottom: 3px; border-bottom: 2px solid #333; }
        .badge-in { background: #22C55E; color: white; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-out { background: #EF4444; color: white; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>Stock Report</h1>
    <p class="subtitle">Period: {{ ucfirst($period) }} @if($period === 'day') - {{ $date }} @elseif($period === 'custom' && request('date_from')) - {{ request('date_from') }} to {{ request('date_to') }} @elseif(in_array($period, ['week','month','year'])) - {{ $date }} @endif | Generated: {{ now()->format('d M Y h:i A') }}</p>

    <div class="summary">
        <div class="summary-row"><strong>Total Stock In:</strong> <span class="text-green">+{{ number_format($totals->total_in ?? 0) }}</span></div>
        <div class="summary-row"><strong>Total Stock Out:</strong> <span class="text-red">-{{ number_format($totals->total_out ?? 0) }}</span></div>
        @php $net = ($totals->total_in ?? 0) - ($totals->total_out ?? 0); @endphp
        <div class="summary-row"><strong>Net Change:</strong> <span class="{{ $net >= 0 ? 'text-green' : 'text-red' }}">{{ $net >= 0 ? '+' : '-' }}{{ number_format(abs($net)) }}</span></div>
        <div class="summary-row"><strong>Total Transactions:</strong> {{ $transactions->count() }}</div>
    </div>

    <div class="section-title">Transactions</div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Date & Time</th>
                <th>Product</th>
                <th>Size</th>
                <th class="text-right">Qty</th>
                <th>Note</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
            <tr>
                <td>
                    <span class="{{ $t->type->value === 'in' ? 'badge-in' : 'badge-out' }}">
                        {{ strtoupper($t->type->value) }}
                    </span>
                </td>
                <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                <td>{{ $t->product?->product_name ?? 'Deleted' }}</td>
                <td>{{ $t->size }}</td>
                <td class="text-right {{ $t->type->value === 'in' ? 'text-green' : 'text-red' }} font-weight:bold">
                    {{ $t->type->value === 'in' ? '+' : '-' }}{{ number_format(abs($t->quantity)) }}
                </td>
                <td>{{ $t->note ?: '—' }}</td>
                <td>{{ $t->user?->name ?? 'System' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:20px;color:#999;">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">DribblingBD - Stock Report</div>
</body>
</html>
