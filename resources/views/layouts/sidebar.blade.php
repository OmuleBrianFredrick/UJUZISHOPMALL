@php
    $lowStockCount = \App\Models\Product::whereColumn('quantity', '<=', 'reorder_level')->count();
@endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-warehouse"></i>
        <div>
            <div class="sidebar-brand-name">Ujuzi</div>
            <div class="sidebar-brand-tag">Shop Mall</div>
        </div>
    </div>

    <div class="sidebar-section-label">Workspace</div>
    <nav class="sidebar-nav">
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="{{ route('products.create') }}" class="{{ request()->routeIs('products.create') ? 'active' : '' }}">
            <i class="fa-solid fa-plus"></i> Add Product
        </a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Users
        </a>
    </nav>

    <div class="sidebar-section-label">Quick info</div>
    <div class="sidebar-quick-info">
        <div class="quick-info-label">Stock status</div>
        @if ($lowStockCount > 0)
            <div class="quick-info-value quick-info-warning">{{ $lowStockCount }} Low</div>
        @else
            <div class="quick-info-value quick-info-healthy">Healthy</div>
        @endif
    </div>
</aside>