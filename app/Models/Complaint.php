<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'order_id', 'ly_do', 'mo_ta', 'hinh_anh_minh_chung', 'video_minh_chung', 'trang_thai',
        'sales_id', 'ly_do_tu_choi', 'don_bu_id', 'ngay_duyet',
    ];

    protected function casts(): array
    {
        return ['ngay_tao' => 'datetime', 'ngay_duyet' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function compensationOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'don_bu_id');
    }
}
