@extends('layouts.dashboard')
@section('title', 'Products — Ujuzi Shop Mall')
@section('content')

    <div class="stat-grid">
        <div class="stat-card stat-card-terracotta">
            <div class="stat-card-value">{{ $totalProducts }}</div>
            <div class="stat-card-label"><i class="fa-solid fa-boxes-stacked"></i> Total products</div>
        </div>
        <div class="stat-card stat-card-amber">
            <div class="stat-card-value">{{ $totalStock }}</div>
            <div class="stat-card-label"><i class="fa-solid fa-warehouse"></i> Total stock</div>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin:30px 0 26px;">
        <h1 class="page-heading" style="margin:0;">Product Inventory</h1>
        <a href="{{ route('products.create') }}" class="btn-solid"><i class="fa-solid fa-plus"></i> Add Product</a>
    </div>

    <table class="inv-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td style="display:flex; align-items:center; gap:12px;">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="product-thumb" alt="{{ $product->name }}">
                        @else
                            <div class="product-thumb"></div>
                        @endif
                        {{ $product->name }}
                    </td>
                    <td class="qty-mono">{{ $product->sku }}</td>
                    <td>{{ $product->category ?? '—' }}</td>
                    <td class="qty-mono price-value" data-ugx="{{ $product->price }}">UGX {{ number_format($product->price, 0) }}</td>
                    <td class="qty-mono">{{ $product->quantity }}</td>
                    <td>
                        @if ($product->isLowStock())
                            <span class="badge-low"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>
                        @else
                            <span class="badge-ok"><i class="fa-solid fa-circle-check"></i> In Stock</span>
                        @endif
                    </td>
                    <td class="row-actions">
                        <a href="{{ route('products.stockIn', $product) }}"><i class="fa-solid fa-arrow-down"></i> Stock In</a>
                        <a href="{{ route('products.stockOut', $product) }}"><i class="fa-solid fa-arrow-up"></i> Stock Out</a>
                        <a href="{{ route('products.edit', $product) }}"><i class="fa-solid fa-pen"></i> Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline-form" onsubmit="return confirm('Delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"><i class="fa-solid fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:var(--warm-gray); padding:30px;">No products yet. Add your first product to get started.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="card-panel chart-card">
        <h3 class="chart-title">Stock in vs stock out</h3>
        <canvas id="stockMovementChart" height="90"></canvas>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('stockMovementChart'), {
            type: 'bar',
            data: {
                labels: ['Stock in', 'Stock out'],
                datasets: [{
                    label: 'Total units',
                    data: [{{ $stockInTotal }}, {{ $stockOutTotal }}],
                    backgroundColor: ['#3D7A4F', '#B23A34']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>
@endpush
