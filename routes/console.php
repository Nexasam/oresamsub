<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ClearErrorLogs;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SendNewRegistrationEmail;
use App\Console\Commands\ZerorizeNegativeBalances;
use App\Console\Commands\ProcessPendingAirtimeTransactions;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
    // Schedule::command('php artisan migrate')->everyMinute();    
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('logs:clear', function() {
    
    exec('rm -f ' . storage_path('logs/*.log'));

    exec('rm -f ' . base_path('*.log'));
    
    $this->comment('Logs have been cleared!');
    
})->describe('Clear log files');


// Schedule::command('log:clear --force')->everyMinute();

Schedule::command('logs:clear')->everyMinute();
Schedule::command('migrate --force')->everyMinute();
Schedule::command(ProcessPendingAirtimeTransactions::class)->everyFifteenSeconds()->withoutOverlapping();
Schedule::command(ZerorizeNegativeBalances::class)->everyTenSeconds()->withoutOverlapping();
Schedule::command(SendNewRegistrationEmail::class)->everyFiveMinutes()->withoutOverlapping();
// Schedule::command(ClearErrorLogs::class)->everyFifteenSeconds()->withoutOverlapping();

