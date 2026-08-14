<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdate;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SellerOrderController extends Controller
{
    private function seller(Request $request)
    {
        abort_unless(($request->user()->role ?? null) === 'seller', 403);
        return $request->user();
    }

    public function index(Request $request)
    {
        $seller = $this->seller($request);
        $orders = Order::whereHas('items', fn ($q) => $q->where('seller_id', $seller->id))
            ->with(['items' => fn ($q) => $q->where('seller_id', $seller->id)])
            ->latest()->paginate(15);
        return view('seller.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        $seller = $this->seller($request);
        abort_unless($order->items()->where('seller_id', $seller->id)->exists(), 403);
        $order->load(['items' => fn ($q) => $q->where('seller_id', $seller->id)]);
        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $seller = $this->seller($request);
        abort_unless($order->items()->where('seller_id', $seller->id)->exists(), 403);
        $validated = $request->validate(['status' => ['required', Rule::in(['processing', 'ready', 'shipped', 'delivered'])]]);
        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        DB::transaction(function () use ($order, $newStatus) {
            $order->update(['status' => $newStatus]);
        });

        if ($oldStatus !== $newStatus && filled($order->customer_email)) {
            Mail::to($order->customer_email)->send(new OrderStatusUpdate($order->fresh(), $newStatus));
        }

        return back()->with('success', 'Order status updated and customer notification sent.');
    }
}
