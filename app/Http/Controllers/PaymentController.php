<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order);
        }

        $payment = $order->payments()->whereIn('status', ['pending', 'processing'])->latest()->first();

        return view('storefront.payment', compact('order', 'payment'));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'method' => ['required', 'in:mtn_momo,airtel_money'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order);
        }

        $payment = DB::transaction(function () use ($order, $validated) {
            $existing = $order->payments()
                ->whereIn('status', ['pending', 'processing'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            return $order->payments()->create([
                'provider' => $validated['method'] === 'mtn_momo' ? 'mtn' : 'airtel',
                'method' => $validated['method'],
                'status' => 'pending',
                'merchant_reference' => 'UJM-PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                'amount' => $order->total,
                'currency' => 'UGX',
                'payer_phone' => $validated['phone'],
            ]);
        });

        // Provider initiation is intentionally separated from transaction creation.
        // Credentials/API calls will be added through concrete gateway adapters in the next payment milestone.
        $payment->update(['status' => 'processing']);

        return redirect()->route('payments.show', [$order, $payment])
            ->with('success', 'Payment request created. Complete the mobile-money prompt to continue.');
    }

    public function show(Request $request, Order $order, Payment $payment)
    {
        abort_unless($order->user_id === $request->user()->id && $payment->order_id === $order->id, 403);

        return view('storefront.payment-status', compact('order', 'payment'));
    }
}
