<?php

namespace App\Console\Commands;

use App\Services\Standalone\RenewStandaloneFeatures as RenewalService;
use Illuminate\Console\Command;

class RenewStandaloneFeatures extends Command
{
    protected $signature = 'standalones:renew-features';

    protected $description = 'Renew due standalone monthly feature subscriptions';

    public function handle(RenewalService $renewals): int
    {
        $results = $renewals->handle();
        $this->info(collect($results)->map(fn ($count, $status) => "{$status}: {$count}")->implode(', '));

        return self::SUCCESS;
    }
}
