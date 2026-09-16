<?php

namespace App\Console\Commands;

use App\Services\Automation\AutomationLowBalanceNotificationService;
use Illuminate\Console\Command;

class NotifyAutomationLowBalances extends Command
{
    protected $signature = 'automation:notify-low-balances {position : Burst position: 1, 2, or 3}';

    protected $description = 'Email administrators about active automations at or below their funding threshold';

    public function handle(AutomationLowBalanceNotificationService $service): int
    {
        $result = $service->run((int) $this->argument('position'));
        $this->info("Automation low-balance emails sent: {$result['notified']}; failed: {$result['failed']}.");

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
