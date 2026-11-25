<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliverySchedule extends Model
{
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'subscription_id', 'ngay_giao_du_kien', 'order_id', 'trang_thai_sinh_don',
        'nguoi_xu_ly_id', 'ghi_chu',
    ];

    protected function casts(): array
    {
        return ['ngay_giao_du_kien' => 'date'];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_xu_ly_id');
    }
}
