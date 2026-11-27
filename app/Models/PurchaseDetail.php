<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseDetail extends Model
{
    protected $primaryKey = 'purchase_detail_id';

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'inventory_item_id',
        'quantity_purchased',
        'purchase_unit_id',
        'conversion_factor',
        'base_quantity',
        'unit_cost',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity_purchased' => 'decimal:4',
            'conversion_factor' => 'decimal:8',
            'base_quantity' => 'decimal:4',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(
            Purchase::class,
            'purchase_id'
        );
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(
            InventoryItem::class,
            'inventory_item_id'
        );
    }

    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(
            MeasurementUnit::class,
            'purchase_unit_id'
        );
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(
            StockMovement::class,
            'purchase_detail_id'
        );
    }
}
