<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Services\Financial\PayoutService;
use Illuminate\Http\Request;
use Throwable;

class SellerPayoutController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isSeller(), 403);
        $payouts = Payout::where('seller_id', $request->user()->id)->latest()->paginate(20);
        return view('seller.payouts.index', compact('payouts'));
    }

    public function store(Request $request, PayoutService $service)
    {
        abort_unless($request->user()->isSeller(), 403);
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:1'], 'method' => ['required', 'in:mtn_momo,airtel_money'], 'phone' => ['required', 'string', 'max:30']]);
        try { $service->request($request->user(), (float) $data['amount'], $data['method'], $data['phone']); }
        catch (Throwable $e) { return back()->withInput()->withErrors(['amount' => $e->getMessage()]); }
        return redirect()->route('seller.payouts.index')->with('success', 'Payout request submitted for administrator approval.');
    }
}
