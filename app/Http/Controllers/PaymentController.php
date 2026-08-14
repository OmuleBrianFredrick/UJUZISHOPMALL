<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Financial\CommissionService;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        if ($order->payment_status === 'paid') return redirect()->route('orders.show', $order);
        $payment = $order->payments()->whereIn('status', ['pending', 'processing'])->latest()->first();
        return view('storefront.payment', compact('order', 'payment'));
    }

    public function store(Request $request, Order $order, PaymentManager $manager)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $validated = $request->validate(['method' => ['required', 'in:mtn_momo,airtel_money'], 'phone' => ['required', 'string', 'max:30']]);
        if ($order->payment_status === 'paid') return redirect()->route('orders.show', $order);
        $payment = DB::transaction(function () use ($order, $validated) {
            $existing = $order->payments()->whereIn('status', ['pending', 'processing'])->lockForUpdate()->first();
            if ($existing) return $existing;
            return $order->payments()->create([
                'provider' => $validated['method'] === 'mtn_momo' ? 'mtn' : 'airtel', 'method' => $validated['method'], 'status' => 'pending',
                'merchant_reference' => 'UJM-PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)), 'amount' => $order->total,
                'currency' => 'UGX', 'payer_phone' => $validated['phone'],
            ]);
        });
        if ($payment->status === 'pending') $payment = $manager->initiate($order, $payment, $validated['phone']);
        return redirect()->route('payments.show', [$order, $payment])->with('success', 'Payment request sent. Complete the mobile-money prompt to continue.');
    }

    public function callbackMtn(Request $request, PaymentManager $manager, CommissionService $commissionService)
    {
        return $this->processProviderCallback($request, $manager, $commissionService, 'mtn_momo');
    }

    public function callbackAirtel(Request $request, PaymentManager $manager, CommissionService $commissionService)
    {
        return $this->processProviderCallback($request, $manager, $commissionService, 'airtel_money');
    }

    private function processProviderCallback(Request $request, PaymentManager $manager, CommissionService $commissionService, string $method)
    {
        $payload = $request->json()->all();
        $normalized = $manager->gateway($method)->handleCallback($payload);
        $providerReference = $normalized['provider_reference'] ?? null;
        if (! $providerReference) return response()->json(['message' => 'Missing payment reference'], 422);
        $payment = Payment::where('provider_reference', $providerReference)->orWhere('merchant_reference', $providerReference)->first();
        if (! $payment) return response()->json(['message' => 'Payment not found'], 404);
        if ($payment->method !== $method) return response()->json(['message' => 'Payment provider mismatch'], 409);
        if (in_array($payment->status, ['successful', 'failed'], true)) return response()->json(['ok' => true]);
        DB::transaction(function () use ($payment, $normalized, $commissionService) {
            $payment->update(['status' => $normalized['status'], 'failure_reason' => $normalized['failure_reason'] ?? null, 'provider_response' => $normalized['provider_response'] ?? null, 'paid_at' => $normalized['status'] === 'successful' ? now() : null]);
            if ($normalized['status'] === 'successful') {
                $order = $payment->order()->lockForUpdate()->first();
                $order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
                $commissionService->settlePaidOrder($order, $payment);
            } elseif ($normalized['status'] === 'failed') {
                $payment->order()->update(['payment_status' => 'failed']);
            }
        });
        return response()->json(['ok' => true]);
    }

    public function show(Request $request, Order $order, Payment $payment)
    {
        abort_unless($order->user_id === $request->user()->id && $payment->order_id === $order->id, 403);
        return view('storefront.payment-status', compact('order', 'payment'));
    }
}
