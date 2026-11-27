<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function processedOrders(): HasMany
    {
        return $this->hasMany(
            Order::class,
            'processed_by'
        );
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(
            Payment::class,
            'verified_by'
        );
    }

    public function processedRefunds(): HasMany
    {
        return $this->hasMany(
            Refund::class,
            'processed_by'
        );
    }

    public function laundryStatusHistories(): HasMany
    {
        return $this->hasMany(
            LaundryStatusHistory::class,
            'changed_by'
        );
    }

    public function recordedPurchases(): HasMany
    {
        return $this->hasMany(
            Purchase::class,
            'recorded_by'
        );
    }

    public function recordedStockMovements(): HasMany
    {
        return $this->hasMany(
            StockMovement::class,
            'recorded_by'
        );
    }

    public function recordedExpenses(): HasMany
    {
        return $this->hasMany(
            Expense::class,
            'recorded_by'
        );
    }
}
