<?php
namespace App\Http\Controllers;
use App\Models\Promotion;
use Illuminate\Http\Request;
class PromotionController extends Controller {
    public function validateCode(Request $request) {
        $data = $request->validate(['code'=>['required','string','max:50'],'subtotal'=>['required','numeric','min:0']]);
        $promotion = Promotion::where('code',strtoupper(trim($data['code'])))->first();
        if (!$promotion || !$promotion->isValidFor((float)$data['subtotal'])) return response()->json(['valid'=>false,'discount'=>0,'message'=>'Promotion code is invalid, expired, inactive or below its minimum order.'],422);
        return response()->json(['valid'=>true,'discount'=>$promotion->discountFor((float)$data['subtotal']),'code'=>$promotion->code]);
    }
}
