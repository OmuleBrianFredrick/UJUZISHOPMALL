<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\LoyaltyTransaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function store(Request $request, NotificationService $notifications)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'promotion_code' => ['nullable', 'string', 'max:50'],
            'loyalty_points' => ['nullable', 'integer', 'min:0'],
        ]);

        $cart = collect($request->session()->get('cart', []));
        if ($cart->isEmpty()) throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);

        $order = DB::transaction(function () use ($cart, $validated, $request) {
            $subtotal = 0;
            $products = [];
            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product || $product->quantity < $item['quantity']) {
                    throw ValidationException::withMessages(['cart' => "Insufficient stock for {$item['name']}. Please update your cart."]);
                }
                $products[] = [$product, (int) $item['quantity']];
                $subtotal += $product->price * $item['quantity'];
            }

            $discount = 0.0;
            $promotion = null;
            if (!empty($validated['promotion_code'])) {
                $promotion = Promotion::where('code', strtoupper(trim($validated['promotion_code'])))->lockForUpdate()->first();
                if (!$promotion || !$promotion->isValidFor($subtotal)) {
                    throw ValidationException::withMessages(['promotion_code' => 'The promotion code is invalid, expired, inactive or unavailable.']);
                }
                $discount = $promotion->discountFor($subtotal);
                if ($discount <= 0) throw ValidationException::withMessages(['promotion_code' => 'The promotion code does not provide a valid discount.']);
                $promotion->increment('usage_count');
            }

            $pointsUsed = (int) ($validated['loyalty_points'] ?? 0);
            $pointsDiscount = 0.0;
            if ($pointsUsed > 0) {
                $balance = (int) LoyaltyTransaction::where('user_id', $request->user()->id)->lockForUpdate()->sum('points');
                if ($pointsUsed > $balance) throw ValidationException::withMessages(['loyalty_points' => 'You do not have enough loyalty points.']);
                $pointsDiscount = min($subtotal - $discount, $pointsUsed * 10);
                if ($pointsDiscount <= 0) throw ValidationException::withMessages(['loyalty_points' => 'The selected loyalty points cannot be applied.']);
            }

            $total = max(0, round($subtotal - $discount - $pointsDiscount, 2));
            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'UJM-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5)),
                'status' => 'confirmed', 'payment_status' => 'unpaid',
                'subtotal' => $subtotal, 'delivery_fee' => 0, 'total' => $total,
                'discount' => $discount + $pointsDiscount,
                'promotion_code' => $promotion?->code,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'delivery_address' => $validated['delivery_address'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($products as [$product, $quantity]) {
                $lineTotal = $product->price * $quantity;
                $order->items()->create([
                    'product_id' => $product->id, 'seller_id' => $product->seller_id,
                    'product_name' => $product->name, 'sku' => $product->sku,
                    'quantity' => $quantity, 'unit_price' => $product->price, 'line_total' => $lineTotal,
                ]);
                $product->decrement('quantity', $quantity);
                $product->stockMovements()->create([
                    'user_id' => $request->user()->id, 'type' => 'out', 'quantity' => $quantity,
                    'note' => 'Customer order ' . $order->order_number,
                ]);
            }

            if ($pointsUsed > 0) {
                LoyaltyTransaction::create([
                    'user_id' => $request->user()->id,
                    'order_id' => $order->id,
                    'type' => 'redemption',
                    'points' => -$pointsUsed,
                    'reference' => 'LOY-' . Str::upper(Str::random(18)),
                    'description' => 'Loyalty points redeemed on ' . $order->order_number,
                ]);
            }
            return $order;
        });

        $request->session()->forget('cart');
        $notifications->order($order, 'order_confirmation');
        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully. A confirmation has been queued to your email.');
    }
}
