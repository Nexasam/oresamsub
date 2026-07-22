<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\PurchaseAirtimeRequest;
use App\Http\Requests\Api\Mobile\V1\PurchaseCableRequest;
use App\Http\Requests\Api\Mobile\V1\PurchaseDataRequest;
use App\Http\Requests\Api\Mobile\V1\PurchaseElectricityRequest;
use App\Http\Requests\Api\Mobile\V1\ValidateBillerRequest;
use App\Http\Resources\Api\Mobile\V1\TransactionResource;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MobilePurchaseController extends Controller
{
    use RespondsToMobileApi;

    public function data(PurchaseDataRequest $request, ProductsService $productsService): JsonResponse
    {
        if (! $this->validPin($request->user(), $request->string('pin')->toString())) {
            return $this->errorResponse('Incorrect transaction PIN.', null, 422);
        }
        $plan = $this->availablePlan($request->string('product_plan_id')->toString(), 'data');
        if (! $plan || ! $plan->product_plan_category->network) {
            return $this->errorResponse('This data plan is no longer available.', null, 422);
        }

        $result = $this->runPurchase(fn () => $productsService->buy_data_service([
            'network_id' => $plan->product_plan_category->network->id,
            'product_id' => $plan->product_plan_category->product->id,
            'phone_number' => $request->string('phone_number')->toString(),
            'product_plan_category_id' => $plan->product_plan_category->id,
            'product_plan_id' => $plan->id,
            'pin' => $request->string('pin')->toString(),
            'wallet_category' => 'main_wallet',
            'validatephonenetwork' => $request->boolean('validate_phone_network', true) ? 1 : 0,
            'user_id' => $request->user()->id,
            'user' => $request->user()->loadMissing('user_plan'),
            'coupon_code' => $request->input('coupon_code'),
            'reference' => $request->string('reference')->toString(),
        ]));

        return $this->purchaseResponse($result, 'Data purchase');
    }

    public function airtime(PurchaseAirtimeRequest $request, ProductsService $productsService): JsonResponse
    {
        if (! $this->validPin($request->user(), $request->string('pin')->toString())) {
            return $this->errorResponse('Incorrect transaction PIN.', null, 422);
        }
        $plan = $this->availablePlan($request->string('product_plan_id')->toString(), 'airtime');
        if (! $plan || ! $plan->product_plan_category->network) {
            return $this->errorResponse('This airtime product is no longer available.', null, 422);
        }

        $amount = round((float) $request->input('amount'), 2);
        $result = $this->runPurchase(fn () => $productsService->buy_airtime_service([
            'network_id' => $plan->product_plan_category->network->id,
            'product_plan_category_id' => $plan->product_plan_category->id,
            'phone_number' => $request->string('phone_number')->toString(),
            'product_plan_id' => $plan->id,
            'pin' => $request->string('pin')->toString(),
            'amount' => $amount,
            'actual_amount' => $amount,
            'validatephonenetwork' => $request->boolean('validate_phone_network', true) ? 1 : 0,
            'user_id' => $request->user()->id,
            'reference' => $request->string('reference')->toString(),
        ]));

        return $this->purchaseResponse($result, 'Airtime purchase');
    }

    public function validateCable(ValidateBillerRequest $request): JsonResponse
    {
        $plan = $this->availablePlan($request->string('product_plan_id')->toString(), 'cable_subscription');
        if (! $plan) {
            return $this->errorResponse('This cable plan is no longer available.', null, 422);
        }
        $result = $this->runPurchase(fn () => (new MegaSubCableTV(smart_card_number: $request->string('customer_number')->toString(), plan_id: $plan->id, user_id: $request->user()->id))->validateSmartCardNumber());
        if ((int) ($result['status'] ?? -1) !== 1) {
            return $this->errorResponse($result['message'] ?? 'Smart card validation failed.', null, 422);
        }

        return $this->successResponse('Smart card validated successfully.', $this->safeValidation($result));
    }

    public function cable(PurchaseCableRequest $request, ProductsService $productsService): JsonResponse
    {
        if (! $this->validPin($request->user(), $request->string('pin')->toString())) {
            return $this->errorResponse('Incorrect transaction PIN.', null, 422);
        }
        $plan = $this->availablePlan($request->string('product_plan_id')->toString(), 'cable_subscription');
        if (! $plan) {
            return $this->errorResponse('This cable plan is no longer available.', null, 422);
        }
        $result = $this->runPurchase(fn () => $productsService->buy_cable_service([
            'user_id' => $request->user()->id, 'user' => $request->user(), 'smart_card_number' => $request->string('smart_card_number')->toString(),
            'validation_customer_name' => $request->string('customer_name')->toString(), 'cable_product_plan_category_id' => $plan->product_plan_category_id,
            'cable_product_plan_id' => $plan->id, 'no_of_slots' => '1', 'wallet_category' => 'main_wallet', 'pin' => $request->string('pin')->toString(), 'reference' => $request->string('reference')->toString(),
        ]));

        return $this->purchaseResponse($result, 'Cable subscription');
    }

    public function validateElectricity(ValidateBillerRequest $request): JsonResponse
    {
        $plan = $this->availablePlan($request->string('product_plan_id')->toString(), 'utility_bills');
        if (! $plan) {
            return $this->errorResponse('This electricity provider is no longer available.', null, 422);
        }
        $result = $this->runPurchase(fn () => (new MegaSubElectricity(metre_number: $request->string('customer_number')->toString(), plan_id: $plan->id, user_id: $request->user()->id))->validateMetreNumber());
        if ((int) ($result['status'] ?? -1) !== 1) {
            return $this->errorResponse($result['message'] ?? 'Meter validation failed.', null, 422);
        }

        return $this->successResponse('Meter validated successfully.', $this->safeValidation($result));
    }

    public function electricity(PurchaseElectricityRequest $request, ProductsService $productsService): JsonResponse
    {
        if (! $this->validPin($request->user(), $request->string('pin')->toString())) {
            return $this->errorResponse('Incorrect transaction PIN.', null, 422);
        }
        $plan = $this->availablePlan($request->string('product_plan_id')->toString(), 'utility_bills');
        if (! $plan) {
            return $this->errorResponse('This electricity provider is no longer available.', null, 422);
        }
        $amount = round((float) $request->input('amount'), 2);
        $result = $this->runPurchase(fn () => $productsService->buy_electricity_service([
            'metre_number' => $request->string('metre_number')->toString(), 'validation_extra_info' => $request->string('validation_extra_info')->toString(),
            'validated_address' => $request->input('validated_address'), 'electricity_product_plan_category_id' => $plan->product_plan_category_id,
            'electricity_product_plan_id' => $plan->id, 'amount' => $amount, 'actual_amount' => $amount, 'pin' => $request->string('pin')->toString(),
            'user_id' => $request->user()->id, 'no_of_slots' => '1', 'wallet_category' => 'main_wallet', 'reference' => $request->string('reference')->toString(),
        ]));

        return $this->purchaseResponse($result, 'Electricity purchase');
    }

    public function status(Request $request, string $reference): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->where('txn_reference', $reference)->firstOrFail();

        return $this->successResponse('Purchase status fetched successfully.', ['transaction' => (new TransactionResource($transaction))->resolve($request)]);
    }

    private function availablePlan(string $id, string $slug): ?ProductPlan
    {
        return ProductPlan::query()->with(['product_plan_category.network', 'product_plan_category.product'])
            ->whereKey($id)->where('visibility', '1')->where('public_visibility', '1')->where('active_status', '1')
            ->whereHas('product_plan_category', fn ($query) => $query->where('visibility', '1')->whereHas(
                'product', fn ($product) => $product->where('slug', $slug)->where('visibility', '1')->where('active_status', '1')
            ))->first();
    }

    private function runPurchase(callable $purchase): array
    {
        try {
            return $purchase();
        } catch (Throwable $exception) {
            Log::error('Mobile purchase service failed.', ['exception' => $exception]);

            return ['status' => -1, 'message' => 'The provider could not process this purchase. No duplicate retry was made.'];
        }
    }

    private function purchaseResponse(array $result, string $label): JsonResponse
    {
        if ((int) ($result['status'] ?? -1) !== 1) {
            return $this->errorResponse($result['message'] ?? "$label failed.", null, 422);
        }

        return $this->successResponse("$label processed successfully.", [
            'status' => 'processed',
            'message' => $result['message'] ?? null,
        ]);
    }

    private function validPin($user, string $pin): bool
    {
        return filled($user->pin) && hash_equals((string) $user->pin, $pin);
    }

    private function safeValidation(array $result): array
    {
        $data = is_array($result['data'] ?? null) ? $result['data'] : [];

        return ['name' => $data['name'] ?? $result['name'] ?? null, 'address' => $data['address'] ?? $result['address'] ?? null, 'extra_info' => (string) ($data['extra_info'] ?? $result['extra_info'] ?? '')];
    }
}
