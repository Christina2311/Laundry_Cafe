<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->isActive();
    }

    public function rules(): array
    {
        return [
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique(
                    'inventory_categories',
                    'category_name'
                )->ignore(
                    $this->route('inventory_category')
                ),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
