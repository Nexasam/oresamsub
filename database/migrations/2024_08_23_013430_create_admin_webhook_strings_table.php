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
        Schema::create('admin_webhook_strings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('funding_option_id')->constrained('funding_options');
            $table->string('webhook_suffix_string');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_webhook_strings');
    }
};
