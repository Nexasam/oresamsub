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
        // Add api_id to products table if not exists
        if (!Schema::hasColumn('products', 'api_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('api_id')->nullable()->after('id');
            });
        }

        // Add api_id to networks table if not exists
        if (!Schema::hasColumn('networks', 'api_id')) {
            Schema::table('networks', function (Blueprint $table) {
                $table->unsignedBigInteger('api_id')->nullable()->after('id');
            });
        }

        // Add api_id to product_plan_categories table if not exists
        if (!Schema::hasColumn('product_plan_categories', 'api_id')) {
            Schema::table('product_plan_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('api_id')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop api_id from products table if exists
        if (Schema::hasColumn('products', 'api_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('api_id');
            });
        }

        // Drop api_id from networks table if exists
        if (Schema::hasColumn('networks', 'api_id')) {
            Schema::table('networks', function (Blueprint $table) {
                $table->dropColumn('api_id');
            });
        }

        // Drop api_id from product_plan_categories table if exists
        if (Schema::hasColumn('product_plan_categories', 'api_id')) {
            Schema::table('product_plan_categories', function (Blueprint $table) {
                $table->dropColumn('api_id');
            });
        }
    }
};
