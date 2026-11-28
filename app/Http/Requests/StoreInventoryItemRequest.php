<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->isActive();
    }

    public function rules(): array
    {
        return [
            'item_name' => [
                'required',
                'string',
                'max:150',
                'unique:inventory_items,item_name',
            ],

            'category_id' => [
                'required',
                'integer',
                Rule::exists('inventory_categories', 'category_id'),
            ],

            'base_unit_id' => [
                'required',
                'integer',
                Rule::exists('measurement_units', 'unit_id'),
            ],

            'reorder_level' => [
                'required',
                'numeric',
                'decimal:0,4',
                'min:0',
                'max:99999999999.9999',
            ],

            'current_stock' => ['prohibited'],

            'is_active' => ['prohibited'],
        ];
    }
}
