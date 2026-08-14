<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = ['seller_id','provider','method','phone','amount','currency','status','merchant_reference','provider_reference','approved_by','approved_at','processed_at','paid_at','failed_at','failure_reason','provider_response','metadata'];

    protected $casts = ['amount' => 'decimal:2', 'approved_at' => 'datetime', 'processed_at' => 'datetime', 'paid_at' => 'datetime', 'failed_at' => 'datetime', 'provider_response' => 'array', 'metadata' => 'array'];

    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}
