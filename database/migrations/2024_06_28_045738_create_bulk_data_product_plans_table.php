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
        Schema::create('bulk_data_product_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('bulk_data_plan_name')->unique();
            $table->foreignUuid('product_plan_category_id')->constrained('product_plan_categories');
            $table->string('data_value_mb')->default(0);
            $table->string('data_value_gb')->default(0);
            $table->string('data_value_tb')->default(0);
            $table->string('mb_data_measurement')->default(1024)->commet('this states the number of mb calculated per GB inturn per TB e.g 1024/1000 etc');
            $table->string('cost_price')->default(0);
            $table->string('default_selling_price');
            $table->string('user_level_1_selling_price')->nullable();
            $table->string('user_level_2_selling_price')->nullable();
            $table->string('user_level_3_selling_price')->nullable();
            $table->string('user_level_4_selling_price')->nullable();
            $table->string('user_level_5_selling_price')->nullable();
            $table->string('user_level_6_selling_price')->nullable();
            $table->string('visibility')->default(1)->comment(' 1 - visible to user, 0 - hidden from user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_data_product_plans');
    }
};
