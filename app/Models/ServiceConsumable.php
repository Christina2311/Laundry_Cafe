<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceConsumable extends Model
{
    protected $primaryKey = 'service_consumable_id';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'inventory_item_id',
        'quantity_required',
        'unit_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity_required' => 'decimal:4',
        ];
    }

    public function sellableItem(): BelongsTo
    {
        return $this->belongsTo(
            SellableItem::class,
            'item_id'
        );
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(
            InventoryItem::class,
            'inventory_item_id'
        );
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(
            MeasurementUnit::class,
            'unit_id'
        );
    }
}
