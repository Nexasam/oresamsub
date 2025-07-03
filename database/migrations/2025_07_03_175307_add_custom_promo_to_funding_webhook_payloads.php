<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('funding_webhook_payloads', function (Blueprint $table) {
            $table->string('custom_wallet_funding_promo_id')->nullable()->after('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funding_webhook_payloads', function (Blueprint $table) {
            $table->dropColumn('custom_wallet_funding_promo_id');
        });
    }
};
