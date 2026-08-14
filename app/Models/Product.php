<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'category', 'description', 'price', 'quantity', 'reorder_level', 'image', 'seller_id'];

    public function stockMovements() { return $this->hasMany(StockMovement::class); }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function isLowStock(): bool { return $this->quantity <= $this->reorder_level; }
}
