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
        Schema::create('feature_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('purchase_amount')->nullable();
            $table->string('payment_condition')->default('onetime')->comment('onetime / monthly etc');
            $table->string('purchase_status')->default('0')->comment('0-pending 1-success');
            $table->string('require_further_support')->default('1')->comment('0-pending 1-success');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_lists');
    }
};
