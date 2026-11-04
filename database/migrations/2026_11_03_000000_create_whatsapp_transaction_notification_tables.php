<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_notification_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('transactional_enabled')->default(false);
            $table->timestamp('consented_at')->nullable();
            $table->string('consent_source', 50)->nullable();
            $table->timestamp('opted_out_at')->nullable();
            $table->timestamp('last_inbound_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_notification_deliveries', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('transaction_id')->nullable()->constrained('transactions')->cascadeOnDelete();
            $table->foreignUuid('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_key')->unique();
            $table->string('notification_type', 40);
            $table->string('payload_type', 20)->nullable();
            $table->string('template_name')->nullable();
            $table->string('recipient_last_four', 4)->nullable();
            $table->string('status', 30)->default('queued');
            $table->string('meta_message_id')->nullable()->unique();
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->string('message_excerpt', 500)->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['transaction_id', 'notification_type']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notification_deliveries');
        Schema::dropIfExists('whatsapp_notification_preferences');
    }
};
