<?php

namespace App\Observers;

use App\Jobs\SendMobilePushNotification;
use App\Models\FundingWebhookPayload;

class WalletFundingMobilePushObserver
{
    public function created(FundingWebhookPayload $funding): void
    {
        if (! in_array(strtolower((string) $funding->funding_status), ['success', 'successful', 'completed'], true)) {
            return;
        }
        SendMobilePushNotification::dispatch($funding->user_id, "funding:{$funding->id}:successful", 'Wallet funded', 'Your wallet funding was received successfully.', ['screen' => 'wallet'])->afterCommit();
    }
}
