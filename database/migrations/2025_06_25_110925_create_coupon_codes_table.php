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
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('code')->unique();
            $table->string('status')->default(1); #only 1 afctive status per time
            $table->string('product_slug');
            $table->string('network_id')->nullable();
            $table->string('product_plan_category_id')->nullable();
            $table->string('amount');
            $table->string('transaction_metrics');
            $table->string('transaction_metrics_date');
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
        Schema::dropIfExists('coupon_codes');
    }
};
