<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Promotion extends Model {
    protected $fillable = ['code','type','value','minimum_order','usage_limit','usage_count','starts_at','ends_at','active'];
    protected $casts = ['value'=>'decimal:2','minimum_order'=>'decimal:2','starts_at'=>'datetime','ends_at'=>'datetime','active'=>'boolean'];
    public function isValidFor(float $subtotal): bool {
        $now = now();
        return $this->active && $subtotal >= (float)$this->minimum_order && (!$this->starts_at || $this->starts_at <= $now) && (!$this->ends_at || $this->ends_at >= $now) && (!$this->usage_limit || $this->usage_count < $this->usage_limit);
    }
    public function discountFor(float $subtotal): float {
        if (!$this->isValidFor($subtotal)) return 0.0;
        return round(min($subtotal, $this->type === 'fixed' ? (float)$this->value : $subtotal * ((float)$this->value / 100)), 2);
    }
}
