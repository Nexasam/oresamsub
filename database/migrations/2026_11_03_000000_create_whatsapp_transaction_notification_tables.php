<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_notification_preferences')) {
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
        }

        if (! Schema::hasTable('whatsapp_notification_deliveries')) {
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

                $table->index(['transaction_id', 'notification_type'], 'wa_delivery_txn_type_idx');
                $table->index(['user_id', 'status'], 'wa_delivery_user_status_idx');
            });
        }

        // MySQL limits identifiers to 64 characters. Laravel's generated name
        // for the transaction/type index exceeds that limit. These checks also
        // repair the table if MySQL created it before the original migration
        // failed while adding its indexes.
        if (! Schema::hasIndex('whatsapp_notification_deliveries', 'wa_delivery_txn_type_idx')) {
            Schema::table('whatsapp_notification_deliveries', function (Blueprint $table): void {
                $table->index(['transaction_id', 'notification_type'], 'wa_delivery_txn_type_idx');
            });
        }

        if (! Schema::hasIndex('whatsapp_notification_deliveries', 'wa_delivery_user_status_idx')) {
            Schema::table('whatsapp_notification_deliveries', function (Blueprint $table): void {
                $table->index(['user_id', 'status'], 'wa_delivery_user_status_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notification_deliveries');
        Schema::dropIfExists('whatsapp_notification_preferences');
    }
};
