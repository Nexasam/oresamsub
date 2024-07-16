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
        Schema::create('admin2fa_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('global_user_2fa_setting')->default('OFF')->comment('control 2fa settings for all users')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin2fa_settings');
    }
};
