<?php

namespace App\Services\Pricing;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
use App\Models\ProductPlanCustomPricing;
use App\Models\User;

class CustomerProductPricingService
{
    public function resolve(User $user, ProductPlan $plan): array
    {
        $plan->loadMissing(['product_plan_category.product', 'product_plan_category.network']);

        $category = $plan->product_plan_category;
        $slug = $category->product->slug;
        $level = min(12, max(1, (int) ($user->user_plan?->plan_level ?? 1)));

        if ($slug === 'data') {
            $pricing = (new DataPlansService())->get_customer_price_per_plan([
                'product_id' => $category->product->id,
                'user' => $user,
                'plan_details' => $plan,
                'network_id' => $category->network->id,
            ]);

            return [
                'price' => round((float) $pricing['message'], 2),
                'pricing_type' => 'fixed',
                'plan_level' => $level,
            ];
        }

        $priceField = "user_level_{$level}_selling_price";
        $levelPrice = $plan->{$priceField} ?? $plan->user_level_1_selling_price ?? $plan->default_selling_price;
        $customPrice = ProductPlanCustomPricing::query()
            ->where('product_plan_id', $plan->id)
            ->where('user_id', $user->id)
            ->first();

        return [
            'price' => round((float) ($customPrice?->price ?? $levelPrice), 2),
            'pricing_type' => in_array($slug, ['airtime', 'utility_bills'], true)
                ? 'percentage_discount'
                : 'fixed',
            'plan_level' => $level,
        ];
    }
}
