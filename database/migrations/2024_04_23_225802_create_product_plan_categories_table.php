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
            $table->foreignUuid('network_id')->constrained('networks');
            $table->string('bulk_data_wallet_in_mb')->default(0);
            $table->string('mb_data_measurement')->default(1024)->commet('this states the number of mb calculated per GB inturn per TB e.g 1024/1000 etc');
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
