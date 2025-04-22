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
        Schema::create('user_bulk_data_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bulk_data_wallet_id')->constrained('user_bulk_data_wallets');
            $table->string('bulk_data_plan_name');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('plan_category_id')->constrained('product_plan_categories');
            $table->foreignUuid('bulk_data_product_plan_id')->constrained('bulk_data_product_plans');
            $table->string('main_wallet_before')->default(0);
            $table->string('main_wallet_after')->default(0);
            $table->string('wallet_data_balance_before')->default(0);
            $table->string('wallet_data_balance_after')->default(0);
            $table->string('data_value_mb')->default(0);
            $table->string('data_value_gb')->default(0);
            $table->string('data_value_tb')->default(0);
            $table->string('amount_spent')->default(0);
            $table->string('mb_data_measurement')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bulk_data_purchases');
    }
};
