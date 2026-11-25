<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'user_id', 'ten_goi', 'nguoi_nhan', 'sdt_nhan', 'dia_chi_giao', 'tong_tien',
        'ngay_bat_dau', 'ngay_ket_thuc', 'trang_thai',
    ];

    protected function casts(): array
    {
        return ['tong_tien' => 'decimal:2', 'ngay_bat_dau' => 'date', 'ngay_ket_thuc' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DeliverySchedule::class)->orderBy('ngay_giao_du_kien');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
