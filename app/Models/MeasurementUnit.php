<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementUnit extends Model
{
    protected $primaryKey = 'unit_id';

    public $timestamps = false;

    protected $fillable = [
        'unit_name',
        'abbreviation',
    ];

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'base_unit_id');
    }

    public function fromConversions(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'from_unit_id');
    }

    public function toConversions(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'to_unit_id');
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class, 'unit_id');
    }

    public function modifierOptionIngredients(): HasMany
    {
        return $this->hasMany(ModifierOptionIngredient::class, 'unit_id');
    }

    public function serviceConsumables(): HasMany
    {
        return $this->hasMany(ServiceConsumable::class, 'unit_id');
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_unit_id');
    }
}
