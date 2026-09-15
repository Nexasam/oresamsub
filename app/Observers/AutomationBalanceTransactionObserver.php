<?php

namespace App\Observers;

use App\Models\AutomationWalletFunding;
use App\Models\Transaction;
use App\Services\Automation\AutomationBalanceResolver;

class AutomationBalanceTransactionObserver
{
    public function __construct(private readonly AutomationBalanceResolver $resolver) {}

    public function created(Transaction $transaction): void
    {
        $this->sync($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        if ($transaction->wasChanged(['status', 'admin_screen_message', 'automation_id'])) {
            $this->sync($transaction);
        }
    }

    private function sync(Transaction $transaction): void
    {
        if ((string) $transaction->status !== '1' || blank($transaction->automation_id)) {
            return;
        }

        $funding = AutomationWalletFunding::query()
            ->where('automation_id', $transaction->automation_id)
            ->first();

        if ($funding) {
            $this->resolver->syncTransaction($funding, $transaction);
        }
    }
}
