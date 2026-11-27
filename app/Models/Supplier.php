<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $primaryKey = 'supplier_id';

    public $timestamps = false;

    protected $fillable = [
        'supplier_name',
        'contact_number',
        'address',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }
}
