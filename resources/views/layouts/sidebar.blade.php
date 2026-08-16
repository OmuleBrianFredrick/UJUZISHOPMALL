@php
    $lowStockCount = \Illuminate\Support\Facades\Cache::remember('low_stock_count', 60, function () {
        return \App\Models\Product::whereColumn('quantity', '<=', 'reorder_level')->count();
    });
    $cartCount = collect(session('cart', []))->sum('quantity');
@endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-store"></i>
        <div>
            <div class="sidebar-brand-name">Ujuzi</div>
            <div class="sidebar-brand-tag">Shop Mall</div>
        </div>
    </div>

    <div class="sidebar-section-label">Shopping</div>
    <nav class="sidebar-nav">
        <a href="{{ route('storefront.index') }}" class="{{ request()->routeIs('storefront.index') ? 'active' : '' }}">
            <i class="fa-solid fa-bag-shopping"></i> Shop
        </a>
        <a href="{{ route('storefront.cart') }}" class="{{ request()->routeIs('storefront.cart*') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping"></i> Cart <span class="sidebar-count">{{ $cartCount }}</span>
        </a>
    </nav>

    <div class="sidebar-section-label">Management</div>
    <nav class="sidebar-nav">
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') || request()->routeIs('products.analytics') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Inventory
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