<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->isActive();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')),
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],

            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'role_id')
                    ->whereIn('role_name', ['Owner', 'Cashier', 'Staff']),
            ],
        ];
    }
}
