<?php
namespace App\Http\Services\Api\v1\VendorUsersApi\Products;


use App\Http\Services\Api\v1\VendorUsersApi\Products\VendorExecutionService;
use App\Http\Services\Api\v1\VendorUsersApi\Products\WalletService;
use App\Http\Services\TransactionService;
use App\Models\ProductPlan;
use App\Models\User;
use App\Services\Product\ProductPricingService;
use App\Services\Product\ProductValidatorService;
use Illuminate\Support\Facades\DB;

class ProductPurchaseService
{
    public function __construct(
        protected VendorExecutionService $vendor,
        protected WalletService $wallet,
        protected TransactionService $transaction,
        protected ProductPricingService $pricing,
        protected ProductValidatorService $validator
    ) {}

    public function purchase(array $data): array
    {
        return DB::transaction(function () use ($data) {

            // 1. Resolve user
            $user = $this->resolveUser($data);

            // 2. Resolve plan
            $plan = $this->resolvePlan($data);

            // 3. Get pricing
            $pricing = $this->pricing->calculate($user, $plan, $data);

            // 4. Validate
            $this->validator->validate($user, $data, $pricing['amount']);

            // 5. Debit wallet
            $walletMeta = $this->wallet->debit($user, $pricing['amount'], [
                'type' => 'data_purchase'
            ]);

            // 6. Execute vendor
            $vendorResponse = $this->vendor->executeData($plan, $data);

            // 7. Determine status
            $status = $vendorResponse['status'] ? 'success' : 'failed';

            // 8. Handle failure (refund)
            if ($status === 'failed') {
                $this->wallet->credit($user, $pricing['amount'], [
                    'reason' => 'refund'
                ]);
            }

            // 9. Save transaction
            $txn = $this->transaction->create([
                'user' => $user,
                'plan' => $plan,
                'amount' => $pricing['amount'],
                'status' => $status,
                'meta' => $vendorResponse,
                'wallet_before' => $walletMeta['before'],
                'wallet_after' => $walletMeta['after'],
            ]);

            // 10. Return response
            return $this->buildResponse($txn, $vendorResponse);
        });
    }

    private function resolveUser($data): User
    {
        return $data['user']
            ?? User::with('user_plan')->findOrFail($data['user_id']);
    }

    private function resolvePlan($data): ProductPlan
    {
        return ProductPlan::findOrFail($data['product_plan_id']);
    }

    private function buildResponse($txn, $vendor): array
    {
        return [
            'reference' => $txn->txn_reference,
            'status' => $txn->status,
            'message' => $vendor['user_message'] ?? 'Processed',
        ];
    }
}