<?php

use App\Console\Commands\FinalizeDailyCommission;
use App\Console\Commands\SyncAddons;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ClearErrorLogs;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\GeneralRepetitiveTasks;
use App\Console\Commands\SendNewRegistrationEmail;
use App\Console\Commands\ZerorizeNegativeBalances;
use App\Console\Commands\ComputeReferralCommission;
use App\Console\Commands\SendFailedTransactionEmail;
use App\Console\Commands\ReprocessPendingTransaction;
use App\Console\Commands\SendPendingTransactionEmail;
use App\Console\Commands\ProcessPendingAirtimeTransactions;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
//     // Schedule::command('php artisan migrate')->everyMinute();    
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command('migrate --force')->everyMinute();
Schedule::command(ProcessPendingAirtimeTransactions::class)->everyThirtySeconds();
Schedule::command(ZerorizeNegativeBalances::class)->everyTwoMinutes()->withoutOverlapping();
Schedule::command(ComputeReferralCommission::class)->everyFiveMinutes(); 
Schedule::command(FinalizeDailyCommission::class)
    ->dailyAt('02:00')
    ->timezone('Africa/Lagos')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule::command(ComputeReferralCommission::class)->everySixHours();
// Schedule::command(ComputeReferralCommission::class)->hourly();


Schedule::command(SendNewRegistrationEmail::class)->everyTwoMinutes()->withoutOverlapping();
Schedule::command(SendFailedTransactionEmail::class)->everyThirtySeconds()->withoutOverlapping();
Schedule::command(SendPendingTransactionEmail::class)->everyTwoMinutes()->withoutOverlapping();

Schedule::command(ReprocessPendingTransaction::class)->everyMinute()->withoutOverlapping();


Schedule::command(ClearErrorLogs::class)->everyThirtyMinutes()->withoutOverlapping();


