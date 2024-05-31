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
        // e.g mtn data, mtn airtime, glo airtime, glo data, gotv, dstv etc
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_name');
            $table->foreignUuid('network_id')->nullable();
            $table->string('slug');
            $table->foreignUuid('product_categories_id')->constrained(table: 'product_categories', indexName: 'id');
            $table->string('visibility')->default(0)->comment(' 0- hidden, 1- visible');
            $table->string('active_status')->default(0)->comment(' 0 - inactive, 1- active');
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
