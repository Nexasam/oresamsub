<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('standalone_websites', 'price_level')) {
            Schema::table('standalone_websites', function (Blueprint $table): void {
                $table->unsignedTinyInteger('price_level')->nullable()->after('master_wallet');
            });
        }

        if (! Schema::hasTable('standalone_features')) {
            Schema::create('standalone_features', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('billing_type', 32)->default('one_time');
                $table->decimal('default_price', 18, 2);
                $table->decimal('level_1_price', 18, 2);
                $table->decimal('level_2_price', 18, 2);
                $table->decimal('level_3_price', 18, 2);
                $table->decimal('level_4_price', 18, 2);
                $table->decimal('default_monthly_price', 18, 2)->nullable();
                $table->decimal('level_1_monthly_price', 18, 2)->nullable();
                $table->decimal('level_2_monthly_price', 18, 2)->nullable();
                $table->decimal('level_3_monthly_price', 18, 2)->nullable();
                $table->decimal('level_4_monthly_price', 18, 2)->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('standalone_feature_subscriptions')) {
            Schema::create('standalone_feature_subscriptions', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->foreignUuid('standalone_website_id');
                $table->foreignUuid('standalone_feature_id');
                $table->string('status', 16)->default('active')->index();
                $table->timestamp('current_period_starts_at')->nullable();
                $table->timestamp('current_period_ends_at')->nullable()->index();
                $table->timestamp('grace_ends_at')->nullable()->index();
                $table->boolean('cancel_at_period_end')->default(false);
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
                $table->unique(['standalone_website_id', 'standalone_feature_id'], 'sfs_site_feature_unique');
                $table->foreign('standalone_website_id', 'sfs_site_foreign')->references('id')->on('standalone_websites')->cascadeOnDelete();
                $table->foreign('standalone_feature_id', 'sfs_feature_foreign')->references('id')->on('standalone_features')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('standalone_feature_purchases')) {
            Schema::create('standalone_feature_purchases', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->foreignUuid('standalone_website_id');
                $table->foreignUuid('standalone_feature_id');
                $table->foreignUuid('standalone_wallet_entry_id')->nullable();
                $table->string('transaction_id')->unique();
                $table->string('client_reference');
                $table->string('billing_event', 16);
                $table->decimal('amount', 18, 2);
                $table->string('applied_price_level', 16);
                $table->timestamp('period_starts_at')->nullable();
                $table->timestamp('period_ends_at')->nullable();
                $table->timestamps();
                $table->unique(['standalone_website_id', 'client_reference'], 'sfp_site_reference_unique');
                $table->foreign('standalone_website_id', 'sfp_site_foreign')->references('id')->on('standalone_websites')->cascadeOnDelete();
                $table->foreign('standalone_feature_id', 'sfp_feature_foreign')->references('id')->on('standalone_features')->cascadeOnDelete();
                $table->foreign('standalone_wallet_entry_id', 'sfp_wallet_entry_foreign')->references('id')->on('standalone_wallet_entries')->nullOnDelete();
            });
        }

        DB::table('standalone_features')->updateOrInsert(['slug' => 'data-provider-integration'], [
            'id' => DB::table('standalone_features')->where('slug', 'data-provider-integration')->value('id') ?? (string) Str::uuid(),
            'slug' => 'data-provider-integration',
            'name' => 'Data provider integration',
            'description' => 'Access to data-provider integration features for the standalone website.',
            'billing_type' => 'one_time',
            'default_price' => 40000,
            'level_1_price' => 38000,
            'level_2_price' => 37000,
            'level_3_price' => 36000,
            'level_4_price' => 35000,
            'default_monthly_price' => null,
            'level_1_monthly_price' => null,
            'level_2_monthly_price' => null,
            'level_3_monthly_price' => null,
            'level_4_monthly_price' => null,
            'is_active' => true,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_feature_purchases');
        Schema::dropIfExists('standalone_feature_subscriptions');
        Schema::dropIfExists('standalone_features');
        Schema::table('standalone_websites', fn (Blueprint $table) => $table->dropColumn('price_level'));
    }
};
