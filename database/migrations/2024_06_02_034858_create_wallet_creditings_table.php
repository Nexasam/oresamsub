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
        Schema::create('wallet_creditings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained(table: 'users');
            $table->string('transaction_reference')->nullable();
            $table->string('transaction_status')->nullable()->comment('PAID, PENDING, FAILED');
            $table->string('funding_status')->nullable()->comment('completed, pending, failed');
            $table->text('transaction_message')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_reference')->nullable();
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('amount_charged', 15, 2)->default(0);
            $table->decimal('amount_settled', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_creditings');
    }
};
