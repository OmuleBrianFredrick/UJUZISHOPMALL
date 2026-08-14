<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialLedger extends Model
{
    protected $fillable = [
        'seller_id', 'order_id', 'payment_id', 'type', 'direction',
        'amount', 'currency', 'reference', 'description', 'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
}
