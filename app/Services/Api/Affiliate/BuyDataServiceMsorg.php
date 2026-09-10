<?php

namespace App\Services\Api\Affiliate;

use App\Models\AffiliateDataPurchaseRequest;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletLog;
use App\Services\Pricing\CustomerProductPricingService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BuyDataServiceMsorg
{
    public function __construct(
        private readonly CustomerProductPricingService $pricing,
        private readonly MsorgDataProviderExecutor $provider,
    ) {}

    public function purchase(User $user, array $payload): array
    {
        $payload = $this->normalize($payload);
        $fingerprint = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));

        if ($existing = $this->existing($user, $payload['reference'])) {
            return $this->replay($existing, $fingerprint);
        }

        $plan = $this->plan($payload);
        if (! $plan) {
            return $this->failure('The selected data plan is unavailable for this network.', 422);
        }

        $price = number_format((float) $this->pricing->resolve($user, $plan)['price'], 2, '.', '');

        try {
            [$requestRecord, $transaction] = DB::transaction(function () use ($user, $payload, $fingerprint, $plan, $price): array {
                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
                $balanceBefore = number_format((float) $lockedUser->main_wallet, 2, '.', '');
                $transactionReference = $this->transactionReference($lockedUser, $payload['reference']);
                $transaction = Transaction::create([
                    'transaction_category' => 'data', 'transaction_route' => 'api',
                    'user_id' => $lockedUser->id, 'txn_reference' => $transactionReference,
                    'wallet_category' => 'main_wallet', 'product_plan_id' => $plan->id,
                    'phone_number' => $payload['mobile_number'], 'amount' => $price,
                    'service_charge' => $price, 'discounted_amount' => $price,
                    'status' => 0, 'balance_before' => $balanceBefore, 'balance_after' => $balanceBefore,
                    'description' => 'Purchase of data', 'user_screen_message' => 'Transaction processing...',
                    'admin_screen_message' => 'MSOrg API transaction initialized', 'upline_commission' => 0,
                ]);
                $record = AffiliateDataPurchaseRequest::create([
                    'user_id' => $lockedUser->id, 'reference' => $payload['reference'],
                    'request_fingerprint' => $fingerprint, 'transaction_id' => $transaction->id,
                ]);

                if ($this->cents($balanceBefore) < $this->cents($price)) {
                    $body = $this->body($payload, $plan, $transaction, 'failed', 'Insufficient wallet balance.', $balanceBefore, $balanceBefore, $price);
                    $transaction->update(['status' => -1, 'user_screen_message' => $body['apiresponse'], 'admin_screen_message' => 'Insufficient wallet balance']);
                    $record->update(['status' => 'failed', 'response_status' => 422, 'response_body' => $body]);

                    return [$record->fresh(), $transaction->fresh()];
                }

                $balanceAfter = number_format((float) $balanceBefore - (float) $price, 2, '.', '');
                $lockedUser->update(['main_wallet' => $balanceAfter]);
                $transaction->update(['balance_after' => $balanceAfter]);
                WalletLog::create([
                    'user_id' => $lockedUser->id, 
                    'transaction_category' => 'DATA_FROM_MAIN_WALLET',
                    'balance_before' => $balanceBefore, 'balance_after' => $balanceAfter,
                    'transaction_id' => $transaction->id, 'action_by' => $lockedUser->id,
                    'description' => 'MSOrg API data purchase reservation',
                ]);

                return [$record, $transaction->fresh()];
            });
        } catch (QueryException $exception) {
            if ($existing = $this->existing($user, $payload['reference'])) {
                return $this->replay($existing, $fingerprint);
            }
            throw $exception;
        }

        if ($requestRecord->response_body) {
            return ['status' => $requestRecord->response_status, 'body' => $requestRecord->response_body];
        }

        try {
            $providerResponse = $this->provider->execute($user, $plan, $payload);
        } catch (Throwable $exception) {
            report($exception);
            Log::warning('oresamsub.msorg_data.provider_error', ['reference' => $payload['reference'], 'exception' => $exception::class]);

            return $this->refund($requestRecord, $transaction, $payload, $plan, $price, 'Provider temporarily unavailable.', 503);
        }

        $safeProviderResponse = [
            'status' => (int) ($providerResponse['status'] ?? -1),
            'user_message' => (string) ($providerResponse['user_message'] ?? ''),
            'provider_id' => $providerResponse['provider_id'] ?? null,
            'provider_name' => $providerResponse['provider_name'] ?? null,
            'provider_slug' => $providerResponse['provider_slug'] ?? null,
            'provider_plan_id' => $providerResponse['provider_plan_id'] ?? null,
        ];
        $transaction->update(array_filter([
            'automation_id' => $providerResponse['provider_id'] ?? null,
            'admin_screen_message' => $providerResponse['admin_message'] ?? null,
        ], fn ($value) => $value !== null && $value !== ''));

        if ((int) ($providerResponse['status'] ?? -1) !== 1) {
            $message = trim((string) ($providerResponse['user_message'] ?? 'Data processing failed.')) ?: 'Data processing failed.';
            Log::channel('single')->warning('oresamsub.msorg_data.provider_rejected', [
                'reference' => $payload['reference'],
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_api_id' => $plan->api_id,
                'network' => $payload['network'],
                'status' => $providerResponse['status'] ?? null,
                'user_message' => $providerResponse['user_message'] ?? null,
                'admin_message' => $providerResponse['admin_message'] ?? null,
                'provider_response' => $this->safeLogData($providerResponse),
            ]);

            return $this->refund($requestRecord, $transaction, $payload, $plan, $price, $message, 502, $safeProviderResponse);
        }

        $message = trim((string) ($providerResponse['user_message'] ?? 'Transaction processed successfully.')) ?: 'Transaction processed successfully.';
        $body = $this->body($payload, $plan, $transaction, 'successful', $message, $transaction->balance_before, $transaction->balance_after, $price, true);
        DB::transaction(function () use ($requestRecord, $transaction, $body, $safeProviderResponse): void {
            $transaction->update(['status' => 1, 'user_screen_message' => $body['apiresponse']]);
            $requestRecord->update(['status' => 'successful', 'response_status' => 200, 'response_body' => $body, 'provider_response' => $safeProviderResponse]);
        });

        return ['status' => 200, 'body' => $body];
    }

    private function normalize(array $payload): array
    {
        return [
            'network' => (string) $payload['network'], 'mobile_number' => trim($payload['mobile_number']),
            'plan' => (string) $payload['plan'], 'Ported_number' => (bool) $payload['Ported_number'],
            'reference' => trim($payload['reference']), 'wallet_category' => 'main_wallet',
            'validatephonenetwork' => (bool) ($payload['validatephonenetwork'] ?? true),
        ];
    }

    private function plan(array $payload): ?ProductPlan
    {
        return ProductPlan::query()->with(['automation', 'product_plan_category.product', 'product_plan_category.network'])
            ->where('api_id', $payload['plan'])->where('visibility', '1')->where('public_visibility', '1')->where('active_status', '1')
            ->whereHas('product_plan_category', fn ($query) => $query->where('visibility', '1')
                ->whereHas('product', fn ($product) => $product->where('slug', 'data')->where('visibility', '1')->where('active_status', '1'))
                ->whereHas('network', fn ($network) => $network->where('api_id', $payload['network'])->where('visibility', '1')))
            ->first();
    }

    private function existing(User $user, string $reference): ?AffiliateDataPurchaseRequest
    {
        return AffiliateDataPurchaseRequest::where('user_id', $user->id)->where('reference', $reference)->first();
    }

    private function replay(AffiliateDataPurchaseRequest $record, string $fingerprint): array
    {
        if (! hash_equals($record->request_fingerprint, $fingerprint)) {
            return $this->failure('This reference has already been used for a different transaction.', 409);
        }
        if (! $record->response_body) {
            return $this->failure('This transaction is still processing.', 409);
        }

        $body = $record->response_body;
        $status = strtolower((string) ($body['Status'] ?? $record->status));
        $message = $status === 'successful'
            ? 'Existing successful transaction returned. No new purchase was made.'
            : 'Existing failed transaction returned. No new purchase was attempted.';
        $body['idempotent_replay'] = true;
        $body['provider_called'] = false;
        $body['apiresponse'] = $message;
        $body['api_response'] = $message;

        return ['status' => $record->response_status, 'body' => $body];
    }

    private function refund(AffiliateDataPurchaseRequest $record, Transaction $transaction, array $payload, ProductPlan $plan, string $price, string $message, int $httpStatus, array $providerResponse = []): array
    {
        $body = DB::transaction(function () use ($record, $transaction, $payload, $plan, $price, $message, $httpStatus, $providerResponse): array {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($record->user_id);
            $beforeRefund = number_format((float) $lockedUser->main_wallet, 2, '.', '');
            $afterRefund = number_format((float) $beforeRefund + (float) $price, 2, '.', '');
            $lockedUser->update(['main_wallet' => $afterRefund]);
            $body = $this->body($payload, $plan, $transaction, 'failed', $message, $transaction->balance_before, $transaction->balance_before, $price, true);
            $transaction->update([
                'status' => -1,
                'balance_after' => $transaction->balance_before,
                'user_screen_message' => $message,
                'admin_screen_message' => $transaction->admin_screen_message ?: 'Provider did not confirm delivery',
            ]);
            WalletLog::create([
                'user_id' => $lockedUser->id, 'transaction_category' => 'DATA_REFUND_TO_MAIN_WALLET',
                'balance_before' => $beforeRefund, 'balance_after' => $afterRefund,
                'transaction_id' => $transaction->id, 'action_by' => $lockedUser->id,
                'description' => 'Refund for failed MSOrg API data purchase',
            ]);
            $record->update(['status' => 'failed', 'response_status' => $httpStatus, 'response_body' => $body, 'provider_response' => $providerResponse]);

            return $body;
        });

        return ['status' => $httpStatus, 'body' => $body];
    }

    private function transactionReference(User $user, string $reference): string
    {
        return Transaction::where('txn_reference', $reference)->exists()
            ? 'MSORG-'.substr(str_replace('-', '', $user->id), 0, 8).'-'.substr(hash('sha256', $reference), 0, 24)
            : $reference;
    }

    private function body(array $payload, ProductPlan $plan, Transaction $transaction, string $status, string $message, mixed $before, mixed $after, string $price, bool $providerCalled = false): array
    {
        return [
            'id' => $transaction->id, 'ident' => $payload['reference'], 'payment_medium' => 'MAIN WALLET',
            'duration' => ((int) $plan->validity_in_days).' DAYS', 'plan_type' => $plan->product_plan_category->product_plan_category_name,
            'network' => $payload['network'], 'apiresponse' => $message, 'api_response' => $message,
            'balance_before' => number_format((float) $before, 2, '.', ''), 'balance_after' => number_format((float) $after, 2, '.', ''),
            'mobile_number' => $payload['mobile_number'], 'plan' => is_numeric($payload['plan']) ? (int) $payload['plan'] : $payload['plan'],
            'Status' => $status, 'plan_network' => $plan->product_plan_category->network->network_name,
            'plan_name' => $plan->product_plan_name, 'plan_amount' => $price,
            'create_date' => $transaction->created_at, 'Ported_number' => $payload['Ported_number'],
            'idempotent_replay' => false, 'provider_called' => $providerCalled,
        ];
    }

    private function failure(string $message, int $status): array
    {
        return ['status' => $status, 'body' => ['Status' => 'failed', 'apiresponse' => $message, 'api_response' => $message]];
    }

    private function cents(string $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function safeLogData(mixed $value): mixed
    {
        if (! is_array($value)) {
            return is_scalar($value) || $value === null ? $value : get_debug_type($value);
        }

        $hidden = ['token', 'api_key', 'api_secret', 'secret', 'password', 'authorization', 'credential'];
        $result = [];
        foreach ($value as $key => $item) {
            $keyString = strtolower((string) $key);
            $result[$key] = collect($hidden)->contains(fn (string $needle) => str_contains($keyString, $needle))
                ? '[redacted]'
                : $this->safeLogData($item);
        }

        return $result;
    }
}
