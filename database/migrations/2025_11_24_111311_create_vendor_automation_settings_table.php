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
        Schema::create('vendor_automation_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('product_slug')->default('data');
            $table->longText('endpoint_url');
            $table->json('request_params');
            $table->json('network_plans');
            $table->json('headers')->nullable();
            $table->string('method');
            $table->json('success_conditions');
            $table->longText('success_response');
            $table->longText('failed_response');
            $table->string('success_code');
            $table->string('failure_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_automation_settings');
    }
};
