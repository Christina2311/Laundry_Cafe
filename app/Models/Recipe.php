<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $primaryKey = 'recipe_id';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'recipe_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sellableItem(): BelongsTo
    {
        return $this->belongsTo(
            SellableItem::class,
            'item_id'
        );
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(
            RecipeIngredient::class,
            'recipe_id'
        );
    }
}
