<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'other_names' => ['nullable', 'string', 'max:80'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($this->user()->id)],
            'customer_landmark' => ['nullable', 'string', 'max:255'],
        ];
    }
}
