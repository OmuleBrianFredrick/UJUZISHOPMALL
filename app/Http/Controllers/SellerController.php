<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    public function apply(Request $request)
    {
        $profile = $request->user()->sellerProfile;
        return view('seller.apply', compact('profile'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);

        SellerProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            [...$validated, 'slug' => Str::slug($validated['store_name']), 'status' => 'pending']
        );

        return redirect()->route('seller.dashboard')->with('success', 'Seller application submitted for approval.');
    }

    public function dashboard(Request $request)
    {
        $profile = $request->user()->sellerProfile;
        abort_unless($profile && $profile->isApproved(), 403, 'Your seller account is awaiting approval.');

        $products = Product::where('seller_id', $request->user()->id)->latest()->paginate(12);
        return view('seller.dashboard', compact('profile', 'products'));
    }
}
