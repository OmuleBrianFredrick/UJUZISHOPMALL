<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        $totalProducts = $products->count();
        $totalStock = $products->sum('quantity');
        $stockInTotal = StockMovement::where('type', 'in')->sum('quantity');
        $stockOutTotal = StockMovement::where('type', 'out')->sum('quantity');

        return view('products.index', compact('products', 'totalProducts', 'totalStock', 'stockInTotal', 'stockOutTotal'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    public function stockIn(Product $product)
    {
        return view('products.stock', compact('product'));
    }

    public function processStockIn(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $product->increment('quantity', $validated['quantity']);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => 'in',
            'quantity' => $validated['quantity'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('products.index')->with('success', 'Stock added successfully.');
    }

    public function stockOut(Product $product)
    {
        return view('products.stockout', compact('product'));
    }

    public function processStockOut(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
            'note' => 'nullable|string|max:255',
        ]);

        $product->decrement('quantity', $validated['quantity']);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => 'out',
            'quantity' => $validated['quantity'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('products.index')->with('success', 'Stock removed successfully.');
    }
}
