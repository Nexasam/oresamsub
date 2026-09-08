<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standalone_funding_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('standalone_website_id')->constrained()->cascadeOnDelete();
            $table->string('event_id')->unique();
            $table->string('provider_reference')->unique();
            $table->string('reference')->unique();
            $table->decimal('amount_gross', 18, 2);
            $table->decimal('fees', 18, 2)->default(0);
            $table->decimal('amount_settled', 18, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('payment_status', 24);
            $table->timestamp('paid_at')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable()->index();
            $table->json('provider_payload')->nullable();
            $table->string('callback_url')->nullable();
            $table->json('callback_payload');
            $table->string('delivery_status', 24)->default('pending')->index();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->unsignedSmallInteger('last_http_status')->nullable();
            $table->string('last_error', 500)->nullable();
            $table->timestamp('first_attempted_at')->nullable();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_funding_events');
    }
};
