<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'name', 'slug', 'unit', 'base_price', 'discount_price',
        'thumbnail_url', 'description', 'is_subscription', 'status',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'is_subscription' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /** Các lô còn bán được, xếp theo FEFO (hạn gần nhất trước) */
    public function availableBatchesFefo(): HasMany
    {
        return $this->hasMany(Batch::class)
            ->where('current_quantity', '>', 0)
            ->whereDate('expiry_date', '>', now()->toDateString())
            ->orderBy('expiry_date')
            ->orderBy('id');
    }
}
