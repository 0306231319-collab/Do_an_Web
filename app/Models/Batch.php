<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'created_by', 'batch_code', 'qr_code_url', 'harvest_date', 'expiry_date',
        'initial_quantity', 'current_quantity', 'status',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'expiry_date' => 'date',
            'initial_quantity' => 'decimal:2',
            'current_quantity' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function farmingLogs(): HasMany
    {
        return $this->hasMany(FarmingLog::class)->orderBy('thoi_gian');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    /** Số ngày còn lại đến hạn sử dụng (âm nếu đã quá hạn) */
    public function daysToExpiry(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->expiry_date->copy()->startOfDay(), false);
    }
}
