<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('automation_low_balance_alerts')) {
            Schema::create('automation_low_balance_alerts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('automation_wallet_funding_id');
                $table->date('alert_date');
                $table->unsignedTinyInteger('slot');
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasIndex('automation_low_balance_alerts', ['automation_wallet_funding_id', 'alert_date', 'slot'])) {
            Schema::table('automation_low_balance_alerts', function (Blueprint $table): void {
                $table->unique(
                    ['automation_wallet_funding_id', 'alert_date', 'slot'],
                    'automation_low_balance_alert_slot_unique'
                );
            });
        }

        if (! Schema::hasForeignKey('automation_low_balance_alerts', ['automation_wallet_funding_id'])) {
            Schema::table('automation_low_balance_alerts', function (Blueprint $table): void {
                $table->foreign('automation_wallet_funding_id', 'automation_low_balance_alert_funding_foreign')
                    ->references('id')
                    ->on('automation_wallet_fundings')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_low_balance_alerts');
    }
};
