<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SellerProductController extends Controller
{
    private function seller(Request $request)
    {
        abort_unless(in_array($request->user()->role ?? null, ['seller', 'admin'], true), 403);
        return $request->user();
    }

    public function index(Request $request)
    {
        $seller = $this->seller($request);
        $products = Product::where('seller_id', $seller->id)->latest()->paginate(15);
        return view('seller.products.index', compact('products'));
    }

    public function create(Request $request)
    {
        $this->seller($request);
        return view('seller.products.create');
    }

    public function store(Request $request)
    {
        $seller = $this->seller($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku'],
            'category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:500'],
        ]);
        $data['seller_id'] = $seller->id;
        Product::create($data);
        return redirect()->route('seller.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Request $request, Product $product)
    {
        $seller = $this->seller($request);
        abort_unless($product->seller_id === $seller->id || ($seller->role ?? null) === 'admin', 403);
        return view('seller.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $seller = $this->seller($request);
        abort_unless($product->seller_id === $seller->id || ($seller->role ?? null) === 'admin', 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku,' . $product->id],
            'category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:500'],
        ]);
        $product->update($data);
        return redirect()->route('seller.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        $seller = $this->seller($request);
        abort_unless($product->seller_id === $seller->id || ($seller->role ?? null) === 'admin', 403);
        $product->delete();
        return redirect()->route('seller.products.index')->with('success', 'Product removed from your catalogue.');
    }
}
