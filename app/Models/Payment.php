<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'subscription_id', 'phuong_thuc', 'so_tien', 'ma_giao_dich',
        'trang_thai', 'ngay_thanh_toan', 'du_lieu_ipn',
    ];

    protected function casts(): array
    {
        return ['so_tien' => 'decimal:2', 'ngay_thanh_toan' => 'datetime', 'du_lieu_ipn' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
