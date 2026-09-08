<?php

namespace App\Services\Standalone;

use App\Models\StandaloneVirtualAccount;
use App\Models\StandaloneWebsite;

class StandaloneSecurewavePaymentMatcher
{
    public function match(array $payload): ?StandaloneWebsite
    {
        $account = (string) data_get($payload, 'receiver.account_number', '');
        $reference = (string) data_get($payload, 'receiver.account_reference', '');
        if ($account === '' && $reference === '') {
            return null;
        }
        $virtual = StandaloneVirtualAccount::query()->with('standaloneWebsite')->where(function ($q) use ($account, $reference) {
            if ($account !== '') {
                $q->where('account_number', $account);
            } if ($reference !== '') {
                $q->orWhere('account_reference', $reference);
            }
        })->first();

        return $virtual?->standaloneWebsite;
    }
}
