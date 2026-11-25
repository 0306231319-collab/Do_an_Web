<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'subscription_id', 'ma_don_hang', 'loai_don', 'nguoi_nhan', 'sdt_nhan',
        'dia_chi_giao', 'ghi_chu', 'tam_tinh', 'phi_van_chuyen', 'tong_tien', 'trang_thai',
        'ngay_giao_thuc_te',
    ];

    protected function casts(): array
    {
        return [
            'tam_tinh' => 'decimal:2',
            'phi_van_chuyen' => 'decimal:2',
            'tong_tien' => 'decimal:2',
            'ngay_tao' => 'datetime',
            'ngay_giao_thuc_te' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function complaint(): HasOne
    {
        return $this->hasOne(Complaint::class);
    }
}
