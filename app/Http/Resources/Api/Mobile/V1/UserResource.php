<?php

namespace App\Http\Resources\Api\Mobile\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'other_names' => $this->other_names,
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'phone_verified' => (bool) $this->phone_verification,
            'transaction_pin_set' => filled($this->pin),
            'is_deactivated' => (bool) $this->is_deactivated,
            'customer_landmark' => $this->customer_landmark,
        ];
    }
}
