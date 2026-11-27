<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetailModifier extends Model
{
    protected $primaryKey = 'order_detail_modifier_id';

    public $timestamps = false;

    protected $fillable = [
        'order_detail_id',
        'modifier_option_id',
        'additional_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'quantity' => 'decimal:4',
        ];
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(
            OrderDetail::class,
            'order_detail_id'
        );
    }

    public function modifierOption(): BelongsTo
    {
        return $this->belongsTo(
            ModifierOption::class,
            'modifier_option_id'
        );
    }
}
