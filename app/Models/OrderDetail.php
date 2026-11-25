<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_id', 'product_id', 'batch_id', 'so_luong', 'don_gia', 'thanh_tien'];

    protected function casts(): array
    {
        return ['so_luong' => 'decimal:2', 'don_gia' => 'decimal:2', 'thanh_tien' => 'decimal:2'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
