<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSellerController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->role === 'admin', 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);
        $profiles = SellerProfile::with('user')->latest()->paginate(20);
        return view('admin.sellers.index', compact('profiles'));
    }

    public function approve(Request $request, SellerProfile $sellerProfile)
    {
        $this->authorizeAdmin($request);

        DB::transaction(function () use ($sellerProfile) {
            $sellerProfile->update(['status' => 'approved']);
            $sellerProfile->user()->update(['role' => 'seller']);
        });

        return back()->with('success', 'Seller approved successfully.');
    }

    public function reject(Request $request, SellerProfile $sellerProfile)
    {
        $this->authorizeAdmin($request);
        $sellerProfile->update(['status' => 'rejected']);
        return back()->with('success', 'Seller application rejected.');
    }
}
