<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryStatusHistory extends Model
{
    protected $table = 'laundry_status_histories';

    protected $primaryKey = 'history_id';

    public $timestamps = false;

    protected $fillable = [
        'laundry_order_id',
        'changed_by',
        'previous_status',
        'new_status',
        'changed_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(
            LaundryOrder::class,
            'laundry_order_id'
        );
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}
