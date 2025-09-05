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
        Schema::create('plan_profit_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('data_size_in_mb')->nullable();
            $table->string('validity_in_days')->nullable();
            $table->string('network_id');
            $table->string('product_id'); //starting with data
            $table->string('lowest_cost_price')->nullable(); //lowest costprice
            $table->string('profit_interval')->default(5);
            $table->string('profit_1')->nullable();
            $table->string('profit_2')->nullable();
            $table->string('profit_3')->nullable();
            $table->string('profit_4')->nullable();
            $table->string('profit_5')->nullable();
            $table->string('profit_6')->nullable();
            $table->string('profit_7')->nullable();
            $table->string('profit_8')->nullable();
            $table->string('profit_9')->nullable();
            $table->string('profit_10')->nullable();
            $table->string('profit_11')->nullable();
            $table->string('profit_12')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_profit_settings');
    }
};
