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
            $table->string('user_level_1_commission')->default('0')->after('user_level_6_selling_price');
            $table->string('user_level_2_commission')->default('0')->after('user_level_6_selling_price');
            $table->string('user_level_3_commission')->default('0')->after('user_level_6_selling_price');
            $table->string('user_level_4_commission')->default('0')->after('user_level_6_selling_price');
            $table->string('user_level_5_commission')->default('0')->after('user_level_6_selling_price');
            $table->string('user_level_6_commission')->default('0')->after('user_level_6_selling_price');
            $table->string('commission_feature')->after('user_level_6_selling_price')->default('1')->comment('1 on  0 - off');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_plans', function (Blueprint $table) {
            $table->dropColumn('user_level_1_commission');
            $table->dropColumn('user_level_2_commission');
            $table->dropColumn('user_level_3_commission');
            $table->dropColumn('user_level_4_commission');
            $table->dropColumn('user_level_5_commission');
            $table->dropColumn('user_level_6_commission');
            $table->dropColumn('commission_feature');
        });
    }
};
