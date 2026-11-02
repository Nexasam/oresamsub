<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
//     // Schedule::command('php artisan migrate')->everyMinute();    
// })->purpose('Display an inspiring quote')->hourly();


// Hostinger disables proc_open, which Schedule::command() requires. Run
// Artisan commands inside scheduler callbacks so the existing schedule:run
// cron works without creating child processes.
$scheduleArtisan = static function (string $command, array $parameters, string $name) {
    return Schedule::call(static function () use ($command, $parameters): void {
        Artisan::call($command, $parameters);
    })->name($name);
};

$scheduleArtisan('app:process-pending-airtime-transactions', [], 'process-pending-airtime-transactions')
    ->everyThirtySeconds();

// This installation uses the cron-driven Laravel scheduler instead of a
// permanent queue worker. Drain all currently available database queue jobs
// on every scheduler run, then exit cleanly. Delayed push-receipt checks will
// be picked up by the next cron run after their delay expires.
$scheduleArtisan('queue:work', [
        '--stop-when-empty' => true,
        '--queue' => 'default',
        '--tries' => 3,
        '--timeout' => 60,
    ], 'process-default-queue-without-proc-open')
    ->everyMinute()
    ->withoutOverlapping();

$scheduleArtisan('app:zerorize-negative-balances', [], 'zerorize-negative-balances')
    ->everyTwoMinutes()
    ->withoutOverlapping();
// Schedule::command(BackfillProductPlanNetworks::class)->everyFiveMinutes(); //temp use
$scheduleArtisan('compute-referral-commission', [], 'compute-referral-commission')
    ->everyFiveMinutes();
// Schedule::command(FinalizeDailyCommission::class)->everyMinute(); 
$scheduleArtisan('finalize-referral-daily-commission', [], 'finalize-referral-daily-commission')
    ->dailyAt('02:00')
    ->timezone('Africa/Lagos')
    ->withoutOverlapping();

// Schedule::command(ComputeReferralCommission::class)->everySixHours();
// Schedule::command(ComputeReferralCommission::class)->hourly();


$scheduleArtisan('app:send-new-registration-email', [], 'send-new-registration-email')
    ->everyTwoMinutes()
    ->withoutOverlapping();
$scheduleArtisan('app:send-failed-transaction-email', [], 'send-failed-transaction-email')
    ->everyThirtySeconds()
    ->withoutOverlapping();
$scheduleArtisan('app:send-pending-transaction-email', [], 'send-pending-transaction-email')
    ->everyTwoMinutes()
    ->withoutOverlapping();

$scheduleArtisan('app:reprocess-pending-transaction', [], 'reprocess-pending-transaction')
    ->everyMinute()
    ->withoutOverlapping();

// Schedule::command(SyncUserContactsFromTransactions::class)->everyTwoMinutes()->withoutOverlapping();

// Schedule::command(ClearErrorLogs::class)->everyThirtyMinutes()->withoutOverlapping();

$scheduleArtisan('automation:wallet-funding', [], 'automation-wallet-funding')
    ->everyFiveMinutes()
    ->withoutOverlapping();
$scheduleArtisan('automation:notify-low-balances', ['position' => 1], 'notify-automation-low-balances-1')
    ->cron('0 */3 * * *')->timezone('Africa/Lagos')->withoutOverlapping();
$scheduleArtisan('automation:notify-low-balances', ['position' => 2], 'notify-automation-low-balances-2')
    ->cron('2 */3 * * *')->timezone('Africa/Lagos')->withoutOverlapping();
$scheduleArtisan('automation:notify-low-balances', ['position' => 3], 'notify-automation-low-balances-3')
    ->cron('4 */3 * * *')->timezone('Africa/Lagos')->withoutOverlapping();
$scheduleArtisan('standalones:renew-features', [], 'renew-standalone-features')
    ->hourly()
    ->withoutOverlapping();
$scheduleArtisan('affiliate:check-low-balances', [], 'check-affiliate-low-balances')
    ->hourly()
    ->timezone('Africa/Lagos')
    ->withoutOverlapping();

$scheduleArtisan('bonuses:process-weekly-transactions', [], 'process-weekly-transaction-bonuses')
    ->weeklyOn(1, '00:15')
    ->timezone('Africa/Lagos')
    ->withoutOverlapping();
