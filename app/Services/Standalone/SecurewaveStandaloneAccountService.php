<?php

namespace App\Services\Standalone;

use App\Exceptions\StandaloneAccountProvisioningException;
use App\Models\FundingOption;
use App\Models\StandaloneVirtualAccount;
use App\Models\StandaloneWebsite;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SecurewaveStandaloneAccountService
{
    public function provision(StandaloneWebsite $site): array
    {
        if ($existing = $site->virtualAccount()->first()) {
            return [$existing, false];
        }
        $option = FundingOption::where('slug', 'securewaveng')->first();
        if (! $option || ! $option->api_public_key || ! $option->api_secret_key || ! $option->contract_code) {
            $configuration = [
                'funding_option' => (bool) $option,
                'api_public_key' => (bool) $option?->api_public_key,
                'api_secret_key' => (bool) $option?->api_secret_key,
                'contract_code' => (bool) $option?->contract_code,
            ];
            Log::warning('Standalone Kolomoni provisioning configuration is incomplete.', $this->context($site) + [
                'missing_configuration' => array_keys(array_filter($configuration, fn (bool $configured): bool => ! $configured)),
            ]);

            throw new StandaloneAccountProvisioningException('SecureWave account generation is not configured.');
        }
        try {
            $response = Http::acceptJson()->asJson()->withToken($option->api_secret_key)->withHeaders(['x-api-key' => $option->api_public_key])->connectTimeout(10)->timeout(30)->post('https://securewaveng.com/api/virtual_accounts/generate', [
                'email' => $site->email, 'first_name' => $site->contact_first_name, 'last_name' => $site->contact_last_name, 'phone_number' => $site->phone,
                'bank_code' => [1], 'business_id' => $option->contract_code, 'account_type' => 'static', 'id_type' => 'bvn', 'id_number' => $site->bvn,
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('SecureWave connection failed during standalone Kolomoni provisioning.', $this->context($site) + [
                'exception' => $exception->getMessage(),
            ]);

            throw new StandaloneAccountProvisioningException('SecureWave could not be reached.');
        }
        $account = collect($response->json('data', []))->first(fn ($item) => (string) ($item['bank_code'] ?? '') === '1' && (int) ($item['status'] ?? 0) === 1);
        if ($response->failed() || $response->json('status') !== true || ! $account) {
            Log::warning('SecureWave rejected standalone Kolomoni provisioning.', $this->context($site) + [
                'http_status' => $response->status(),
                'provider_status' => $response->json('status'),
                'provider_message' => $response->json('message'),
                'provider_errors' => $response->json('errors'),
                'has_eligible_kolomoni_account' => (bool) $account,
            ]);

            throw new StandaloneAccountProvisioningException('SecureWave could not generate the Kolomoni account.');
        }
        $model = StandaloneVirtualAccount::updateOrCreate(['account_reference' => $account['account_reference']], [
            'standalone_website_id' => $site->id, 'funding_option_id' => $option->id, 'account_number' => $account['account_number'], 'bank_code' => '1',
            'bank_name' => $account['account_bank'] ?? 'Kolomoni', 'account_name' => $account['account_name'] ?? null, 'account_email' => $account['account_email'] ?? null,
            'provider_status' => (string) $account['status'], 'provider_metadata' => ['bank_code' => '1'],
        ]);
        Log::info('Standalone Kolomoni account provisioned successfully.', $this->context($site) + [
            'account_reference' => $model->account_reference,
            'bank_code' => $model->bank_code,
        ]);

        return [$model, true];
    }

    private function context(StandaloneWebsite $site): array
    {
        return [
            'standalone_id' => $site->id,
            'standalone_slug' => $site->slug,
            'standalone_email' => $site->email,
        ];
    }
}
