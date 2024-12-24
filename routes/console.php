<?php

use App\Console\Commands\SendNewRegistrationEmail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ZerorizeNegativeBalances;
use App\Console\Commands\ProcessPendingAirtimeTransactions;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
    // Schedule::command('php artisan migrate')->everyMinute();
    
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('log:clear')->everyMinute();
Schedule::command('migrate --force')->everyMinute();
Schedule::command(ProcessPendingAirtimeTransactions::class)->everyFifteenSeconds()->withoutOverlapping();
Schedule::command(ZerorizeNegativeBalances::class)->everyTenSeconds()->withoutOverlapping();
Schedule::command(SendNewRegistrationEmail::class)->everyFiveMinutes()->withoutOverlapping();

