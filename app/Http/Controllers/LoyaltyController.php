<?php
namespace App\Http\Controllers;
use App\Models\LoyaltyTransaction;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
class LoyaltyController extends Controller {
    public function index(Request $request, LoyaltyService $loyalty) {
        $balance = $loyalty->balance($request->user()->id);
        $transactions = LoyaltyTransaction::where('user_id',$request->user()->id)->latest()->paginate(20);
        return view('storefront.loyalty', compact('balance','transactions'));
    }
}
