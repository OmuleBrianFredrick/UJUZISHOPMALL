<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('quantity', '>', 0);

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = trim((string) $request->query('category'))) {
            $query->where('category', $category);
        }

        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Product::whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('storefront.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if($product->quantity < 1, 404);

        $related = Product::where('id', '!=', $product->id)
            ->where('quantity', '>', 0)
            ->when($product->category, fn ($query) => $query->where('category', $product->category))
            ->latest()
            ->limit(4)
            ->get();

        // Eager-load approved reviews with their user to avoid running multiple queries in the view
        $product->load(['approvedReviews' => fn ($q) => $q->with('user')->latest()]);

        // Prepare a dedicated variable for the approved reviews collection and compute rating/count in-memory
        $approvedReviews = $product->approvedReviews;
        $rating = (float) ($approvedReviews->avg('rating') ?? 0);
        $rating = round($rating, 1);
        $reviewsCount = $approvedReviews->count();

        return view('storefront.show', compact('product', 'related', 'rating', 'reviewsCount', 'approvedReviews'));
    }

    public function addToCart(Request $request, Product $product)
    {
        abort_if($product->quantity < 1, 404);

        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart = $request->session()->get('cart', []);
        $id = (string) $product->id;
        $current = (int) ($cart[$id]['quantity'] ?? 0);
        $cart[$id] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => $product->image,
            'quantity' => min($product->quantity, $current + $quantity),
        ];

        $request->session()->put('cart', $cart);

        return redirect()->route('storefront.cart')->with('success', 'Product added to your cart.');
    }

    public function cart(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));
        $total = $cart->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('storefront.cart', compact('cart', 'total'));
    }

    public function updateCart(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        foreach ($request->input('quantity', []) as $id => $quantity) {
            if (! isset($cart[$id])) {
                continue;
            }

            $product = Product::find($cart[$id]['product_id']);
            if (! $product || $product->quantity < 1) {
                unset($cart[$id]);
                continue;
            }

            $cart[$id]['quantity'] = min($product->quantity, max(1, (int) $quantity));
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('storefront.cart')->with('success', 'Cart updated.');
    }

    public function removeFromCart(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[(string) $product->id]);
        $request->session()->put('cart', $cart);

        return redirect()->route('storefront.cart')->with('success', 'Product removed from your cart.');
    }
}
