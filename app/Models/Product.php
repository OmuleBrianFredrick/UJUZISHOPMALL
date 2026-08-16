<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'category', 'description', 'price', 'quantity', 'reorder_level', 'image', 'seller_id'];
    protected $casts = ['price' => 'decimal:2'];
    public function stockMovements(): HasMany { return $this->hasMany(StockMovement::class); }
    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function approvedReviews(): HasMany { return $this->reviews()->where('status', 'approved'); }
    public function wishlists(): HasMany { return $this->hasMany(Wishlist::class); }
    public function averageRating(): float { return round((float) $this->approvedReviews()->avg('rating'), 1); }
    public function isLowStock(): bool { return $this->quantity <= $this->reorder_level; }

    /**
     * Query scope for low-stock products. Centralises the low-stock rule so
     * callers (controllers, views) can reuse the same logic without reimplementing it.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }
}
