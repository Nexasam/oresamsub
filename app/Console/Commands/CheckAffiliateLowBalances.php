<?php

namespace App\Console\Commands;

use App\Services\AffiliateLowBalanceService;
use Illuminate\Console\Command;

class CheckAffiliateLowBalances extends Command
{
    protected $signature = 'affiliate:check-low-balances';

    protected $description = 'Notify monitored affiliates whose OresamSub wallet is below their configured threshold';

    public function handle(AffiliateLowBalanceService $service): int
    {
        $result = $service->run();
        $this->info(
            "Affiliate balances checked: {$result['checked']}; notifications sent: {$result['notified']}; failed: {$result['failed']}."
        );

        return self::SUCCESS;
    }
}
