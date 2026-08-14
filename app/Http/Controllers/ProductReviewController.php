<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
class ProductReviewController extends Controller {
    public function store(Request $request, Product $product) {
        $data = $request->validate(['rating'=>['required','integer','between:1,5'],'body'=>['nullable','string','max:2000'],'order_id'=>['required','integer','exists:orders,id']]);
        $order = Order::where('id',$data['order_id'])->where('user_id',$request->user()->id)->where('status','delivered')->whereHas('items', fn($q)=>$q->where('product_id',$product->id))->first();
        if (!$order) throw ValidationException::withMessages(['order_id'=>'You can review this product only after your order has been delivered.']);
        if (ProductReview::where('user_id',$request->user()->id)->where('product_id',$product->id)->where('order_id',$order->id)->exists()) throw ValidationException::withMessages(['rating'=>'You have already reviewed this purchase.']);
        ProductReview::create(['user_id'=>$request->user()->id,'product_id'=>$product->id,'order_id'=>$order->id,'rating'=>$data['rating'],'body'=>$data['body'] ?? null,'status'=>'approved','approved_at'=>now()]);
        return back()->with('success','Thank you. Your verified review has been published.');
    }
}
