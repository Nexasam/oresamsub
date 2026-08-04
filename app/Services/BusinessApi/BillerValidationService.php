<?php

namespace App\Services\BusinessApi;

use App\Models\ProductPlan;
use App\Models\User;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

class BillerValidationService
{
    private const TTL_MINUTES = 10;

    public function validate(User $user, string $service, ProductPlan $plan, string $customer): array
    {
        $result = $service === 'cable'
            ? (new MegaSubCableTV(smart_card_number: $customer, plan_id: $plan->id, user_id: $user->id))->validateSmartCardNumber()
            : (new MegaSubElectricity(metre_number: $customer, plan_id: $plan->id, user_id: $user->id))->validateMetreNumber();

        if ((int) ($result['status'] ?? -1) !== 1) {
            throw new RuntimeException((string) ($result['user_message'] ?? $result['message'] ?? 'Customer validation failed.'));
        }

        $reference = 'VAL-'.strtoupper(Str::random(24));
        $expiresAt = now()->addMinutes(self::TTL_MINUTES);
        $payload = [
            'user_id' => $user->id,
            'service' => $service,
            'plan_id' => $plan->id,
            'customer' => $customer,
            'customer_name' => $result['name'] ?? null,
            'address' => $result['address'] ?? null,
            'extra_info' => (string) ($result['extra_info'] ?? $result['name'] ?? ''),
        ];

        Cache::put($this->cacheKey($reference), $payload, $expiresAt);

        return [
            'validation_reference' => $reference,
            'customer_name' => $payload['customer_name'],
            'address' => $payload['address'],
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    public function resolve(string $reference, User $user, string $service, ProductPlan $plan, string $customer): ?array
    {
        $payload = Cache::get($this->cacheKey($reference));
        if (! is_array($payload)) {
            return null;
        }

        return $payload['user_id'] === $user->id
            && $payload['service'] === $service
            && $payload['plan_id'] === $plan->id
            && $payload['customer'] === $customer
                ? $payload
                : null;
    }

    private function cacheKey(string $reference): string
    {
        return 'business-api:biller-validation:'.$reference;
    }
}
