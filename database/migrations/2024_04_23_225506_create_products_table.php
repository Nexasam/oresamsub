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
