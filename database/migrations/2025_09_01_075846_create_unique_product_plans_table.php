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
        Schema::create('unique_product_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('api_id')->unique();
            $table->string('product_plan_name')->unique();
            $table->string('data_size_in_mb')->nullable();
            $table->string('validity_in_days')->nullable();
            $table->string('network_id');
            $table->string('product_id'); //starting with data
            $table->string('cost_price')->nullable(); //this will be by default the most expensive product_plan  + [pricing, which will be done later but say 70 by default]
            $table->string('price_1')->nullable();
            $table->string('price_2')->nullable();
            $table->string('price_3')->nullable();
            $table->string('price_4')->nullable();
            $table->string('price_5')->nullable();
            $table->string('price_6')->nullable();
            $table->string('price_7')->nullable();
            $table->string('price_8')->nullable();
            $table->string('price_9')->nullable();
            $table->string('price_10')->nullable();
            $table->string('price_11')->nullable();
            $table->string('price_12')->nullable();
            $table->string('commission_1')->default(0);
            $table->string('commission_2')->default(0);
            $table->string('commission_3')->default(0);
            $table->string('commission_4')->default(0);
            $table->string('commission_5')->default(0);
            $table->string('commission_6')->default(0);
            $table->string('commission_7')->default(0);
            $table->string('commission_8')->default(0);
            $table->string('commission_9')->default(0);
            $table->string('commission_10')->default(0);
            $table->string('commission_11')->default(0);
            $table->string('commission_12')->default(0);
            $table->string('visibility')->default(0)->comment(' 0 - hidden, 1 - visible');
            $table->string('commission_status')->default(0)->comment('0 - hidden, 1 - visible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unique_product_plans');
    }
};
