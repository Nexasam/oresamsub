<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class RequestAccountDeletionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'in:DELETE MY ACCOUNT'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
