<?php

namespace App\Console\Commands;

use App\Services\WeeklyTransactionBonusService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ProcessWeeklyTransactionBonuses extends Command
{
    protected $signature = 'bonuses:process-weekly-transactions {--week-start= : Monday date in YYYY-MM-DD format; defaults to the previous week}';

    protected $description = 'Credit weekly transaction-volume campaign rewards to qualifying customer bonus wallets';

    public function handle(WeeklyTransactionBonusService $bonuses): int
    {
        $weekStart = $this->option('week-start')
            ? CarbonImmutable::parse($this->option('week-start'), 'Africa/Lagos')->startOfWeek()
            : CarbonImmutable::now('Africa/Lagos')->subWeek()->startOfWeek();

        if ($weekStart->endOfWeek()->isFuture()) {
            $this->error('Only completed weeks can be processed.');

            return self::FAILURE;
        }

        $result = $bonuses->processWeek($weekStart);
        $this->info("Processed {$result['week_start']} to {$result['week_end']}: {$result['rewarded']} customers rewarded, ₦".number_format($result['amount'], 2).'.');

        return self::SUCCESS;
    }
}
