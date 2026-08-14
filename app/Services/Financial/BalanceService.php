<?php

namespace App\Services\Financial;

use App\Models\FinancialLedger;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    public function availableFor(int $sellerId): float
    {
        return round((float) FinancialLedger::where('seller_id', $sellerId)->selectRaw("COALESCE(SUM(CASE WHEN direction = 'credit' THEN amount ELSE -amount END), 0) AS balance")->value('balance'), 2);
    }

    public function totalsFor(int $sellerId): array
    {
        $credits = (float) FinancialLedger::where('seller_id', $sellerId)->where('direction', 'credit')->sum('amount');
        $debits = (float) FinancialLedger::where('seller_id', $sellerId)->where('direction', 'debit')->sum('amount');
        return ['credits' => round($credits, 2), 'debits' => round($debits, 2), 'available' => round($credits - $debits, 2)];
    }
}
