<?php

namespace App\Observers;

use App\Jobs\SendMobilePushNotification;
use App\Models\Transaction;

class TransactionMobilePushObserver
{
    public function created(Transaction $transaction): void
    {
        $this->notify($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        if ($transaction->wasChanged('status')) {
            $this->notify($transaction);
        }
    }

    private function notify(Transaction $transaction): void
    {
        $status = match ((string) $transaction->status) {
            '1' => 'successful', '-1' => 'failed', '2' => 'refunded', '3' => 'processing', default => 'pending'
        };
        if ($status === 'pending') {
            return;
        }
        SendMobilePushNotification::dispatch(
            $transaction->user_id, "transaction:{$transaction->id}:{$status}", 'Transaction update',
            "Your {$transaction->transaction_category} transaction is {$status}.", ['transaction_id' => $transaction->id]
        )->afterCommit();
    }
}
