<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => ['required','integer','min:1','max:5'],
            'body' => ['required','string','min:5','max:2000'],
        ]);

        $purchased = Order::where('user_id', $request->user()->id)
            ->where('status', 'delivered')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();
        abort_unless($purchased, 403, 'Only customers who received this product can review it.');

        if (Review::where('user_id', $request->user()->id)->where('product_id', $product->id)->exists()) {
            return back()->withErrors(['review' => 'You have already reviewed this product.']);
        }

        Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'body' => $validated['body'],
            'status' => 'pending',
            'verified_purchase' => true,
        ]);

        return back()->with('success', 'Thank you. Your verified review has been submitted for moderation.');
    }
}
