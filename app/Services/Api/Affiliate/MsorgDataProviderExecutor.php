<?php

namespace App\Services\Api\Affiliate;

use App\Models\AutomationProductPlan;
use App\Models\ProductPlan;
use App\Models\User;
use App\Models\UserProductPlanAutomation;
use App\Services\Automation\AutomationLogic;
use RuntimeException;

class MsorgDataProviderExecutor
{
    public function execute(User $user, ProductPlan $plan, array $payload): array
    {
        $userPlan = UserProductPlanAutomation::query()
            ->with('userAutomation.automation')
            ->where('user_id', $user->id)
            ->where('product_plan_id', $plan->id)
            ->where('status', 1)
            ->first();
        $mappedPlan = AutomationProductPlan::query()
            ->with('automation')
            ->where('product_plan_id', $plan->id)
            ->where('is_active', 1)
            ->orderBy('priority')
            ->first();

        if ($userPlan?->userAutomation?->automation && $userPlan->automation_product_plan_id) {
            $automation = $userPlan;
            $providerPlanId = $userPlan->automation_product_plan_id;
        } elseif ($mappedPlan?->automation && $mappedPlan->provider_plan_id) {
            $automation = $mappedPlan->automation;
            $providerPlanId = $mappedPlan->provider_plan_id;
        } elseif ($plan->automation && $plan->automation_product_plan_id) {
            $automation = $plan->automation;
            $providerPlanId = $plan->automation_product_plan_id;
        } else {
            throw new RuntimeException('No active provider is configured for this plan.');
        }

        $provider = $automation instanceof UserProductPlanAutomation
            ? $automation->userAutomation->automation
            : $automation;
        if ((string) $provider->activation_status !== '1') {
            throw new RuntimeException('No active provider is configured for this plan.');
        }

        return AutomationLogic::initiateDataPurchase([
            'phone_number' => $payload['mobile_number'],
            'automation_details' => $automation,
            'provider_plan_id' => $providerPlanId,
            'network_id' => $plan->product_plan_category->network_id,
            'plan_id' => $plan->id,
            'Ported_number' => $payload['Ported_number'],
            'validatephonenetwork' => $payload['validatephonenetwork'] ? 1 : 0,
        ]);
    }
}
