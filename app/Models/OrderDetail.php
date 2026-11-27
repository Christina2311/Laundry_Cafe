<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    protected $primaryKey = 'order_detail_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_id'
        );
    }

    public function sellableItem(): BelongsTo
    {
        return $this->belongsTo(
            SellableItem::class,
            'item_id'
        );
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(
            OrderDetailModifier::class,
            'order_detail_id'
        );
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(
            StockMovement::class,
            'order_detail_id'
        );
    }
}
