<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellableItem extends Model
{
    protected $primaryKey = 'item_id';

    public $timestamps = false;

    protected $fillable = [
        'menu_item_id',
        'item_name',
        'item_type',
        'current_price',
        'counts_as_load',
        'counts_as_piece',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_price' => 'decimal:2',
            'counts_as_load' => 'boolean',
            'counts_as_piece' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(
            MenuItem::class,
            'menu_item_id'
        );
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'item_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'item_id');
    }

    public function modifierGroups(): HasMany
    {
        return $this->hasMany(ModifierGroup::class, 'item_id');
    }

    public function serviceConsumables(): HasMany
    {
        return $this->hasMany(ServiceConsumable::class, 'item_id');
    }
}
