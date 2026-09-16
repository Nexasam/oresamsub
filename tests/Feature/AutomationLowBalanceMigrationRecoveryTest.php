<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('repairs an existing low balance alerts table left by a partial migration', function (): void {
    Schema::drop('automation_low_balance_alerts');

    Schema::create('automation_low_balance_alerts', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('automation_wallet_funding_id');
        $table->date('alert_date');
        $table->unsignedTinyInteger('slot');
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
    });

    $migration = require database_path('migrations/2026_09_16_000003_create_automation_low_balance_alerts_table.php');
    $migration->up();

    expect(Schema::hasIndex(
        'automation_low_balance_alerts',
        ['automation_wallet_funding_id', 'alert_date', 'slot'],
        'unique'
    ))->toBeTrue()
        ->and(Schema::hasForeignKey('automation_low_balance_alerts', ['automation_wallet_funding_id']))->toBeTrue();
});

it('does not run database migrations from the recurring application scheduler', function (): void {
    $commands = collect(Schedule::events())->pluck('command')->filter();

    expect($commands->contains(fn (string $command) => str_contains($command, 'migrate')))->toBeFalse();
});
