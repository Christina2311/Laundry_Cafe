<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $primaryKey = 'purchase_id';

    public $timestamps = false;

    protected $fillable = [
        'supplier_id',
        'recorded_by',
        'source_type',
        'source_name',
        'purchase_date',
        'purchase_status',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(
            PurchaseDetail::class,
            'purchase_id'
        );
    }
}
