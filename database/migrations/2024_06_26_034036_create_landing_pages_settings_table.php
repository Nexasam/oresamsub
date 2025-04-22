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
        Schema::create('landing_pages_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template_type')->default('template_1');
            $table->string('field_name');
            $table->longText('field_details');
            $table->string('visibility')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages_settings');
    }
};
