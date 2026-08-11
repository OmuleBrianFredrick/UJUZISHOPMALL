@extends('layouts.dashboard')
@section('title', 'Inventory Dashboard — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero">
    <div>
        <div class="dashboard-kicker">Inventory intelligence</div>
        <h1 class="dashboard-title">Good day, {{ Auth::user()->name }} 👋</h1>
        <p class="dashboard-subtitle">Monitor products, stock movement and inventory health from one place.</p>
    </div>
    <div class="dashboard-actions">
        <a href="{{ route('products.create') }}" class="btn-solid"><i class="fa-solid fa-plus"></i> Add Product</a>
        <a href="{{ route('users.index') }}" class="btn-outline-dark"><i class="fa-solid fa-users"></i> Manage Users</a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-stat stat-blue">
        <div class="stat-top"><span>Total Products</span><span class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></span></div>
        <div class="stat-value" id="totalProducts">{{ number_format($totalProducts) }}</div>
        <div class="stat-meta">Active products in catalogue</div>
    </div>
    <div class="dashboard-stat stat-green">
        <div class="stat-top"><span>Current Stock</span><span class="stat-icon"><i class="fa-solid fa-warehouse"></i></span></div>
        <div class="stat-value" id="currentStock">{{ number_format($totalStock) }}</div>
        <div class="stat-meta">Units currently available</div>
    </div>
    <div class="dashboard-stat stat-orange">
        <div class="stat-top"><span>Inventory Value</span><span class="stat-icon"><i class="fa-solid fa-coins"></i></span></div>
        <div class="stat-value inventory-value" data-ugx="{{ $inventoryValue }}">UGX {{ number_format($inventoryValue, 0) }}</div>
        <div class="stat-meta">Estimated value of current stock</div>
    </div>
    <div class="dashboard-stat stat-purple">
        <div class="stat-top"><span>Low Stock</span><span class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></span></div>
        <div class="stat-value" id="lowStockCount">{{ number_format($lowStockProducts->count()) }}</div>
        <div class="stat-meta">Products at or below reorder level</div>
    </div>
</div>

<div class="dashboard-main-grid">
    <section class="dashboard-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Stock movement</h2>
                <p class="panel-note">Daily stock-in versus stock-out activity</p>
            </div>
            <span class="live-pill"><span class="live-dot"></span> Live sync</span>
        </div>
        <div class="chart-wrap"><canvas id="stockMovementChart"></canvas></div>
    </section>

    <section class="dashboard-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Low stock alerts</h2>
                <p class="panel-note">Prioritise products needing replenishment</p>
            </div>
            <span class="status-badge status-low">{{ $lowStockProducts->count() }} alerts</span>
        </div>
        <div class="low-list">
            @forelse($lowStockProducts as $product)
                <div class="low-item">
                    @if($product->image)
                        <img class="low-thumb" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div class="low-thumb"></div>
                    @endif
                    <div class="low-name">{{ $product->name }}<small>Reorder at {{ number_format($product->reorder_level) }}</small></div>
                    <div class="low-count">{{ number_format($product->quantity) }} left</div>
                </div>
            @empty
                <div style="padding:35px 10px;text-align:center;color:var(--dash-muted);">🎉 All products are above their reorder levels.</div>
            @endforelse
        </div>
    </section>
</div>

<section class="dashboard-panel dashboard-table-panel">
    <div class="table-toolbar">
        <div>
            <h2 class="panel-title">Product inventory</h2>
            <p class="panel-note">Edit products or record stock movement directly from the table.</p>
        </div>
        <input class="table-search" id="productSearch" type="search" placeholder="Search products, SKU or category…">
    </div>
    <table class="modern-table" id="productsTable">
        <thead>
            <tr><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            @php $stockPercent = $product->reorder_level > 0 ? min(100, ($product->quantity / max($product->reorder_level * 3, 1)) * 100) : ($product->quantity > 0 ? 100 : 0); @endphp
            <tr data-search="{{ strtolower($product->name.' '.$product->sku.' '.($product->category ?? '')) }}">
                <td>
                    <div class="product-cell">
                        @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">@else<div class="product-placeholder"><i class="fa-solid fa-box"></i></div>@endif
                        <div>{{ $product->name }}<span class="product-sub">{{ $product->description ? Str::limit($product->description, 34) : 'No description' }}</span></div>
                    </div>
                </td>
                <td class="qty-mono">{{ $product->sku }}</td>
                <td>{{ $product->category ?? 'Uncategorised' }}</td>
                <td class="qty-mono price-value" data-ugx="{{ $product->price }}">UGX {{ number_format($product->price, 0) }}</td>
                <td>
                    <div class="stock-bar"><div class="stock-bar-line"><div class="stock-bar-fill" style="width:{{ $stockPercent }}%;background:{{ $product->isLowStock() ? 'var(--dash-red)' : 'var(--dash-green)' }}"></div></div><strong>{{ number_format($product->quantity) }}</strong></div>
                </td>
                <td>
                    @if($product->isLowStock())<span class="status-badge status-low"><i class="fa-solid fa-circle-exclamation"></i> Low</span>
                    @else<span class="status-badge status-ok"><i class="fa-solid fa-circle-check"></i> Healthy</span>@endif
                </td>
                <td>
                    <div class="action-links">
                        <a href="{{ route('products.edit',$product) }}">Edit</a>
                        <a href="{{ route('products.stockIn',$product) }}">+ In</a>
                        <a href="{{ route('products.stockOut',$product) }}">− Out</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--dash-muted);">No products yet. Add your first product.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>

<div class="dashboard-main-grid">
    <section class="dashboard-panel">
        <div class="panel-head"><div><h2 class="panel-title">Recent stock activity</h2><p class="panel-note">Latest inventory movements</p></div></div>
        <div class="movement-list">
            @forelse($recentMovements as $movement)
                <div class="movement {{ $movement->type === 'in' ? 'movement-in' : 'movement-out' }}">
                    <div class="movement-icon"><i class="fa-solid fa-arrow-{{ $movement->type === 'in' ? 'down' : 'up' }}"></i></div>
                    <div class="movement-main"><strong>{{ $movement->product->name }}</strong><span>{{ ucfirst($movement->type) }} · {{ $movement->user->name ?? 'System' }} · {{ $movement->created_at->diffForHumans() }}</span></div>
                    <div class="movement-qty">{{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity) }}</div>
                </div>
            @empty
                <p style="color:var(--dash-muted);font-size:13px;">No stock movement recorded yet.</p>
            @endforelse
        </div>
    </section>

    <section class="dashboard-panel">
        <div class="panel-head"><div><h2 class="panel-title">Top stock holdings</h2><p class="panel-note">Products with the highest unit count</p></div></div>
        <div class="movement-list">
            @forelse($topProducts as $product)
                <div class="movement"><div class="movement-icon" style="background:#eff6ff;color:var(--dash-blue);"><i class="fa-solid fa-box"></i></div><div class="movement-main"><strong>{{ $product->name }}</strong><span>{{ $product->category ?? 'Uncategorised' }}</span></div><div class="movement-qty" style="color:var(--dash-ink);">{{ number_format($product->quantity) }}</div></div>
            @empty
                <p style="color:var(--dash-muted);font-size:13px;">No products available.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const ctx = document.getElementById('stockMovementChart');
    if (!ctx || typeof Chart === 'undefined') return;

    const chart = new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [
            { label: 'Stock In', data: [], borderWidth: 2, tension: .35, fill: false, pointRadius: 3 },
            { label: 'Stock Out', data: [], borderWidth: 2, tension: .35, fill: false, pointRadius: 3 }
        ]},
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: { beginAtZero: true, grid: { color: '#eef1f5' } }, x: { grid: { display: false } } }
        }
    });

    async function refreshDashboard() {
        try {
            const response = await fetch('{{ route('products.analytics') }}?days=14', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
            if (!response.ok) return;
            const data = await response.json();
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.stock_in;
            chart.data.datasets[1].data = data.stock_out;
            chart.update('none');
            document.getElementById('currentStock').textContent = Number(data.totals.current_stock).toLocaleString();
        } catch (error) { console.warn('Live inventory sync unavailable:', error); }
    }
    refreshDashboard();
    setInterval(refreshDashboard, 10000);

    const search = document.getElementById('productSearch');
    if (search) search.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();
        document.querySelectorAll('#productsTable tbody tr[data-search]').forEach(row => row.style.display = row.dataset.search.includes(term) ? '' : 'none');
    });
})();
</script>
@endpush
