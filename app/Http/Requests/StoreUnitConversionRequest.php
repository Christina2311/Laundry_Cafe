<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitConversionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->isActive();
    }

    public function rules(): array
    {
        return [
            'inventory_item_id' => [
                'required',
                'integer',
                'exists:inventory_items,inventory_item_id',
            ],

            'from_unit_id' => [
                'required',
                'integer',
                'exists:measurement_units,unit_id',
            ],

            'conversion_factor' => [
                'required',
                'numeric',
                'decimal:0,8',
                'min:0.00000001',
                'regex:/^\d{1,10}(\.\d{1,8})?$/',
            ],

            'to_unit_id' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'inventory_item_id.required' =>
            'Please select an inventory item.',

            'inventory_item_id.exists' =>
            'The selected inventory item does not exist.',

            'from_unit_id.required' =>
            'Please select a source measurement unit.',

            'from_unit_id.exists' =>
            'The selected measurement unit does not exist.',

            'conversion_factor.required' =>
            'Please enter a conversion factor.',

            'conversion_factor.numeric' =>
            'The conversion factor must be a number.',

            'conversion_factor.min' =>
            'The conversion factor must be greater than zero.',

            'conversion_factor.decimal' =>
            'The conversion factor must have at most 8 decimal places.',

            'conversion_factor.regex' =>
            'The conversion factor must have at most 10 integer digits and 8 decimal places.',

            'to_unit_id.prohibited' =>
            'The destination unit is automatically assigned.',
        ];
    }
}
