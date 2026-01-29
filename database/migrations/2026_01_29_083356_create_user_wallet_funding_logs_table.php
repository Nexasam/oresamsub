<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_wallet_funding_logs', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary key as UUID
            $table->string('user_id')->nullable(); // FK to users table
            $table->string('funding_option_id'); // FK to funding options
            $table->decimal('funding_amount', 15, 2); // Amount user intended to fund
            $table->decimal('promo_bonus', 15, 2)->default(0); // Promo bonus applied
            $table->decimal('amount_settled_by_gateway', 15, 2); // Actual amount settled by gateway
            $table->decimal('gateway_charge', 15, 2)->default(0); // Gateway fee
            $table->uuid('promo_id')->nullable(); // FK to UserWalletFundingPromo
            $table->tinyInteger('status')->default(1); // 1 = success, 0 = failed
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wallet_funding_logs');
    }
};
