<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'bonus_wallet')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('bonus_wallet', 15, 2)->default(0)->after('main_wallet');
            });
        }

        if (! Schema::hasColumn('users', 'registration_ip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('registration_ip', 45)->nullable()->after('bonus_wallet');
            });
        }

        if (! Schema::hasColumn('users', 'last_login_ip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_login_ip', 45)->nullable()->after('registration_ip');
            });
        }

        if (! Schema::hasColumn('users', 'registration_device_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->char('registration_device_hash', 64)->nullable()->after('last_login_ip');
            });
        }

        if (! Schema::hasColumn('users', 'last_login_device_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->char('last_login_device_hash', 64)->nullable()->after('registration_device_hash');
            });
        }

        Schema::create('bonuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->string('group', 50);
            $table->json('enjoyment');
            $table->json('conditions')->nullable();
            $table->string('funding_type', 20)->nullable();
            $table->decimal('funding_value', 15, 4)->default(0);
            $table->decimal('funding_cap', 15, 2)->nullable();
            $table->decimal('bonus_wallet_amount', 15, 2)->default(0);
            $table->json('funding_whitelist')->nullable();
            $table->unsignedSmallInteger('frequency_per_user')->default(1);
            $table->unsignedSmallInteger('max_rewards_per_ip')->nullable();
            $table->unsignedSmallInteger('max_rewards_per_device')->nullable();
            $table->unsignedSmallInteger('reward_valid_days')->nullable();
            $table->integer('priority')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'group'], 'bonus_status_group_idx');
            $table->index(['starts_at', 'ends_at'], 'bonus_window_idx');
        });

        Schema::create('bonus_entitlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bonus_id')->constrained('bonuses')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('active');
            $table->string('registration_ip', 45)->nullable();
            $table->char('device_hash', 64)->nullable();
            $table->decimal('bonus_wallet_awarded', 15, 2)->default(0);
            $table->decimal('bonus_wallet_remaining', 15, 2)->default(0);
            $table->unsignedSmallInteger('funding_uses')->default(0);
            $table->dateTime('awarded_at');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->unique(['bonus_id', 'user_id'], 'bonus_user_unique');
            $table->index(['user_id', 'status', 'expires_at'], 'bonus_ent_user_status_idx');
            $table->index(['bonus_id', 'registration_ip'], 'bonus_ent_ip_idx');
            $table->index(['bonus_id', 'device_hash'], 'bonus_ent_device_idx');
        });

        Schema::create('bonus_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bonus_id')->nullable()->constrained('bonuses')->nullOnDelete();
            $table->foreignUuid('bonus_entitlement_id')->nullable()->constrained('bonus_entitlements')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_type', 50);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('balance_before', 15, 2)->nullable();
            $table->decimal('balance_after', 15, 2)->nullable();
            $table->string('funding_provider', 50)->nullable();
            $table->string('funding_reference')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->char('device_hash', 64)->nullable();
            $table->string('event_key', 191)->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['bonus_id', 'event_type'], 'bonus_log_campaign_event_idx');
            $table->index(['user_id', 'created_at'], 'bonus_log_user_date_idx');
            $table->index(['funding_provider', 'funding_reference'], 'bonus_log_funding_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_logs');
        Schema::dropIfExists('bonus_entitlements');
        Schema::dropIfExists('bonuses');

        $columns = collect([
            'bonus_wallet',
            'registration_ip',
            'last_login_ip',
            'registration_device_hash',
            'last_login_device_hash',
        ])->filter(fn (string $column) => Schema::hasColumn('users', $column))->all();

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
