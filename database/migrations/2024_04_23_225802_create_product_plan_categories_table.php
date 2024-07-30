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
        Schema::create('product_plan_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_plan_category_name')->unique();
            $table->foreignUuid('automation_id')->constrained('automations');
            $table->foreignUuid('product_id')->constrained('products');
            $table->string('is_purchase_discount_percentage')->default(1)->comment('1 means yes, 0 means no');
            $table->string('discount_value')->default(0)->comment('if perecent, should not be greater than 100');
            $table->string('is_hot_sales')->default(0)->comment('this is to notify the customer that this product is hotsales');
            $table->string('network_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_plan_categories');
    }
};
