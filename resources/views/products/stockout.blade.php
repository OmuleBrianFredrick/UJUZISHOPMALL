@extends('layouts.dashboard')
@section('title', 'Stock Out — ' . $product->name)
@section('content')
    <h1 class="page-heading">Stock Out: {{ $product->name }}</h1>
    <div class="card-panel" style="max-width:480px;">
        <p style="font-size:14px; color:var(--muted);">Current quantity: <span class="qty-mono">{{ $product->quantity }}</span></p>
        <form method="POST" action="{{ route('products.stockOut.submit', $product) }}">
            @csrf
            <div class="form-group">
                <label>Quantity Removed</label>
                <input type="number" name="quantity" min="1" max="{{ $product->quantity }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Note (optional)</label>
                <input type="text" name="note" placeholder="e.g. Sold, damaged, transferred">
            </div>
            <button type="submit" class="btn-solid">Remove from Stock</button>
            <a href="{{ route('products.index') }}" class="btn-outline-dark">Cancel</a>
        </form>
    </div>
@endsection
