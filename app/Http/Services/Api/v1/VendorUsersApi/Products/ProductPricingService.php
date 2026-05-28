<?php

namespace App\Http\Services\Api\v1\VendorUsersApi\Products;


use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
use App\Models\User;

class ProductPricingService
{
    public function calculate(User $user, ProductPlan $plan, array $data): array
    {
        // 🔥 base pricing from your existing logic
        $priceData = (new DataPlansService())->get_customer_price_per_plan([
            'product_id' => $plan->product_plan_category->product->id,
            'user' => $user,
            'plan_details' => $plan,
            'network_id' => $data['network_id'] ?? null,
        ]);

        $amount = $priceData['message']; // your system uses 'message' as price
        $commission = $priceData['upline_commission'] ?? 0;

        // ✅ Apply coupon (if exists)
        $discount = 0;

        if (!empty($data['coupon_code'])) {
            $discount = $this->applyCoupon(
                $data['coupon_code'],
                $user,
                $amount
            );
        }

        $finalAmount = max(0, $amount - $discount);

        return [
            'amount' => $finalAmount,
            'original_amount' => $amount,
            'discount' => $discount,
            'commission' => $commission,
        ];
    }

    private function applyCoupon(string $code, User $user, float $amount): float
    {
        // ⚠️ Replace with your real coupon logic
        $coupon = \App\Models\Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return 0;
        }

        // Example logic
        if ($coupon->type === 'percentage') {
            return ($coupon->value / 100) * $amount;
        }

        if ($coupon->type === 'fixed') {
            return min($coupon->value, $amount);
        }

        return 0;
    }
}