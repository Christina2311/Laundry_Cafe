<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $primaryKey = 'inventory_item_id';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'base_unit_id',
        'item_name',
        'reorder_level',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:4',
            'reorder_level' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            InventoryCategory::class,
            'category_id'
        );
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(
            MeasurementUnit::class,
            'base_unit_id'
        );
    }

    public function unitConversions(): HasMany
    {
        return $this->hasMany(
            UnitConversion::class,
            'inventory_item_id'
        );
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(
            RecipeIngredient::class,
            'inventory_item_id'
        );
    }

    public function modifierOptionIngredients(): HasMany
    {
        return $this->hasMany(
            ModifierOptionIngredient::class,
            'inventory_item_id'
        );
    }

    public function serviceConsumables(): HasMany
    {
        return $this->hasMany(
            ServiceConsumable::class,
            'inventory_item_id'
        );
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(
            PurchaseDetail::class,
            'inventory_item_id'
        );
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(
            StockMovement::class,
            'inventory_item_id'
        );
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }
}
