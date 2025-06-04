<?php

use App\Console\Commands\SyncAddons;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ClearErrorLogs;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SendNewRegistrationEmail;
use App\Console\Commands\ZerorizeNegativeBalances;
use App\Console\Commands\SendFailedTransactionEmail;
use App\Console\Commands\ProcessPendingAirtimeTransactions;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
//     // Schedule::command('php artisan migrate')->everyMinute();    
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command('migrate --force')->everyMinute();
Schedule::command(ProcessPendingAirtimeTransactions::class)->everyMinute();
Schedule::command(ZerorizeNegativeBalances::class)->everyTwoMinutes()->withoutOverlapping();

// Schedule::command(SendNewRegistrationEmail::class)->everyFourMinutes()->withoutOverlapping();
// Schedule::command(SendFailedTransactionEmail::class)->everyThirtySeconds();

Schedule::command(ClearErrorLogs::class)->everyThirtyMinutes()->withoutOverlapping();


