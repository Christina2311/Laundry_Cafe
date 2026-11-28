<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner()
            && $this->user()?->isActive();
    }

    public function rules(): array
    {
        $recipe = $this->route('recipe');

        return [
            'recipe_name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('recipes', 'recipe_name')
                    ->where('item_id', $recipe->item_id)
                    ->ignore($recipe->recipe_id, 'recipe_id'),
            ],

            'item_id' => ['prohibited'],
            'is_active' => ['prohibited'],
        ];
    }
}
