<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'batch_id', 'user_id', 'order_id', 'action_type', 'quantity_change',
        'balance_after', 'reason', 'evidence_image',
    ];

    protected function casts(): array
    {
        return ['quantity_change' => 'decimal:2', 'balance_after' => 'decimal:2'];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
