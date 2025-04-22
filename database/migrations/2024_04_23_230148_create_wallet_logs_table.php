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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained(table: 'users');
            $table->string('transaction_id')->nullable();
            $table->string('action_by');
            $table->string('transaction_category')->nullable()->comment('Options: wallet_crediting, withdrawal etc');
            $table->string('balance_before');
            $table->string('balance_after');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
