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
        //WE MIGHT NOT NEED THIS:
        Schema::create('user_product_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_plan_id')->comment('e.g platinum reseller, gold reseller etc');
            $table->string('product_plan_id')->constrained('product_plans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_plans');
    }
};
