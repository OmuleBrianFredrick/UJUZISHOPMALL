<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        // Paginate product listing to avoid loading the entire table into memory
        $products = Product::orderBy('name')->paginate(50);

        // Compute aggregates with database queries to keep memory usage low
        $totalProducts = Product::count();
        $totalStock = Product::sum('quantity');
        $stockInTotal = StockMovement::where('type', 'in')->sum('quantity');
        $stockOutTotal = StockMovement::where('type', 'out')->sum('quantity');
        $inventoryValue = (float) Product::selectRaw('COALESCE(SUM(price * quantity), 0) as value')->value('value');

        // Low stock list and top products use targeted queries. Limit low-stock list to avoid large memory usage.
        $lowStockProducts = Product::lowStock()->orderBy('quantity')->limit(100)->get();
        $recentMovements = StockMovement::with(['product', 'user'])->latest()->limit(8)->get();
        $topProducts = Product::orderByDesc('quantity')->limit(5)->get();

        return view('products.index', compact(
            'products',
            'totalProducts',
            'totalStock',
            'stockInTotal',
            'stockOutTotal',
            'inventoryValue',
            'lowStockProducts',
            'recentMovements',
            'topProducts'
        ));
    }

    /**
     * JSON endpoint used by the dashboard for live stock movement charts.
     * It deliberately reads the database on every request so stock-in/out
     * changes are reflected without rebuilding the page.
     */
    public function analytics(Request $request)
    {
        $days = min(max((int) $request->integer('days', 14), 7), 90);
        $start = Carbon::today()->subDays($days - 1);

        $movements = StockMovement::where('created_at', '>=', $start)
            ->selectRaw("DATE(created_at) as movement_date, type, SUM(quantity) as total")
            ->groupBy('movement_date', 'type')
            ->orderBy('movement_date')
            ->get();

        $labels = [];
        $stockIn = [];
        $stockOut = [];

        for ($date = $start->copy(); $date->lte(Carbon::today()); $date->addDay()) {
            $key = $date->toDateString();
            $labels[] = $date->format('d M');
            $stockIn[] = (int) ($movements->first(fn ($row) => $row->movement_date === $key && $row->type === 'in')->total ?? 0);
            $stockOut[] = (int) ($movements->first(fn ($row) => $row->movement_date === $key && $row->type === 'out')->total ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'totals' => [
                'stock_in' => (int) StockMovement::where('type', 'in')->sum('quantity'),
                'stock_out' => (int) StockMovement::where('type', 'out')->sum('quantity'),
                'current_stock' => (int) Product::sum('quantity'),
            ],
        ]);
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
