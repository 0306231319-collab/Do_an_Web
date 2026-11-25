<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemConfig extends Model
{
    public const CREATED_AT = null;

    protected $fillable = ['updated_by', 'config_key', 'config_value', 'description'];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Lấy giá trị cấu hình, ví dụ SystemConfig::getValue('near_expiry_alert_days', 2) */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('config_key', $key)->value('config_value') ?? $default;
    }
}
