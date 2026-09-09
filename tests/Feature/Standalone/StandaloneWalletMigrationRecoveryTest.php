<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('repairs an existing wallet entries table left by a partial migration', function (): void {
    Schema::drop('standalone_wallet_entries');

    Schema::create('standalone_wallet_entries', function (Blueprint $table): void {
        $table->uuid('id')->primary();
        $table->uuid('standalone_website_id');
        $table->string('transaction_id');
        $table->string('type', 12);
        $table->string('category', 24);
        $table->decimal('amount', 18, 2);
        $table->decimal('balance_before', 18, 2);
        $table->decimal('balance_after', 18, 2);
        $table->string('client_reference')->nullable();
        $table->string('purpose', 255);
        $table->string('provider_reference')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });

    $migration = require database_path('migrations/2026_09_09_000002_create_standalone_wallet_entries_table.php');
    $migration->up();

    expect(Schema::hasIndex('standalone_wallet_entries', ['transaction_id'], 'unique'))->toBeTrue()
        ->and(Schema::hasIndex('standalone_wallet_entries', ['type']))->toBeTrue()
        ->and(Schema::hasIndex('standalone_wallet_entries', ['category']))->toBeTrue()
        ->and(Schema::hasIndex('standalone_wallet_entries', ['provider_reference'], 'unique'))->toBeTrue()
        ->and(Schema::hasIndex('standalone_wallet_entries', ['standalone_website_id', 'client_reference'], 'unique'))->toBeTrue()
        ->and(Schema::hasForeignKey('standalone_wallet_entries', ['standalone_website_id']))->toBeTrue();
});
