<?php

use App\Console\Commands\BackfillProductPlanNetworks;
use App\Console\Commands\ClearErrorLogs;
use App\Console\Commands\ComputeReferralCommission;
use App\Console\Commands\FinalizeDailyCommission;
use App\Console\Commands\GeneralRepetitiveTasks;
use App\Console\Commands\ProcessPendingAirtimeTransactions;
use App\Console\Commands\ReprocessPendingTransaction;
use App\Console\Commands\RunWalletAutoFunding;
use App\Console\Commands\SendFailedTransactionEmail;
use App\Console\Commands\SendNewRegistrationEmail;
use App\Console\Commands\SendPendingTransactionEmail;
use App\Console\Commands\SyncAddons;
use App\Console\Commands\SyncUserContactsFromTransactions;
use App\Console\Commands\ZerorizeNegativeBalances;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
//     // Schedule::command('php artisan migrate')->everyMinute();    
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command('migrate --force')->everyMinute();
Schedule::command(ProcessPendingAirtimeTransactions::class)->everyThirtySeconds();
Schedule::command(ZerorizeNegativeBalances::class)->everyTwoMinutes()->withoutOverlapping();
// Schedule::command(BackfillProductPlanNetworks::class)->everyFiveMinutes(); //temp use
Schedule::command(ComputeReferralCommission::class)->everyFiveMinutes(); 
// Schedule::command(FinalizeDailyCommission::class)->everyMinute(); 
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

// Schedule::command(SyncUserContactsFromTransactions::class)->everyTwoMinutes()->withoutOverlapping();

Schedule::command(ClearErrorLogs::class)->everyThirtyMinutes()->withoutOverlapping();

Schedule::command(RunWalletAutoFunding::class)->everyFiveMinutes()->withoutOverlapping();



