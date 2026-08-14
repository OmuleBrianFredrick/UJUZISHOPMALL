<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Services\Financial\PayoutService;
use Illuminate\Http\Request;
use Throwable;

class AdminPayoutController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $payouts = Payout::with(['seller', 'approver'])->latest()->paginate(30);
        return view('admin.payouts.index', compact('payouts'));
    }

    public function approve(Request $request, Payout $payout, PayoutService $service)
    {
        abort_unless($request->user()->isAdmin(), 403);
        try { $service->approve($payout, $request->user()); }
        catch (Throwable $e) { return back()->withErrors(['payout' => $e->getMessage()]); }
        return back()->with('success', 'Payout approved and balance reserved. Complete the provider disbursement, then mark it paid.');
    }

    public function paid(Request $request, Payout $payout, PayoutService $service)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['provider_reference' => ['required', 'string', 'max:120']]);
        try { $service->markPaid($payout, $data['provider_reference']); }
        catch (Throwable $e) { return back()->withErrors(['payout' => $e->getMessage()]); }
        return back()->with('success', 'Payout marked as paid and retained in the ledger.');
    }

    public function fail(Request $request, Payout $payout, PayoutService $service)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);
        try { $service->fail($payout, $data['reason']); }
        catch (Throwable $e) { return back()->withErrors(['payout' => $e->getMessage()]); }
        return back()->with('success', 'Payout failed and its reserved balance was reversed.');
    }
}
