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
        Schema::create('product_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_plan_name');
            // $table->foreignUuid('product_id')->constrained('products');
            // $table->foreignUuid('network_id')->nullable();
            $table->foreignUuid('product_plan_category_id')->constrained('product_plan_categories'); //this carries the productid e.g data and the network e.g mtn if applicable
            $table->string('automation_product_plan_id');
            $table->foreignUuid('automation_id')->constrained('automations');
            $table->string('cost_price')->nullable();
            $table->string('data_size_in_mb')->nullable();
            $table->string('validity_in_days')->nullable();
            $table->string('default_selling_price');
            $table->string('user_level_1_selling_price')->nullable();
            $table->string('user_level_2_selling_price')->nullable();
            $table->string('user_level_3_selling_price')->nullable();
            $table->string('user_level_4_selling_price')->nullable();
            $table->string('user_level_5_selling_price')->nullable();
            $table->string('user_level_6_selling_price')->nullable();
            $table->string('visibility')->default(1)->comment(' 0- hidden, 1- visible');
            $table->string('active_status')->default(0)->comment(' 0 - inactive, 1- active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_plans');
    }
};
