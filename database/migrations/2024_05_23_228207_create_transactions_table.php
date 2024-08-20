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
            $table->string('product_plan_id')->constrained(table: 'product_plans');
            $table->string('transaction_category')->nullable()->comment('Options: data, airtime, bills, cable subscription etc');
            $table->string('status')->default(0)->nullable()->comment('status of transaction: 1:success, 0:pending(default), -1:failed, 2:refunded, 3:processing');
            $table->string('wallet_category')->comment('data_wallet/main_wallet');
            $table->string('phone_number')->comment('phone number that benefits')->nullable();
            $table->string('smart_card_number')->comment('iuc number that benefits that benefits')->nullable();
            $table->string('metre_number')->comment('metre number that benefits')->nullable();
            $table->string('cable_tv_slots')->default('1')->comment('no of slots bought')->nullable();
            $table->string('utility_slots')->default('1')->comment('no of slots bought')->nullable();
            $table->string('amount')->comment('amount that was bought');
            $table->string('balance_before');
            $table->string('balance_after');
            $table->string('description');
            $table->longText('user_screen_message')->nullable();
            $table->longText('admin_screen_message')->nullable();
            $table->longText('referral_commission_status')->nullable()->default(0)->comment('1 - given 0 - not given');
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
