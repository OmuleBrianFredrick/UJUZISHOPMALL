<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const STATUSES = ['confirmed', 'processing', 'ready', 'shipped', 'delivered'];

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);
        return view('storefront.orders', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items.product');
        $statusIndex = array_search($order->status, self::STATUSES, true);
        $timeline = collect(self::STATUSES)->map(fn ($status, $index) => [
            'status' => $status,
            'label' => ucfirst($status),
            'complete' => $statusIndex !== false && $index <= $statusIndex,
            'current' => $status === $order->status,
        ]);
        return view('storefront.order-show', compact('order', 'timeline'));
    }
}
