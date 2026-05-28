<?php
namespace App\Http\Services\Api\v1\VendorUsersApi\Products;

use App\Services\Automation\AutomationLogic;

class VendorExecutionService
{
    public function executeData($plan, $data): array
    {
        return AutomationLogic::initiateDataPurchase([
            'phone_number' => $data['phone_number'],
            'automation_details' => $plan->automation,
            'network_id' => $data['network_id'],
            'plan_id' => $plan->id,
        ]);
    }
}