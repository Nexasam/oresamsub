<?php

namespace App\Http\Requests\Api\Mobile\V1;

use Illuminate\Foundation\Http\FormRequest;

class SetTransactionPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pin' => ['required', 'digits:4', 'confirmed', 'not_in:1234,0000,1111,2222,3333,4444,5555,6666,7777,8888,9999'],
        ];
    }
}
