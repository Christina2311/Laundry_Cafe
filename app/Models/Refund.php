<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $primaryKey = 'refund_id';

    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'processed_by',
        'refund_amount',
        'refund_reason',
        'refund_date',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
            'refund_date' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class,
            'payment_id'
        );
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }
}
