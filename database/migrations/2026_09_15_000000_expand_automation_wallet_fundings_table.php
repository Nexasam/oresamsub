<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->decimal('default_balance', 12, 2)->default(0)->after('amount_to_fund');
            $table->string('balance_response_path')->nullable()->after('last_balance');
            $table->string('securewave_customer_reference')->nullable()->after('linked_customer_email');
            $table->timestamp('securewave_customer_created_at')->nullable();
            $table->string('balance_source')->nullable();
            $table->uuid('balance_source_transaction_id')->nullable();
            $table->timestamp('last_balance_synced_at')->nullable();
            $table->timestamp('last_funded_at')->nullable();
            $table->text('last_error')->nullable();
            $table->unique('automation_id', 'automation_wallet_fundings_automation_unique');
        });

        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->string('linked_customer_email')->nullable()->change();
        });

        // Existing linked emails were already being funded by the legacy service,
        // so they represent customers provisioned before this metadata existed.
        DB::table('automation_wallet_fundings')
            ->whereNotNull('linked_customer_email')
            ->update(['securewave_customer_created_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->dropUnique('automation_wallet_fundings_automation_unique');
            $table->dropColumn([
                'default_balance',
                'balance_response_path',
                'securewave_customer_reference',
                'securewave_customer_created_at',
                'balance_source',
                'balance_source_transaction_id',
                'last_balance_synced_at',
                'last_funded_at',
                'last_error',
            ]);
        });

        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->string('linked_customer_email')->nullable(false)->change();
        });
    }
};
