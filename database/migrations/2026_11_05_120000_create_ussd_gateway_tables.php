<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ussd_gateway_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('main_sim_number', 15)->unique();
            $table->unsignedTinyInteger('sim_slot')->default(0);
            $table->uuid('installation_id')->nullable()->unique();
            $table->integer('subscription_id')->nullable();
            $table->string('device_model', 120)->nullable();
            $table->timestamp('bound_at')->nullable();
            $table->char('token_digest', 64)->unique();
            $table->string('token_hint', 16);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ussd_recharge_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('device_id')->constrained('ussd_gateway_devices')->restrictOnDelete();
            $table->string('name', 120);
            $table->string('plan_label', 160)->nullable();
            $table->string('status', 24)->default('queued')->index();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['device_id', 'status'], 'urg_batch_device_status_idx');
        });

        Schema::create('ussd_recharge_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('batch_id')->constrained('ussd_recharge_batches')->cascadeOnDelete();
            $table->foreignUuid('device_id')->constrained('ussd_gateway_devices')->restrictOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('recipient_number', 15);
            $table->string('status', 32)->default('queued')->index();
            $table->char('dispatch_token_digest', 64)->nullable()->unique();
            $table->uuid('client_result_id')->nullable()->unique();
            $table->string('device_outcome', 40)->nullable();
            $table->text('response_text')->nullable();
            $table->char('response_fingerprint', 64)->nullable();
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('dial_started_at')->nullable();
            $table->timestamp('result_received_at')->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'recipient_number'], 'urg_batch_recipient_unique');
            $table->index(['device_id', 'status', 'sequence'], 'urg_device_status_sequence_idx');
            $table->index(['batch_id', 'status'], 'urg_attempt_batch_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ussd_recharge_attempts');
        Schema::dropIfExists('ussd_recharge_batches');
        Schema::dropIfExists('ussd_gateway_devices');
    }
};
