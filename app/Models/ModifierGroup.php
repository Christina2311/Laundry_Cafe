<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierGroup extends Model
{
    protected $primaryKey = 'modifier_group_id';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'group_name',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function sellableItem(): BelongsTo
    {
        return $this->belongsTo(
            SellableItem::class,
            'item_id'
        );
    }

    public function options(): HasMany
    {
        return $this->hasMany(
            ModifierOption::class,
            'modifier_group_id'
        );
    }
}
