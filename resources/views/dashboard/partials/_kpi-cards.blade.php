<div class="grid grid-cols-2 md:grid-cols-4 gap-2.5" id="kpi-cards-grid">
    <x-dash-card :href="admin_route('orders.index')" icon="shopping-bag" color="#F59E0B" label="Total Orders" :value="$totalOrders" />
    <x-dash-card icon="shopping-cart" color="#3B82F6" label="Orders Today" :value="$ordersToday" />
    <x-dash-card icon="chart-line" color="#22C55E" label="Revenue" value="৳{{ number_format($totalRevenue, 0) }}" />
    <x-dash-card icon="wallet" color="#A855F7" label="Pending" value="৳{{ number_format($totalPendingAmount, 0) }}" />
</div>
