<?php

namespace App\Http\Controllers;

use App\Models\FinancialLedger;
use App\Services\Financial\BalanceService;
use Illuminate\Http\Request;

class SellerFinanceController extends Controller
{
    public function index(Request $request, BalanceService $balanceService)
    {
        abort_unless(($request->user()->role ?? null) === 'seller', 403);
        $summary = $balanceService->totalsFor($request->user()->id);
        $entries = FinancialLedger::where('seller_id', $request->user()->id)->latest()->paginate(20);
        return view('seller.finance.index', compact('summary', 'entries'));
    }
}
