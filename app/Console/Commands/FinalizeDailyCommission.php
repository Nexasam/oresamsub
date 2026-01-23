<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Commissions;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FinalizeDailyCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finalize-referral-daily-commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finalize Referral Daily Commission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // finalize commissions for yesterday's successful "data" transactions
        // $yesterdayStart = \Carbon\Carbon::yesterday()->startOfDay();
        // $yesterdayEnd   = \Carbon\Carbon::yesterday()->endOfDay();

        //ONLY FOR DATA TRANSACTIONS FOR NOW
        $startFrom = Carbon::parse('2023-07-08')->startOfDay();
        $endAt     = Carbon::yesterday()->endOfDay();


        $transactionIds = Transaction::whereBetween('created_at', [$startFrom, $endAt])
        ->where('status', 1)
        ->where('transaction_category', 'data')
        ->pluck('id');

        if ($transactionIds->isEmpty()) {
            $this->info('No eligible commissions to finalize.');
            return;
        }
        

        $finalizedCount = Commissions::whereIn('transaction_id', $transactionIds)
            ->where('status', 0)
            ->update(['status' => 1]);

        $this->info("Daily referral commissions finalized: {$finalizedCount}");
    }
}
