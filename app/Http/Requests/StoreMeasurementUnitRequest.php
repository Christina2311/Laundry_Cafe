<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementUnitRequest extends FormRequest
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
                'unique:measurement_units,unit_name',
            ],

            'abbreviation' => [
                'required',
                'string',
                'max:20',
                'unique:measurement_units,abbreviation',
            ],
        ];
    }
}
