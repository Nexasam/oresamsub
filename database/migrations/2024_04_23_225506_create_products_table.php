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
        //e.g data, airtime, bills , electricity etc
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->string('product_name');
            $table->string('visibility')->default(1)->comment(' 0- hidden, 1- visible');
            $table->string('active_status')->default(1)->comment(' 0 - inactive, 1- active');
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
        Schema::dropIfExists('products');
    }
};
