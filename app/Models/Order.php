<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $fillable = [
        'processed_by',
        'discount_id',
        'order_datetime',
        'order_status',
        'subtotal',
        'discount_amount',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_datetime' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(
            Discount::class,
            'discount_id'
        );
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(
            OrderDetail::class,
            'order_id'
        );
    }

    public function laundryOrder(): HasOne
    {
        return $this->hasOne(
            LaundryOrder::class,
            'order_id'
        );
    }

    public function payment(): HasOne
    {
        return $this->hasOne(
            Payment::class,
            'order_id'
        );
    }
}
