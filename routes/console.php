<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
    // Schedule::command('php artisan migrate')->everyMinute();
    
})->purpose('Display an inspiring quote')->hourly();
Schedule::command('php artisan migrate')->everyFiveMinutes();