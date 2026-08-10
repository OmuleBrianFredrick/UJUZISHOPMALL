@extends('layouts.dashboard')
@section('title', 'Add Product — Ujuzi Shop Mall')
@section('content')
    <h1 class="page-heading">Add New Product</h1>
    <div class="card-panel" style="max-width:600px;">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>SKU (Stock Code)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="{{ old('category') }}">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required>
                </div>
                <div class="form-group">
                    <label>Opening Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 0) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Reorder Level (low stock alert threshold)</label>
                <input type="number" name="reorder_level" value="{{ old('reorder_level', 5) }}" required>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn-solid">Save Product</button>
            <a href="{{ route('products.index') }}" class="btn-outline-dark">Cancel</a>
        </form>
    </div>
@endsection
