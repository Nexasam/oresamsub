<?php

namespace App\Services\Standalone;

use App\Exceptions\StandaloneAccountProvisioningException;
use App\Models\FundingOption;
use App\Models\StandaloneVirtualAccount;
use App\Models\StandaloneWebsite;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SecurewaveStandaloneAccountService
{
    public function provision(StandaloneWebsite $site): array
    {
        if ($existing = $site->virtualAccount()->first()) {
            return [$existing, false];
        }
        $option = FundingOption::where('slug', 'securewaveng')->first();
        if (! $option || ! $option->api_public_key || ! $option->api_secret_key || ! $option->contract_code) {
            throw new StandaloneAccountProvisioningException('SecureWave account generation is not configured.');
        }
        try {
            $response = Http::acceptJson()->asJson()->withToken($option->api_secret_key)->withHeaders(['x-api-key' => $option->api_public_key])->connectTimeout(10)->timeout(30)->post('https://securewaveng.com/api/virtual_accounts/generate', [
                'email' => $site->email, 'first_name' => $site->contact_first_name, 'last_name' => $site->contact_last_name, 'phone_number' => $site->phone,
                'bank_code' => [1], 'business_id' => $option->contract_code, 'account_type' => 'static', 'id_type' => 'bvn', 'id_number' => $site->bvn,
            ]);
        } catch (ConnectionException) {
            throw new StandaloneAccountProvisioningException('SecureWave could not be reached.');
        }
        $account = collect($response->json('data', []))->first(fn ($item) => (string) ($item['bank_code'] ?? '') === '1' && (int) ($item['status'] ?? 0) === 1);
        if ($response->failed() || $response->json('status') !== true || ! $account) {
            throw new StandaloneAccountProvisioningException('SecureWave could not generate the Kolomoni account.');
        }
        $model = StandaloneVirtualAccount::updateOrCreate(['account_reference' => $account['account_reference']], [
            'standalone_website_id' => $site->id, 'funding_option_id' => $option->id, 'account_number' => $account['account_number'], 'bank_code' => '1',
            'bank_name' => $account['account_bank'] ?? 'Kolomoni', 'account_name' => $account['account_name'] ?? null, 'account_email' => $account['account_email'] ?? null,
            'provider_status' => (string) $account['status'], 'provider_metadata' => ['bank_code' => '1'],
        ]);

        return [$model, true];
    }
}
