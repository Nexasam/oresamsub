<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::whenTableDoesntHaveColumn('standalone_features', 'purchase_mode', fn (Blueprint $table) => $table->string('purchase_mode', 32)->default('single')->after('billing_type'));
        Schema::whenTableDoesntHaveColumn('standalone_feature_subscriptions', 'slot_name', fn (Blueprint $table) => $table->string('slot_name', 150)->nullable()->after('standalone_feature_id'));
        Schema::whenTableDoesntHaveColumn('standalone_feature_subscriptions', 'slot_key', fn (Blueprint $table) => $table->string('slot_key', 160)->default('__single__')->after('slot_name'));
        Schema::whenTableDoesntHaveColumn('standalone_feature_purchases', 'slot_name', fn (Blueprint $table) => $table->string('slot_name', 150)->nullable()->after('standalone_feature_id'));
        Schema::whenTableDoesntHaveColumn('standalone_feature_purchases', 'slot_key', fn (Blueprint $table) => $table->string('slot_key', 160)->default('__single__')->after('slot_name'));

        if (Schema::hasIndex('standalone_feature_subscriptions', 'sfs_site_feature_unique')) {
            Schema::table('standalone_feature_subscriptions', fn (Blueprint $table) => $table->dropUnique('sfs_site_feature_unique'));
        }
        if (! Schema::hasIndex('standalone_feature_subscriptions', 'sfs_site_feature_slot_unique')) {
            Schema::table('standalone_feature_subscriptions', fn (Blueprint $table) => $table->unique(
                ['standalone_website_id', 'standalone_feature_id', 'slot_key'], 'sfs_site_feature_slot_unique'
            ));
        }

        DB::table('standalone_features')->where('slug', 'data-provider-integration')->update(['purchase_mode' => 'named_slots']);
    }

    public function down(): void
    {
        Schema::table('standalone_feature_purchases', fn (Blueprint $table) => $table->dropColumn(['slot_name', 'slot_key']));
        Schema::table('standalone_feature_subscriptions', function (Blueprint $table): void {
            $table->dropUnique('sfs_site_feature_slot_unique');
            $table->dropColumn(['slot_name', 'slot_key']);
            $table->unique(['standalone_website_id', 'standalone_feature_id'], 'sfs_site_feature_unique');
        });
        Schema::table('standalone_features', fn (Blueprint $table) => $table->dropColumn('purchase_mode'));
    }
};
