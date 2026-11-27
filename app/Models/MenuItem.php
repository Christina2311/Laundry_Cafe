<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $primaryKey = 'menu_item_id';

    public $timestamps = false;

    protected $fillable = [
        'menu_item_name',
        'category',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sellableItems(): HasMany
    {
        return $this->hasMany(SellableItem::class, 'menu_item_id');
    }
}
