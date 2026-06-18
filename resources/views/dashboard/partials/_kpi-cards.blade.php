<div class="grid grid-cols-2 md:grid-cols-4 gap-2.5" id="kpi-cards-grid">
    <x-dash-card :href="admin_route('orders.index')" icon="shopping-bag" color="#F59E0B" label="Total Orders" :value="$totalOrders" />
    <x-dash-card icon="shopping-cart" color="#3B82F6" label="Orders Today" :value="$ordersToday" />
    <x-dash-card icon="chart-line" color="#22C55E" label="Revenue" value="৳{{ number_format($totalRevenue, 0) }}" />
    <x-dash-card icon="wallet" color="#A855F7" label="Pending" value="৳{{ number_format($totalPendingAmount, 0) }}" />
    <x-dash-card :href="admin_route('stock.management')" icon="cubes" color="#3B82F6" label="Total Stock" :value="number_format($totalStock)" />
    <x-dash-card icon="taka" color="#22C55E" label="Stock Value" value="৳{{ number_format($stockValue, 0) }}" />
    <x-dash-card :href="admin_route('stock.management')" icon="exclamation-triangle" color="#EF4444" label="Low Stock" :value="$lowStockProducts" />
    <x-dash-card :href="admin_route('workers.index')" icon="users" color="#A855F7" label="Workers" :value="$totalWorkers" />
</div>
