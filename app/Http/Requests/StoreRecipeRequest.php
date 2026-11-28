<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner()
            && $this->user()?->isActive();
    }

    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                Rule::exists('sellable_items', 'item_id')
                    ->where('item_type', 'cafe'),
            ],

            'recipe_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('recipes', 'recipe_name')
                    ->where('item_id', $this->input('item_id')),
            ],

            'is_active' => ['prohibited'],
        ];
    }
}
