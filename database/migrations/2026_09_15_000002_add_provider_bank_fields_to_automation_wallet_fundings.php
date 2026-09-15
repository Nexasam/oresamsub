<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->string('provider_bank_name')->nullable();
            $table->string('provider_bank_code')->nullable();
            $table->string('provider_account_name')->nullable();
            $table->text('provider_account_number')->nullable();
            $table->string('securewave_bank_info_id')->nullable();
            $table->timestamp('securewave_bank_info_saved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->dropColumn([
                'provider_bank_name',
                'provider_bank_code',
                'provider_account_name',
                'provider_account_number',
                'securewave_bank_info_id',
                'securewave_bank_info_saved_at',
            ]);
        });
    }
};
