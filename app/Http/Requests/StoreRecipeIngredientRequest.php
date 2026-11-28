<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipeIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner()
            && $this->user()?->isActive();
    }

    public function rules(): array
    {
        return [
            'inventory_item_id' => [
                'required',
                'integer',
                Rule::exists(
                    'inventory_items',
                    'inventory_item_id'
                )->where('is_active', true),
            ],

            'quantity_required' => [
                'required',
                'numeric',
                'decimal:0,4',
                'min:0.0001',
                'max:99999999999.9999',
            ],

            'unit_id' => [
                'required',
                'integer',
                'exists:measurement_units,unit_id',
            ],

            'recipe_id' => ['prohibited'],
        ];
    }
}
