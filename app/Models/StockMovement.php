<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $primaryKey = 'movement_id';

    public $timestamps = false;

    protected $fillable = [
        'inventory_item_id',
        'purchase_detail_id',
        'order_detail_id',
        'laundry_order_id',
        'recorded_by',
        'movement_type',
        'movement_reason',
        'quantity_change',
        'movement_date',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'decimal:4',
            'movement_date' => 'datetime',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(
            InventoryItem::class,
            'inventory_item_id'
        );
    }

    public function purchaseDetail(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseDetail::class,
            'purchase_detail_id'
        );
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(
            OrderDetail::class,
            'order_detail_id'
        );
    }

    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(
            LaundryOrder::class,
            'laundry_order_id'
        );
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }
}
