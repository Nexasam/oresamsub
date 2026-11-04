<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('uses short index names and repairs notification delivery indexes after a partial migration', function (): void {
    Schema::table('whatsapp_notification_deliveries', function (Blueprint $table): void {
        $table->dropIndex('wa_delivery_txn_type_idx');
        $table->dropIndex('wa_delivery_user_status_idx');
    });

    $migration = require database_path('migrations/2026_11_03_000000_create_whatsapp_transaction_notification_tables.php');
    $migration->up();

    expect(Schema::hasIndex('whatsapp_notification_deliveries', 'wa_delivery_txn_type_idx'))->toBeTrue()
        ->and(Schema::hasIndex('whatsapp_notification_deliveries', 'wa_delivery_user_status_idx'))->toBeTrue();
});
