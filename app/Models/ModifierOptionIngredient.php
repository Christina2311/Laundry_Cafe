<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModifierOptionIngredient extends Model
{
    protected $primaryKey = 'modifier_ingredient_id';

    public $timestamps = false;

    protected $fillable = [
        'modifier_option_id',
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

    public function modifierOption(): BelongsTo
    {
        return $this->belongsTo(
            ModifierOption::class,
            'modifier_option_id'
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
