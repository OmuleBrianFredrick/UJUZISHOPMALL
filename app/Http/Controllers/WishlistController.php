<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
class WishlistController extends Controller {
    public function index(Request $request) {
        $items = Wishlist::where('user_id',$request->user()->id)->with('product')->latest()->get();
        return view('storefront.wishlist', compact('items'));
    }
    public function toggle(Request $request, Product $product) {
        $item = Wishlist::where('user_id',$request->user()->id)->where('product_id',$product->id)->first();
        if ($item) { $item->delete(); return back()->with('success','Product removed from your wishlist.'); }
        Wishlist::create(['user_id'=>$request->user()->id,'product_id'=>$product->id]);
        return back()->with('success','Product added to your wishlist.');
    }
}
