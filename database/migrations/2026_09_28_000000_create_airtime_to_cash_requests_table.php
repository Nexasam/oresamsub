<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airtime_to_cash_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('network_id')->nullable()->constrained()->nullOnDelete();
            $table->string('network_name');
            $table->decimal('airtime_amount', 12, 2);
            $table->decimal('cash_amount', 12, 2);
            $table->decimal('rate_per_100', 8, 2)->default(90);
            $table->string('sender_phone');
            $table->string('payout_bank_name');
            $table->string('payout_account_name');
            $table->text('payout_account_number');
            $table->string('payout_bank_code')->nullable();
            $table->string('customer_transfer_reference')->nullable();
            $table->text('customer_note')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->string('payout_reference')->nullable();
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->string('source')->default('pwa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airtime_to_cash_requests');
    }
};
