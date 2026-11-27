<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitConversion extends Model
{
    protected $primaryKey = 'conversion_id';

    public $timestamps = false;

    protected $fillable = [
        'inventory_item_id',
        'from_unit_id',
        'to_unit_id',
        'conversion_factor',
    ];

    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:8',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(
            InventoryItem::class,
            'inventory_item_id'
        );
    }

    public function fromUnit(): BelongsTo
    {
        return $this->belongsTo(
            MeasurementUnit::class,
            'from_unit_id'
        );
    }

    public function toUnit(): BelongsTo
    {
        return $this->belongsTo(
            MeasurementUnit::class,
            'to_unit_id'
        );
    }
}
