<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report - {{ ucfirst($period) }}</title>
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
        .text-amber { color: #F59E0B; }
        .summary { margin-top: 15px; padding: 10px; background: #f9fafb; border-radius: 4px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .section-title { font-size: 13px; font-weight: bold; margin-top: 20px; margin-bottom: 5px; padding-bottom: 3px; border-bottom: 2px solid #333; }
        .badge { color: white; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; display: inline-block; }
        .badge-delivered { background: #22C55E; }
        .badge-pending { background: #F59E0B; }
        .badge-cancelled { background: #EF4444; }
        .badge-default { background: #3B82F6; }
        .footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>Orders Report</h1>
    <p class="subtitle">Period: {{ ucfirst($period) }} - {{ $date }} | Generated: {{ now()->format('d M Y h:i A') }}</p>

    <div class="summary">
        <div class="summary-row"><strong>Total Orders:</strong> <span>{{ number_format($totals->total_orders ?? 0) }}</span></div>
        <div class="summary-row"><strong>Total Revenue:</strong> <span class="text-green">{{ number_format($totals->total_revenue ?? 0, 2) }} Tk</span></div>
    </div>

    <div class="section-title">Orders</div>
    <table>
        <thead>
            <tr>
                <th>Order No</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Date</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $o)
            <tr>
                <td>{{ $o->order_no }}</td>
                <td>{{ $o->customer_name }}</td>
                <td>{{ $o->phone }}</td>
                <td>{{ $o->created_at->format('d M Y') }}</td>
                <td class="text-right">{{ number_format($o->total_amount, 2) }}</td>
                <td>
                    <span class="badge badge-{{ in_array($o->status, ['delivered','pending','cancelled']) ? $o->status : 'default' }}">
                        {{ ucfirst($o->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#999;">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">DribblingBD - Orders Report</div>
</body>
</html>
