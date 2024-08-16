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
        Schema::create('user_virtual_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('funding_option_id')->constrained('funding_options');
            $table->foreignUuid('user_id')->constrained('users');
            $table->string('funding_slug')->nullable();
            $table->string('response_status')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_email')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_reference')->nullable();
            $table->string('bvn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_virtual_accounts');
    }
};
