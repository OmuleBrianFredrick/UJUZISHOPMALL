<?php
namespace App\Services;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use Illuminate\Support\Str;
class LoyaltyService {
    public function balance(int $userId): int { return (int) LoyaltyTransaction::where('user_id',$userId)->sum('points'); }
    public function awardForDeliveredOrder(Order $order): void {
        if (!$order->user_id) return;
        if (LoyaltyTransaction::where('order_id',$order->id)->where('type','purchase')->exists()) return;
        $points = max(0, (int) floor((float)$order->total / 1000));
        if ($points < 1) return;
        LoyaltyTransaction::create(['user_id'=>$order->user_id,'order_id'=>$order->id,'type'=>'purchase','points'=>$points,'reference'=>'LOY-'.Str::upper(Str::random(18)),'description'=>'Points awarded for completed order '.$order->order_number]);
    }
}
