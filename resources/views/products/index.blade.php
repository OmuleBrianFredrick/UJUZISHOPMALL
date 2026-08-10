@extends('layouts.dashboard')
@section('title', 'Products — Ujuzi Shop Mall')
@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:26px;">
        <h1 class="page-heading" style="margin:0;">Product Inventory</h1>
        <a href="{{ route('products.create') }}" class="btn-solid">+ Add Product</a>
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
                    <td class="qty-mono">{{ number_format($product->price, 2) }}</td>
                    <td class="qty-mono">{{ $product->quantity }}</td>
                    <td>
                        @if ($product->isLowStock())
                            <span class="badge-low">Low Stock</span>
                        @else
                            <span class="badge-ok">In Stock</span>
                        @endif
                    </td>
                    <td class="row-actions">
                        <a href="{{ route('products.stockIn', $product) }}">Stock In</a>
                        <a href="{{ route('products.stockOut', $product) }}">Stock Out</a>
                        <a href="{{ route('products.edit', $product) }}">Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline-form" onsubmit="return confirm('Delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:var(--muted); padding:30px;">No products yet. Add your first product to get started.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
