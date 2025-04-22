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
        Schema::create('funding_option_bank_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('funding_option_id')->constrained('funding_options');
            $table->string('bank_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_option_bank_codes');
    }
};
