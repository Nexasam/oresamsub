<?php

namespace App\Observers;

use App\Jobs\SendWhatsappTransactionAlert;
use App\Models\Transaction;

class TransactionWhatsappNotificationObserver
{
    public function created(Transaction $transaction): void
    {
        $this->dispatch($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        if ($transaction->wasChanged('status')) {
            $this->dispatch($transaction);
        }
    }

    private function dispatch(Transaction $transaction): void
    {
        $notificationType = match ((string) $transaction->status) {
            '-1' => 'failed',
            '2' => 'refunded',
            default => null,
        };

        if (! $notificationType) {
            return;
        }

        SendWhatsappTransactionAlert::dispatch(
            $transaction->id,
            $notificationType,
        )->afterCommit();
    }
}
