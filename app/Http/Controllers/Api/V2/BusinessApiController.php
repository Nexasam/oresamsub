<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Support\MobileDisplayMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BusinessApiController extends Controller
{
    public function catalogue(Request $request): JsonResponse
    {
        $user = $this->user($request);
        $level = min(7, max(1, (int) ($user->user_plan?->plan_level ?? 1)));
        $priceField = "user_level_{$level}_selling_price";

        $plans = ProductPlan::query()
            ->with(['product_plan_category.product:id,slug,product_name', 'product_plan_category.network:id,network_name'])
            ->where('visibility', '1')
            ->where('public_visibility', '1')
            ->where('active_status', '1')
            ->whereHas('product_plan_category', fn ($query) => $query
                ->where('visibility', '1')
                ->whereHas('product', fn ($product) => $product
                    ->whereIn('slug', ['data', 'airtime'])
                    ->where('visibility', '1')
                    ->where('active_status', '1')))
            ->orderBy('product_plan_category_id')
            ->orderByRaw('CAST(default_selling_price AS DECIMAL(12,2))')
            ->get()
            ->map(function (ProductPlan $plan) use ($priceField): array {
                $category = $plan->product_plan_category;

                return [
                    'id' => $plan->api_id,
                    'service' => $category->product->slug,
                    'name' => $plan->product_plan_name,
                    'network' => $category->network?->network_name,
                    'category' => $category->product_plan_category_name,
                    'price' => round((float) ($plan->{$priceField} ?: $plan->default_selling_price), 2),
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

    public function buyService(Request $request, ProductsService $productsService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service' => ['required', 'string', 'in:data,airtime'],
            'plan_id' => ['required'],
            'customer' => ['required', 'regex:/^0[789][01][0-9]{8}$/'],
            'reference' => ['required', 'string', 'max:100'],
            'amount' => ['required_if:service,airtime', 'nullable', 'numeric', 'min:50'],
            'validate_phone_network' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->error('Please check the provided information.', $validator->errors(), 422);
        }

        $user = $this->user($request);
        $plan = ProductPlan::query()
            ->with(['product_plan_category.product', 'product_plan_category.network'])
            ->where('api_id', $request->input('plan_id'))
            ->where('visibility', '1')->where('public_visibility', '1')->where('active_status', '1')
            ->whereHas('product_plan_category', fn ($query) => $query->where('visibility', '1')->whereHas(
                'product', fn ($product) => $product->where('slug', $request->string('service'))->where('visibility', '1')->where('active_status', '1')
            ))->first();

        if (! $plan) {
            return $this->error('The selected plan is unavailable for this service.', ['plan_id' => ['Select a valid active plan.']], 422);
        }

        if ($existing = Transaction::where('user_id', $user->id)->where('txn_reference', $request->string('reference'))->first()) {
            $samePurchase = $existing->transaction_category === $request->string('service')->toString()
                && $existing->product_plan_id === $plan->id
                && $existing->phone_number === $request->string('customer')->toString();

            return $samePurchase
                ? $this->success('This transaction was already submitted.', $this->transactionData($existing), 200, ['idempotent_replay' => true])
                : $this->error('This reference has already been used for a different transaction.', ['reference' => ['Use a new unique reference.']], 409);
        }

        $payload = [
            'network_id' => $plan->product_plan_category->network?->id,
            'product_id' => $plan->product_plan_category->product->id,
            'reference' => $request->string('reference')->toString(),
            'phone_number' => $request->string('customer')->toString(),
            'product_plan_category_id' => $plan->product_plan_category_id,
            'product_plan_id' => $plan->id,
            'pin' => $user->pin,
            'wallet_category' => 'main_wallet',
            'validatephonenetwork' => $request->boolean('validate_phone_network', true) ? 1 : 0,
            'user_id' => $user->id,
            'user' => $user,
        ];

        if ($request->string('service')->toString() === 'airtime') {
            $payload['amount'] = round((float) $request->input('amount'), 2);
            $payload['actual_amount'] = $payload['amount'];
        }

        try {
            $result = $request->string('service')->toString() === 'data'
                ? $productsService->buy_data_service_one_api($payload)
                : $productsService->buy_airtime_service($payload);
        } catch (Throwable $exception) {
            report($exception);

            return $this->error('The service provider could not process this transaction. No duplicate retry was made.', null, 503);
        }

        $successful = (int) ($result['status'] ?? -1) === 1
            || strtolower((string) ($result['Status'] ?? '')) === 'successful';
        $status = $successful ? 'successful' : $this->publicStatus($result['Status'] ?? $result['status'] ?? -1);
        $data = [
            'reference' => $request->string('reference')->toString(),
            'status' => $status,
            'service' => $request->string('service')->toString(),
            'customer' => $request->string('customer')->toString(),
            'amount' => isset($result['plan_amount']) ? round((float) $result['plan_amount'], 2) : ($payload['amount'] ?? null),
            'balance_before' => isset($result['balance_before']) ? round((float) $result['balance_before'], 2) : null,
            'balance_after' => isset($result['balance_after']) ? round((float) $result['balance_after'], 2) : null,
        ];

        $message = MobileDisplayMessage::clean($result['user_message'] ?? $result['message'] ?? null,
            $successful ? 'Transaction processed successfully.' : 'Transaction could not be completed.');

        return $successful
            ? $this->success($message, $data)
            : $this->error($message, null, $status === 'processing' || $status === 'pending' ? 202 : 422, $data);
    }

    public function transaction(Request $request, string $reference): JsonResponse
    {
        $transaction = Transaction::where('user_id', $this->user($request)->id)
            ->where('txn_reference', $reference)->firstOrFail();

        return $this->success('Transaction fetched successfully.', $this->transactionData($transaction));
    }

    private function transactionData(Transaction $transaction): array
    {
        return [
            'reference' => $transaction->txn_reference,
            'status' => $this->publicStatus($transaction->status),
            'service' => $transaction->transaction_category,
            'customer' => $transaction->phone_number ?: $transaction->smart_card_number ?: $transaction->metre_number,
            'amount' => round((float) $transaction->amount, 2),
            'balance_before' => round((float) $transaction->balance_before, 2),
            'balance_after' => round((float) $transaction->balance_after, 2),
            'created_at' => $transaction->created_at,
        ];
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
