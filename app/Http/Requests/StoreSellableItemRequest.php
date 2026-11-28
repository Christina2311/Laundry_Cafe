<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSellableItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner()
            && $this->user()?->isActive();
    }

    public function rules(): array
    {
        return [
            'menu_item_id' => [
                'required',
                'integer',
                Rule::exists('menu_items', 'menu_item_id')
                    ->where('is_active', true),
            ],

            'item_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('sellable_items', 'item_name')
                    ->where('item_type', 'cafe'),
            ],

            'current_price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0.01',
                'max:9999999999.99',
            ],

            'item_type' => ['prohibited'],
            'counts_as_load' => ['prohibited'],
            'counts_as_piece' => ['prohibited'],
            'is_active' => ['prohibited'],
        ];
    }
}
