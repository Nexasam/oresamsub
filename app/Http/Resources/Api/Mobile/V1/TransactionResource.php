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
        $plan = $this->relationLoaded('product_plan') ? $this->product_plan : null;
        $category = $plan?->product_plan_category;
        $product = $category?->product;
        $level = min(7, max(1, (int) ($request->user()?->user_plan?->plan_level ?? 1)));
        $priceField = "user_level_{$level}_selling_price";
        $canBuyAgain = $status === 'successful'
            && $this->transaction_category === 'data'
            && $product?->slug === 'data'
            && (string) $plan?->visibility === '1'
            && (string) $plan?->public_visibility === '1'
            && (string) $plan?->active_status === '1'
            && (string) $category?->visibility === '1';

        return [
            'id' => $this->id,
            'category' => $this->transaction_category,
            'status' => $status,
            'amount' => round((float) $this->amount, 2),
            'description' => $this->description,
            'beneficiary' => $this->phone_number ?: ($this->smart_card_number ?: $this->metre_number),
            'message' => $this->user_screen_message,
            'created_at' => $this->created_at,
            'repeat_purchase' => $canBuyAgain ? [
                'product' => 'data',
                'plan_id' => $plan->id,
                'plan_name' => $plan->product_plan_name,
                'price' => round((float) ($plan->{$priceField} ?: $plan->default_selling_price), 2),
                'provider' => $category->network?->network_name ?? $category->product_plan_category_name,
                'beneficiary' => $this->phone_number,
            ] : null,
        ];
    }
}
