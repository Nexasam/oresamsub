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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained(table: 'users');
            $table->string('transaction_category')->nullable()->comment('Options: data, airtime, bills, cable subscription etc');
            $table->string('wallet_category')->comment('data_wallet/main_wallet');
            $table->string('phone_number')->comment('phone number that benefits')->nullable();
            $table->string('amount')->comment('amount data was bought');
            $table->string('balance_before');
            $table->string('balance_after');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
