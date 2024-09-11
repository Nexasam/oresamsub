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
            $table->string('upline_commission_option')->after('id')->default('flat')->comment('flat or percent')->nullable();
            $table->string('upline_percentage_commission')->after('id')->default(0)->comment('if commission_option is percent')->nullable();
            $table->string('upline_flat_commission')->after('id')->default(0)->comment('if commission_option is percent')->nullable();
            $table->string('upline_commission_cap')->after('id')->default(1000)->comment('if commission_option is percent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_plans', function (Blueprint $table) {
            $table->dropColumn('upline_commission_option');
            $table->dropColumn('upline_percentage_commission');
            $table->dropColumn('upline_flat_commission');
            $table->dropColumn('upline_commission_cap');
        });
    }
};
