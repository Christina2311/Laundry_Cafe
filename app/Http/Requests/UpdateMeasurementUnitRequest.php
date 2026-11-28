<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMeasurementUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->isActive();
    }

    public function rules(): array
    {
        return [
            'unit_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('measurement_units', 'unit_name')
                    ->ignore($this->route('measurement_unit')),
            ],

            'abbreviation' => [
                'required',
                'string',
                'max:20',
                Rule::unique('measurement_units', 'abbreviation')
                    ->ignore($this->route('measurement_unit')),
            ],
        ];
    }
}
