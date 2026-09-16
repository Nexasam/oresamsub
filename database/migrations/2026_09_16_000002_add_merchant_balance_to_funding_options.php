<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_options', function (Blueprint $table) {
            $table->decimal('merchant_wallet_balance', 15, 2)->nullable();
            $table->timestamp('merchant_balance_synced_at')->nullable();
            $table->text('merchant_balance_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('funding_options', function (Blueprint $table) {
            $table->dropColumn([
                'merchant_wallet_balance',
                'merchant_balance_synced_at',
                'merchant_balance_error',
            ]);
        });
    }
};
