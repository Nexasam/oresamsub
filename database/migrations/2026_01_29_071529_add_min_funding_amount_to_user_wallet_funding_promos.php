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
        Schema::table('user_wallet_funding_promos', function (Blueprint $table) {
            $table->decimal('min_funding_amount', 15, 2)->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_wallet_funding_promos', function (Blueprint $table) {
            $table->dropColumn('min_funding_amount');
        });
    }
};
