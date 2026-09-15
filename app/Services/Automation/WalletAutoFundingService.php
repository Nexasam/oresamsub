<?php

namespace App\Services\Automation;

use App\Models\AutomationWalletFunding;
use App\Services\Securewave\SecurewaveClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletAutoFundingService
{
    public function __construct(
        private readonly SecurewaveClient $securewave,
        private readonly AutomationBalanceResolver $balanceResolver,
    ) {}

    public function run(): void
    {
        AutomationWalletFunding::query()
            ->with('automation')
            ->where('automatic_funding', true)
            ->where('active', 'yes')
            ->chunkById(50, function ($fundings): void {
                foreach ($fundings as $funding) {
                    $this->process($funding);
                }
            });
    }

    public function getSecurewaveMerchantBalance(): array
    {
        $result = $this->securewave->merchantBalance();

        return [
            'status' => $result['ok'] ? 1 : -1,
            'message' => $result['message'],
            'balance' => $result['balance'] ?? 0,
            'data' => $result['data'],
        ];
    }

    public function process(AutomationWalletFunding $funding): array
    {
        if (! $funding->automatic_funding || $funding->active !== 'yes') {
            return $this->skipped('Automatic funding is disabled.');
        }

        $this->balanceResolver->sync($funding);
        $funding->refresh();

        if ((float) $funding->last_balance > (float) $funding->threshold) {
            return $this->skipped('The automation balance is above its threshold.');
        }

        return $this->fund($funding, (float) $funding->amount_to_fund, 'automatic');
    }

    public function fund(AutomationWalletFunding $funding, float $amount, string $source = 'manual'): array
    {
        if ($amount <= 0) {
            return $this->fail($funding, 'Funding amount must be greater than zero.');
        }

        if (blank($funding->linked_customer_email) || ! $funding->securewave_customer_created_at) {
            return $this->fail($funding, 'Create the Securewave customer before funding this automation.');
        }

        if (! $funding->securewave_bank_info_saved_at) {
            return $this->fail($funding, 'Save the customer bank information on Securewave before funding this automation.');
        }

        return DB::transaction(function () use ($funding, $amount, $source): array {
            $locked = AutomationWalletFunding::query()->lockForUpdate()->findOrFail($funding->id);
            $merchant = $this->securewave->merchantBalance();

            if (! $merchant['ok']) {
                return $this->fail($locked, $merchant['message']);
            }

            if ($merchant['balance'] === null || $amount > $merchant['balance']) {
                return $this->fail($locked, 'Insufficient Securewave merchant balance for this funding amount.');
            }

            $result = $this->securewave->fundCustomer($locked->linked_customer_email, $amount);

            if (! $result['ok']) {
                return $this->fail($locked, $result['message']);
            }

            $newBalance = $result['customer_balance'] ?? ((float) $locked->last_balance + $amount);
            $locked->forceFill([
                'last_balance' => $newBalance,
                'balance_source' => 'securewave_funding',
                'balance_source_transaction_id' => null,
                'last_balance_synced_at' => now(),
                'last_funded_at' => now(),
                'last_error' => null,
            ])->save();

            Log::info('Automation wallet funded through Securewave.', [
                'automation_id' => $locked->automation_id,
                'amount' => $amount,
                'source' => $source,
            ]);

            return [
                'ok' => true,
                'skipped' => false,
                'message' => $result['message'],
                'balance' => $newBalance,
            ];
        }, 3);
    }

    private function fail(AutomationWalletFunding $funding, string $message): array
    {
        $funding->forceFill(['last_error' => $message])->save();

        Log::warning('Automation wallet funding was not completed.', [
            'automation_id' => $funding->automation_id,
            'message' => $message,
        ]);

        return ['ok' => false, 'skipped' => false, 'message' => $message];
    }

    private function skipped(string $message): array
    {
        return ['ok' => false, 'skipped' => true, 'message' => $message];
    }
}
