<?php

namespace App\Services\Financial;

use App\Models\FinancialLedger;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PayoutService
{
    public function request(User $seller, float $amount, string $method, string $phone): Payout
    {
        $minimum = (float) config('commerce.minimum_payout', env('MINIMUM_SELLER_PAYOUT', 10000));
        if ($amount < $minimum) throw new RuntimeException('Payout amount is below the minimum payout threshold.');
        if (! in_array($method, ['mtn_momo', 'airtel_money'], true)) throw new RuntimeException('Unsupported payout method.');
        return DB::transaction(function () use ($seller, $amount, $method, $phone) {
            User::whereKey($seller->id)->lockForUpdate()->firstOrFail();
            $balance = $this->balance($seller->id);
            $reserved = (float) Payout::where('seller_id', $seller->id)->whereIn('status', ['pending', 'approved', 'processing'])->sum('amount');
            if ($amount > round($balance - $reserved, 2)) throw new RuntimeException('Insufficient available balance for this payout.');
            return Payout::create(['seller_id' => $seller->id, 'provider' => $method === 'mtn_momo' ? 'mtn' : 'airtel', 'method' => $method, 'phone' => $phone, 'amount' => $amount, 'currency' => 'UGX', 'status' => 'pending', 'merchant_reference' => 'UJM-PAYOUT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(8))]);
        });
    }

    public function approve(Payout $payout, User $admin): Payout
    {
        return DB::transaction(function () use ($payout, $admin) {
            $seller = User::whereKey($payout->seller_id)->lockForUpdate()->firstOrFail();
            $payout = Payout::whereKey($payout->id)->lockForUpdate()->firstOrFail();
            if ($payout->status !== 'pending') throw new RuntimeException('Only pending payouts can be approved.');
            $reservedOthers = (float) Payout::where('seller_id', $seller->id)->whereIn('status', ['pending', 'approved', 'processing'])->where('id', '!=', $payout->id)->sum('amount');
            if ($payout->amount > round($this->balance($seller->id) - $reservedOthers, 2)) throw new RuntimeException('Seller balance changed and no longer covers this payout.');
            $payout->update(['status' => 'approved', 'approved_by' => $admin->id, 'approved_at' => now()]);
            $this->ledger($payout, 'debit', 'payout_reserved', 'Seller payout reserved for processing.');
            return $payout->fresh();
        });
    }

    public function markPaid(Payout $payout, string $providerReference): Payout
    {
        return DB::transaction(function () use ($payout, $providerReference) {
            $payout = Payout::whereKey($payout->id)->lockForUpdate()->firstOrFail();
            if ($payout->status === 'paid') return $payout;
            if (! in_array($payout->status, ['approved', 'processing'], true)) throw new RuntimeException('Only approved or processing payouts can be marked paid.');
            $payout->update(['status' => 'paid', 'provider_reference' => $providerReference, 'processed_at' => $payout->processed_at ?: now(), 'paid_at' => now()]);
            return $payout->fresh();
        });
    }

    public function fail(Payout $payout, string $reason): Payout
    {
        return DB::transaction(function () use ($payout, $reason) {
            $payout = Payout::whereKey($payout->id)->lockForUpdate()->firstOrFail();
            if ($payout->status === 'failed') return $payout;
            if (! in_array($payout->status, ['approved', 'processing'], true)) throw new RuntimeException('Only approved or processing payouts can fail.');
            $payout->update(['status' => 'failed', 'failed_at' => now(), 'failure_reason' => $reason]);
            $this->ledger($payout, 'credit', 'payout_reversal', 'Payout reservation reversed after failure.');
            return $payout->fresh();
        });
    }

    private function balance(int $sellerId): float
    {
        return round((float) FinancialLedger::where('seller_id', $sellerId)->selectRaw("COALESCE(SUM(CASE WHEN direction = 'credit' THEN amount ELSE -amount END), 0) AS balance")->value('balance'), 2);
    }

    private function ledger(Payout $payout, string $direction, string $type, string $description): void
    {
        FinancialLedger::create(['seller_id' => $payout->seller_id, 'type' => $type, 'direction' => $direction, 'amount' => $payout->amount, 'currency' => $payout->currency, 'reference' => $payout->merchant_reference . '-' . $type, 'description' => $description, 'metadata' => ['payout_id' => $payout->id, 'method' => $payout->method]]);
    }
}
