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
        Schema::table('product_plans', function (Blueprint $table) {
            $table->string('unique_product_plan_id')->nullable()->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_plans', function (Blueprint $table) {
            $table->string('unique_product_plan_id')->nullable(false)->change(); 
        });
    }
};
