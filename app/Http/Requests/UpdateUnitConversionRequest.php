<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitConversionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->isActive();
    }

    public function rules(): array
    {
        return [
            'conversion_factor' => [
                'required',
                'numeric',
                'decimal:0,8',
                'min:0.00000001',
                'regex:/^\d{1,10}(\.\d{1,8})?$/',
            ],

            'inventory_item_id' => ['prohibited'],

            'from_unit_id' => ['prohibited'],

            'to_unit_id' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
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

            'inventory_item_id.prohibited' =>
            'The inventory item cannot be changed.',

            'from_unit_id.prohibited' =>
            'The source measurement unit cannot be changed.',

            'to_unit_id.prohibited' =>
            'The destination measurement unit cannot be changed.',
        ];
    }
}
