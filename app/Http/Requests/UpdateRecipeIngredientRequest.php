<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner()
            && $this->user()?->isActive();
    }

    public function rules(): array
    {
        return [
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

            'inventory_item_id' => ['prohibited'],
            'recipe_id' => ['prohibited'],
        ];
    }
}
