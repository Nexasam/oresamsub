<?php

namespace App\Services\Automation;

use App\Models\AutomationWalletFunding;
use App\Models\Transaction;

class AutomationBalanceResolver
{
    public function resolve(AutomationWalletFunding $funding): ?array
    {
        if (blank($funding->balance_response_path)) {
            return null;
        }

        $query = Transaction::query()
            ->where('automation_id', $funding->automation_id)
            ->where('status', '1')
            ->whereNotNull('admin_screen_message');

        if (in_array($funding->balance_source, ['manual', 'securewave_funding'], true)
            && $funding->last_balance_synced_at) {
            $query->where('created_at', '>', $funding->last_balance_synced_at);
        }

        foreach ($query->latest('created_at')->latest('id')->cursor() as $transaction) {
            if ($resolved = $this->extract($funding, $transaction)) {
                return $resolved;
            }
        }

        return null;
    }

    public function sync(AutomationWalletFunding $funding): bool
    {
        $resolved = $this->resolve($funding);

        if (! $resolved) {
            if (in_array($funding->balance_source, ['manual', 'securewave_funding'], true)) {
                return false;
            }

            $funding->forceFill([
                'last_error' => 'No numeric balance was found at the configured response path in successful transactions.',
            ])->save();

            return false;
        }

        $this->apply($funding, $resolved);

        return true;
    }

    public function syncTransaction(AutomationWalletFunding $funding, Transaction $transaction): bool
    {
        $resolved = $this->extract($funding, $transaction);

        if (! $resolved) {
            $funding->forceFill([
                'last_error' => 'The latest successful transaction did not contain a numeric balance at the configured response path.',
            ])->save();

            return false;
        }

        $this->apply($funding, $resolved);

        return true;
    }

    private function extract(AutomationWalletFunding $funding, Transaction $transaction): ?array
    {
        $response = json_decode($transaction->admin_screen_message, true);

        if (! is_array($response)) {
            return null;
        }

        $balance = data_get($response, $funding->balance_response_path);

        return is_numeric($balance) ? [
            'balance' => round((float) $balance, 2),
            'transaction_id' => $transaction->id,
        ] : null;
    }

    private function apply(AutomationWalletFunding $funding, array $resolved): void
    {
        $funding->forceFill([
            'last_balance' => $resolved['balance'],
            'balance_source' => 'transaction_response',
            'balance_source_transaction_id' => $resolved['transaction_id'],
            'last_balance_synced_at' => now(),
            'last_error' => null,
        ])->save();
    }
}
