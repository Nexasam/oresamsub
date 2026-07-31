<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_wallet_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->decimal('funding_threshold', 15, 2)->default(0);
            $table->decimal('funding_amount', 15, 2)->nullable();
            $table->string('notification_email')->nullable();
            $table->string('admin_copy_email')->nullable();
            $table->text('funding_bank_name')->nullable();
            $table->text('funding_bank_code')->nullable();
            $table->text('funding_account_name')->nullable();
            $table->text('funding_account_number')->nullable();
            $table->string('transfer_provider')->nullable();
            $table->boolean('automatic_transfer_enabled')->default(false);
            $table->date('last_notified_on')->nullable();
            $table->dateTime('last_checked_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['enabled', 'funding_threshold'], 'affiliate_wallet_monitor_idx');
        });

        Schema::create('affiliate_funding_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('affiliate_wallet_setting_id')->constrained('affiliate_wallet_settings')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 40);
            $table->decimal('wallet_balance', 15, 2);
            $table->decimal('funding_threshold', 15, 2);
            $table->decimal('requested_amount', 15, 2)->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('triggered_at');
            $table->timestamps();

            $table->index(['user_id', 'triggered_at'], 'affiliate_attempt_user_date_idx');
            $table->index(['status', 'triggered_at'], 'affiliate_attempt_status_idx');
        });

        Schema::create('upline_funding_bonus_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('reward_type', 20)->default('flat');
            $table->decimal('reward_value', 15, 4)->default(0);
            $table->decimal('reward_cap', 15, 2)->nullable();
            $table->unsignedSmallInteger('frequency_per_downline')->default(1);
            $table->json('funding_whitelist')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['enabled', 'starts_at', 'ends_at'], 'upline_bonus_active_window_idx');
        });

        Schema::create('upline_funding_bonus_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('upline_funding_bonus_setting_id');
            $table->foreignUuid('upline_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('downline_id')->constrained('users')->cascadeOnDelete();
            $table->string('funding_provider', 50);
            $table->string('funding_reference');
            $table->decimal('funded_amount', 15, 2);
            $table->decimal('bonus_amount', 15, 2);
            $table->decimal('bonus_balance_before', 15, 2);
            $table->decimal('bonus_balance_after', 15, 2);
            $table->unsignedSmallInteger('sequence');
            $table->string('event_key', 191)->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(
                ['upline_funding_bonus_setting_id', 'downline_id'],
                'upline_bonus_setting_downline_idx'
            );
            $table->index(['upline_id', 'created_at'], 'upline_bonus_user_date_idx');
            $table->index(['funding_provider', 'funding_reference'], 'upline_bonus_funding_idx');
            $table->foreign(
                'upline_funding_bonus_setting_id',
                'upline_bonus_log_setting_fk'
            )->references('id')->on('upline_funding_bonus_settings')->cascadeOnDelete();
        });

        // The webhook controllers check before processing, while this database
        // constraint closes the concurrent-delivery race for financial credits.
        Schema::table('funding_webhook_payloads', function (Blueprint $table) {
            $table->unique(
                ['funding_slug', 'transaction_reference'],
                'funding_provider_reference_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('funding_webhook_payloads', function (Blueprint $table) {
            $table->dropUnique('funding_provider_reference_unique');
        });
        Schema::dropIfExists('upline_funding_bonus_logs');
        Schema::dropIfExists('upline_funding_bonus_settings');
        Schema::dropIfExists('affiliate_funding_attempts');
        Schema::dropIfExists('affiliate_wallet_settings');
    }
};
