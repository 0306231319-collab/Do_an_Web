<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmingLog extends Model
{
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = null;

    protected $fillable = [
        'batch_id', 'farm_staff_id', 'hanh_dong', 'mo_ta_chi_tiet',
        'hinh_anh_minh_chung', 'video_minh_chung', 'thoi_gian',
    ];

    protected function casts(): array
    {
        return ['thoi_gian' => 'datetime', 'ngay_tao' => 'datetime'];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farm_staff_id');
    }
}
