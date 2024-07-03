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
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_commission_feature')->default(3)->comment('1- activate flat rate, 2 - activate percentage rate, 3 -deactivate both');
            $table->string('set_product_commission_flat_rate')->default(50);
            $table->string('set_product_commission_percentage_rate')->default(5);
            $table->string('first_downline_crediting_feature')->default(3)->comment('1- award upline by flat rate, 2- award upline by percentage rate, 3 - dont award ');
            $table->string('set_first_downline_crediting_flat_rate')->default(50);
            $table->string('set_first_downline_crediting_percentage_rate')->default(5);
            $table->string('set_first_downline_crediting_cap')->default(200)->comment('this says that the upline cannot ever earn more than the capped value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_settings');
    }
};
