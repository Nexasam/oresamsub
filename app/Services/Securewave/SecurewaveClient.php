<?php

namespace App\Services\Securewave;

use App\Models\FundingOption;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class SecurewaveClient
{
    public function merchantBalance(): array
    {
        return $this->request('get', config('services.securewave.balance_url'));
    }

    public function createCustomer(
        string $firstName,
        string $lastName,
        string $email,
        string $phoneNumber,
        string $bankCode,
        string $externalCustomerId
    ): array
    {
        $option = $this->option();

        if (! $option) {
            return $this->failure('Securewave credentials are not configured.');
        }

        if (blank($option->contract_code) || blank($option->virtual_account_id_number)) {
            return $this->failure('Securewave business ID and shared BVN must be configured in Funding Options.');
        }

        $result = $this->request('post', config('services.securewave.customer_create_url'), [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phoneNumber,
            'bank_code' => [(int) $bankCode],
            'business_id' => $option->contract_code,
            'account_type' => 'static',
            'id_type' => 'bvn',
            'id_number' => $option->virtual_account_id_number,
            'metadata' => [
                'external_customer_id' => $externalCustomerId,
                'source' => 'automation-funding',
            ],
        ]);

        if (! $result['ok']) {
            return $result;
        }

        $accounts = data_get($result, 'data.data', []);
        $account = collect(is_array($accounts) ? $accounts : [])
            ->first(fn ($item) => is_array($item)
                && (string) ($item['bank_code'] ?? '') === (string) $bankCode
                && (string) ($item['status'] ?? '1') === '1');

        if (! is_array($account)) {
            return $this->failure('Securewave did not return the requested virtual account.', $result['data']);
        }

        $result['account'] = $account;
        $result['customer_reference'] = $account['account_reference']
            ?? $account['customer_reference']
            ?? $account['reference']
            ?? null;

        return $result;
    }

    public function fundCustomer(string $email, float $amount): array
    {
        return $this->request('post', config('services.securewave.customer_fund_url'), [
            'customer_email' => $email,
            'amount' => $amount,
            'narration' => 'Automation wallet funding',
        ]);
    }

    public function saveCustomerBankInfo(
        string $email,
        string $bankName,
        string $accountName,
        string $bankCode,
        string $accountNumber
    ): array {
        return $this->request('post', config('services.securewave.customer_bank_info_url'), [
            'customer_email' => $email,
            'bank_name' => $bankName,
            'account_name' => $accountName,
            'bank_code' => $bankCode,
            'account_number' => $accountNumber,
        ]);
    }

    private function request(string $method, ?string $url, array $payload = []): array
    {
        $option = $this->option();

        if (! $option || blank($option->api_public_key) || blank($option->api_secret_key)) {
            return $this->failure('Securewave credentials are not configured.');
        }

        if (blank($url)) {
            return $this->failure('Securewave endpoint is not configured.');
        }

        try {
            $request = $this->http($option);
            $response = $method === 'get'
                ? $request->get($url)
                : $request->post($url, $payload);

            return $this->normalize($response);
        } catch (ConnectionException $exception) {
            return $this->failure('Could not connect to Securewave.');
        } catch (Throwable $exception) {
            report($exception);

            return $this->failure('Securewave request failed.');
        }
    }

    private function option(): ?FundingOption
    {
        return FundingOption::query()->where('slug', 'securewaveng')->first();
    }

    private function http(FundingOption $option): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withToken($option->api_secret_key)
            ->withHeaders(['x-api-key' => $option->api_public_key])
            ->connectTimeout(10)
            ->timeout(30);
    }

    private function normalize(Response $response): array
    {
        $body = $response->json();
        $ok = $response->successful()
            && is_array($body)
            && filter_var($body['status'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $ok) {
            return $this->failure(
                is_array($body) ? (string) ($body['message'] ?? 'Securewave rejected the request.') : 'Securewave returned an invalid response.',
                is_array($body) ? $body : []
            );
        }

        return [
            'ok' => true,
            'message' => (string) ($body['message'] ?? 'Successful'),
            'data' => $body,
            'balance' => $this->numericValue($body, ['data.balance', 'balance']),
            'customer_balance' => $this->numericValue($body, ['data.balance_after', 'data.balance', 'balance_after', 'balance']),
            'customer_reference' => data_get($body, 'data.customer_reference')
                ?? data_get($body, 'data.customer_id')
                ?? data_get($body, 'data.reference'),
        ];
    }

    private function numericValue(array $body, array $paths): ?float
    {
        foreach ($paths as $path) {
            $value = data_get($body, $path);

            if (is_numeric($value)) {
                return (float) $value;
            }
        }

        return null;
    }

    private function failure(string $message, array $data = []): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'data' => $data,
            'balance' => null,
            'customer_balance' => null,
            'customer_reference' => null,
        ];
    }
}
