<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner()
            && $this->user()?->isActive();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'menu_item_name' => is_string($this->menu_item_name)
                ? trim($this->menu_item_name)
                : $this->menu_item_name,

            'category' => is_string($this->category)
                ? trim($this->category)
                : $this->category,
        ]);
    }

    public function rules(): array
    {
        return [
            'menu_item_name' => [
                'required',
                'string',
                'max:150',
                'unique:menu_items,menu_item_name',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'is_active' => ['prohibited'],
        ];
    }
}
