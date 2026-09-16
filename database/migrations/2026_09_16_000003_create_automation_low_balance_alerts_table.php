<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_low_balance_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_wallet_funding_id')->constrained()->cascadeOnDelete();
            $table->date('alert_date');
            $table->unsignedTinyInteger('slot');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['automation_wallet_funding_id', 'alert_date', 'slot'], 'automation_low_balance_alert_slot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_low_balance_alerts');
    }
};
