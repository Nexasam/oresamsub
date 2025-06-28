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
        Schema::create('wallet_funding_promos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('funding_option_id');
            $table->string('promo_metric')->default('last_transaction_before')->comment('last_transaction_before/last_transaction_after/username based...more later');
            $table->string('last_transaction_metrics_date')->nullable();
            $table->string('promo_discount_category')->default('percent')->comment('flat/percent');
            $table->string('promo_discount_percentage_cap')->nullable('percent')->comment('if its percentage, cap applies');
            $table->string('beneficiary')->nullable()->comment('this is if the promo metric is username');
            $table->string('comment')->nullable()->comment('can instruct admin about promo');
            $table->string('promo_value');
            $table->string('status')->default(1); #only 1 afctive status per time
            $table->string('slots');
            $table->string('slots_remaining');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_funding_promos');
    }
};
