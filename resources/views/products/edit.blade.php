@extends('layouts.dashboard')
@section('title', 'Edit Product — Ujuzi Shop Mall')
@section('content')
    <h1 class="page-heading">Edit Product</h1>
    <div class="card-panel" style="max-width:600px;">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>SKU (Stock Code)</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="{{ old('category', $product->category) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required>
                </div>
                <div class="form-group">
                    <label>Reorder Level</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" required>
                </div>
            </div>
            <p style="font-size:13px; color:var(--muted);">Current quantity: <span class="qty-mono">{{ $product->quantity }}</span> — use Stock In / Stock Out from the product list to change quantity.</p>
            <div class="form-group">
                <label>Replace Product Image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn-solid">Update Product</button>
            <a href="{{ route('products.index') }}" class="btn-outline-dark">Cancel</a>
        </form>
    </div>
@endsection
