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
        $candidateRoutes = [];

        if ($userPlan?->userAutomation?->automation && $userPlan->automation_product_plan_id) {
            $candidateRoutes[] = [$userPlan, $userPlan->automation_product_plan_id];
        }

        AutomationProductPlan::query()
            ->with('automation')
            ->where('product_plan_id', $plan->id)
            ->where('is_active', 1)
            ->orderBy('priority')
            ->get()
            ->each(function (AutomationProductPlan $automationProductPlan) use (&$candidateRoutes): void {
                if ($automationProductPlan->automation && $automationProductPlan->provider_plan_id) {
                    $candidateRoutes[] = [$automationProductPlan->automation, $automationProductPlan->provider_plan_id];
                }
            });

        if ($plan->automation && $plan->automation_product_plan_id) {
            $candidateRoutes[] = [$plan->automation, $plan->automation_product_plan_id];
        }

        if ($candidateRoutes === []) {
            throw new RuntimeException('No active provider is configured for this plan.');
        }

        $lastResult = null;

        foreach ($candidateRoutes as [$automation, $providerPlanId]) {
            $provider = $automation instanceof UserProductPlanAutomation
                ? $automation->userAutomation->automation
                : $automation;

            if (! $provider || (string) $provider->activation_status !== '1') {
                continue;
            }

            $result = AutomationLogic::initiateDataPurchase([
                'phone_number' => $payload['mobile_number'],
                'automation_details' => $automation,
                'provider_plan_id' => $providerPlanId,
                'network_id' => $plan->product_plan_category->network_id,
                'plan_id' => $plan->id,
                'Ported_number' => $payload['Ported_number'],
                'validatephonenetwork' => $payload['validatephonenetwork'] ? 1 : 0,
            ]);

            $lastResult = array_merge($result, [
                'provider_id' => $provider->id,
                'provider_name' => $provider->automation_name,
                'provider_slug' => $provider->slug,
                'provider_plan_id' => (string) $providerPlanId,
            ]);

            if ((int) ($result['status'] ?? -1) === 1) {
                return $lastResult;
            }
        }

        if (! $lastResult) {
            throw new RuntimeException('No active provider is configured for this plan.');
        }

        return $lastResult;
    }
}
