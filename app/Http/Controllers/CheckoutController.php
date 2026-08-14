<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));
        abort_if($cart->isEmpty(), 404, 'Your cart is empty.');
        $subtotal = $cart->sum(fn ($item) => $item['price'] * $item['quantity']);
        $deliveryFee = 0;
        return view('storefront.checkout', compact('cart', 'subtotal', 'deliveryFee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = collect($request->session()->get('cart', []));
        if ($cart->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($cart, $validated, $request) {
            $subtotal = 0;
            $products = [];

            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (! $product || $product->quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$item['name']}. Please update your cart.",
                    ]);
                }
                $products[] = [$product, (int) $item['quantity']];
                $subtotal += $product->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'UJM-' . now()->format('YmdHis') . '-' . strtoupper(str()->random(5)),
                'status' => 'confirmed',
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'total' => $subtotal,
                ...$validated,
            ]);

            foreach ($products as [$product, $quantity]) {
                $lineTotal = $product->price * $quantity;
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('quantity', $quantity);
                $product->stockMovements()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'out',
                    'quantity' => $quantity,
                    'note' => 'Customer order ' . $order->order_number,
                ]);
            }

            return $order;
        });

        $request->session()->forget('cart');
        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully.');
    }
}
