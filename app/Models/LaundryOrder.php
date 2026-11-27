<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryOrder extends Model
{
    protected $primaryKey = 'laundry_order_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'customer_name',
        'total_loads',
        'total_pieces',
        'requested_pickup_datetime',
        'laundry_status',
        'special_instructions',
    ];

    protected function casts(): array
    {
        return [
            'total_loads' => 'integer',
            'total_pieces' => 'integer',
            'requested_pickup_datetime' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_id'
        );
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(
            LaundryStatusHistory::class,
            'laundry_order_id'
        );
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(
            StockMovement::class,
            'laundry_order_id'
        );
    }
}
