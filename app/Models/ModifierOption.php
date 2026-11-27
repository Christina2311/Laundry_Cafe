<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierOption extends Model
{
    protected $primaryKey = 'modifier_option_id';

    public $timestamps = false;

    protected $fillable = [
        'modifier_group_id',
        'option_name',
        'additional_price',
    ];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
        ];
    }

    public function modifierGroup(): BelongsTo
    {
        return $this->belongsTo(
            ModifierGroup::class,
            'modifier_group_id'
        );
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(
            ModifierOptionIngredient::class,
            'modifier_option_id'
        );
    }

    public function orderDetailModifiers(): HasMany
    {
        return $this->hasMany(
            OrderDetailModifier::class,
            'modifier_option_id'
        );
    }
}
