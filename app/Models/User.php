<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_FARM_STAFF = 'farm_staff';
    public const ROLE_SALES = 'sales';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'full_name', 'email', 'password', 'phone', 'role',
        'avatar_url', 'loyalty_points', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'loyalty_points' => 'integer',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function createdBatches(): HasMany
    {
        return $this->hasMany(Batch::class, 'created_by');
    }

    public function farmingLogs(): HasMany
    {
        return $this->hasMany(FarmingLog::class, 'farm_staff_id');
    }

    public function handledComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'sales_id');
    }
}
