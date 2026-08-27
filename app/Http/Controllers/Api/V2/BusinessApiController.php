<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BusinessApi\BillerValidationService;
use App\Services\Pricing\CustomerProductPricingService;
use App\Support\MobileDisplayMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BusinessApiController extends Controller
{
    public function catalogue(Request $request, CustomerProductPricingService $pricingService): JsonResponse
    {
        $user = $this->user($request);

        $plans = ProductPlan::query()
            ->with(['product_plan_category.product:id,slug,product_name', 'product_plan_category.network:id,network_name'])
            ->where('visibility', '1')
            ->where('public_visibility', '1')
            ->where('active_status', '1')
            ->whereHas('product_plan_category', fn ($query) => $query
                ->where('visibility', '1')
                ->whereHas('product', fn ($product) => $product
                    ->whereIn('slug', ['data', 'airtime', 'cable_subscription', 'utility_bills'])
                    ->where('visibility', '1')
                    ->where('active_status', '1')))
            ->orderBy('product_plan_category_id')
            ->orderByRaw('CAST(default_selling_price AS DECIMAL(12,2))')
            ->get()
            ->map(function (ProductPlan $plan) use ($pricingService, $user): array {
                $category = $plan->product_plan_category;
                $pricing = $pricingService->resolve($user, $plan);

                return [
                    'id' => $plan->api_id,
                    'service' => $this->publicService($category->product->slug),
                    'name' => $plan->product_plan_name,
                    'network' => $category->network?->network_name,
                    'category' => $category->product_plan_category_name,
                    'price' => $pricing['price'],
                    'pricing_type' => $pricing['pricing_type'],
                    'data_size_mb' => $plan->data_size_in_mb ? (int) $plan->data_size_in_mb : null,
                    'validity_days' => $plan->validity_in_days ? (int) $plan->validity_in_days : null,
                ];
            })->values();

        return $this->success('Catalogue fetched successfully.', $plans);
    }

    public function wallet(Request $request): JsonResponse
    {
        return $this->success('Wallet fetched successfully.', [
            'currency' => 'NGN',
            'available_balance' => round((float) $this->user($request)->main_wallet, 2),
        ]);
    }

    public function validateCustomer(Request $request, BillerValidationService $billerValidation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service' => ['required', 'string', 'in:cable,electricity'],
            'plan_id' => ['required'],
            'customer_number' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), $validator->errors(), 422);
        }

        $plan = $this->availablePlan($request->input('plan_id'), $request->string('service')->toString());
        if (! $plan) {
            return $this->error('The selected plan is unavailable for this service.', ['plan_id' => ['Select a valid active plan.']], 422);
        }

        try {
            $data = $billerValidation->validate(
                $this->user($request),
                $request->string('service')->toString(),
                $plan,
                $request->string('customer_number')->toString(),
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->error(MobileDisplayMessage::clean($exception->getMessage(), 'We could not validate this customer right now.'), null, 422);
        }

        return $this->success('Customer validated successfully.', $data);
    }

    public function buyService(Request $request, ProductsService $productsService, BillerValidationService $billerValidation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service' => ['required', 'string', 'in:data,airtime,cable,electricity'],
            'plan_id' => ['required'],
            'customer_number' => ['required', 'string', 'max:50'],
            'reference' => ['required', 'string', 'max:100'],
            'amount' => ['required_if:service,airtime,electricity', 'nullable', 'numeric', 'min:50'],
            'validation_reference' => ['required_if:service,cable,electricity', 'nullable', 'string', 'max:100'],
            'validate_phone_network' => ['sometimes', 'boolean'],
        ], [
            'amount.min' => 'The amount must be at least :min.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if (in_array($request->input('service'), ['data', 'airtime'], true)
                && ! preg_match('/^0[789][01][0-9]{8}$/', (string) $request->input('customer_number'))) {
                $validator->errors()->add('customer_number', 'Provide a valid Nigerian mobile number.');
            }
        });

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), $validator->errors(), 422);
        }

        $user = $this->user($request);
        $service = $request->string('service')->toString();
        $plan = $this->availablePlan($request->input('plan_id'), $service);

        if (! $plan) {
            return $this->error('The selected plan is unavailable for this service.', ['plan_id' => ['Select a valid active plan.']], 422);
        }

        if ($existing = Transaction::where('user_id', $user->id)->where('txn_reference', $request->string('reference'))->first()) {
            $existingCustomer = $existing->phone_number ?: $existing->smart_card_number ?: $existing->metre_number;
            $samePurchase = $existing->product_plan_id === $plan->id
                && $existingCustomer === $request->string('customer_number')->toString();

            return $samePurchase
                ? $this->success('This transaction was already submitted.', $this->transactionData($existing), 200, ['idempotent_replay' => true])
                : $this->error('This reference has already been used for a different transaction.', ['reference' => ['Use a new unique reference.']], 409);
        }

        $validation = null;
        if (in_array($service, ['cable', 'electricity'], true)) {
            $validation = $billerValidation->resolve(
                $request->string('validation_reference')->toString(), $user, $service, $plan,
                $request->string('customer_number')->toString(),
            );
            if (! $validation) {
                return $this->error('The validation reference is invalid, expired or does not match this purchase.', [
                    'validation_reference' => ['Validate the customer again before purchasing.'],
                ], 422);
            }
        }

        $payload = [
            'network_id' => $plan->product_plan_category->network?->id,
            'product_id' => $plan->product_plan_category->product->id,
            'reference' => $request->string('reference')->toString(),
            'phone_number' => $request->string('customer_number')->toString(),
            'product_plan_category_id' => $plan->product_plan_category_id,
            'product_plan_id' => $plan->id,
            'pin' => $user->pin,
            'wallet_category' => 'main_wallet',
            'validatephonenetwork' => $request->boolean('validate_phone_network', true) ? 1 : 0,
            'user_id' => $user->id,
            'user' => $user,
        ];

        if (in_array($service, ['airtime', 'electricity'], true)) {
            $payload['amount'] = round((float) $request->input('amount'), 2);
            $payload['actual_amount'] = $payload['amount'];
        }

        if ($service === 'cable') {
            $payload += [
                'smart_card_number' => $request->string('customer_number')->toString(),
                'validation_customer_name' => (string) ($validation['customer_name'] ?? ''),
                'cable_product_plan_category_id' => $plan->product_plan_category_id,
                'cable_product_plan_id' => $plan->id,
                'no_of_slots' => '1',
            ];
        }

        if ($service === 'electricity') {
            $payload += [
                'metre_number' => $request->string('customer_number')->toString(),
                'validation_extra_info' => (string) ($validation['extra_info'] ?? ''),
                'validated_address' => $validation['address'] ?? null,
                'electricity_product_plan_category_id' => $plan->product_plan_category_id,
                'electricity_product_plan_id' => $plan->id,
                'no_of_slots' => '1',
            ];
        }

        $startedAt = hrtime(true);

        Log::info('oresamsub.purchase.started', [
            'reference' => $payload['reference'],
            'service' => $service,
        ]);

        try {
            $result = match ($service) {
                'data' => $productsService->buy_data_service_one_api($payload),
                'airtime' => $productsService->buy_airtime_service($payload),
                'cable' => $productsService->buy_cable_service($payload),
                'electricity' => $productsService->buy_electricity_service($payload),
            };
        } catch (Throwable $exception) {
            report($exception);

            Log::warning('oresamsub.purchase.failed', [
                'reference' => $payload['reference'],
                'service' => $service,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return $this->error('The service provider could not process this transaction. No duplicate retry was made.', null, 503);
        } finally {
            Log::info('oresamsub.purchase.finished', [
                'reference' => $payload['reference'],
                'service' => $service,
                'duration_seconds' => round((hrtime(true) - $startedAt) / 1_000_000_000, 3),
            ]);
        }

        $successful = (int) ($result['status'] ?? -1) === 1
            || strtolower((string) ($result['Status'] ?? '')) === 'successful';
        $status = $successful ? 'successful' : $this->publicStatus($result['Status'] ?? $result['status'] ?? -1);
        $data = [
            'reference' => $request->string('reference')->toString(),
            'status' => $status,
            'service' => $service,
            'customer_number' => $request->string('customer_number')->toString(),
            'amount' => isset($result['plan_amount']) ? round((float) $result['plan_amount'], 2) : ($payload['amount'] ?? null),
            'balance_before' => isset($result['balance_before']) ? round((float) $result['balance_before'], 2) : null,
            'balance_after' => isset($result['balance_after']) ? round((float) $result['balance_after'], 2) : null,
            'token' => $service === 'electricity' ? ($result['token'] ?? null) : null,
        ];

        $message = MobileDisplayMessage::clean($result['user_message'] ?? $result['message'] ?? null,
            $successful ? 'Transaction processed successfully.' : 'Transaction could not be completed.');

        if ($service === 'airtime' && preg_match('/\bpending\b/i', $message)) {
            $message = 'Transaction is being processed.';
        }

        return $successful
            ? $this->success($message, $data)
            : $this->error($message, null, $status === 'processing' || $status === 'pending' ? 202 : 422, $data);
    }

    public function transaction(Request $request, string $reference): JsonResponse
    {
        $transaction = Transaction::where('user_id', $this->user($request)->id)
            ->where('txn_reference', $reference)->first();

        if (! $transaction) {
            return $this->error('Transaction not found.', ['reference' => ['No transaction matches this reference.']], 404);
        }

        return $this->success('Transaction fetched successfully.', $this->transactionData($transaction));
    }

    private function transactionData(Transaction $transaction): array
    {
        return [
            'reference' => $transaction->txn_reference,
            'status' => $this->publicStatus($transaction->status),
            'service' => $this->publicService($transaction->transaction_category),
            'customer_number' => $transaction->phone_number ?: $transaction->smart_card_number ?: $transaction->metre_number,
            'amount' => round((float) $transaction->amount, 2),
            'balance_before' => round((float) $transaction->balance_before, 2),
            'balance_after' => round((float) $transaction->balance_after, 2),
            'created_at' => $transaction->created_at,
        ];
    }

    private function availablePlan(mixed $apiId, string $service): ?ProductPlan
    {
        $slug = match ($service) {
            'cable' => 'cable_subscription',
            'electricity' => 'utility_bills',
            default => $service,
        };

        return ProductPlan::query()
            ->with(['product_plan_category.product', 'product_plan_category.network'])
            ->where('api_id', $apiId)
            ->where('visibility', '1')
            // ->where('active_status', '1')
            // ->whereHas('product_plan_category', fn ($query) => $query->where('visibility', '1')->whereHas(
            //     'product', fn ($product) => $product->where('slug', $slug)->where('visibility', '1')->where('active_status', '1')
            // ))
            ->first();
    }

    private function publicService(?string $service): ?string
    {
        return match ($service) {
            'cable_subscription' => 'cable',
            'utility_bills' => 'electricity',
            default => $service,
        };
    }

    private function publicStatus(mixed $status): string
    {
        return match ((string) $status) {
            '1', 'success', 'successful' => 'successful',
            '2', 'refunded', 'reversed' => 'reversed',
            '3', 'processing' => 'processing',
            '0', 'pending' => 'pending',
            default => 'failed',
        };
    }

    private function user(Request $request): User
    {
        return $request->attributes->get('api_user');
    }

    private function success(string $message, mixed $data = null, int $status = 200, mixed $meta = null): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data, 'meta' => $meta, 'errors' => null], $status);
    }

    private function error(string $message, mixed $errors = null, int $status = 400, mixed $data = null): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'data' => $data, 'meta' => null, 'errors' => $errors], $status);
    }
}
