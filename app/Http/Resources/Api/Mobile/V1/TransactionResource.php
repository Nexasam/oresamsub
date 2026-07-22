<?php

namespace App\Http\Resources\Api\Mobile\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = match ((string) $this->status) {
            '1' => 'successful',
            '-1' => 'failed',
            '2' => 'refunded',
            '3' => 'processing',
            default => 'pending',
        };

        return [
            'id' => $this->id,
            'category' => $this->transaction_category,
            'status' => $status,
            'amount' => round((float) $this->amount, 2),
            'description' => $this->description,
            'beneficiary' => $this->phone_number ?: ($this->smart_card_number ?: $this->metre_number),
            'message' => $this->user_screen_message,
            'created_at' => $this->created_at,
        ];
    }
}
