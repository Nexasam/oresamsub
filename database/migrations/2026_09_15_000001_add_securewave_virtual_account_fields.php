<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_options', function (Blueprint $table) {
            $table->text('virtual_account_id_number')->nullable();
        });

        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->string('customer_first_name')->nullable();
            $table->string('customer_last_name')->nullable();
            $table->string('customer_phone_number')->nullable();
            $table->string('securewave_account_number')->nullable();
            $table->string('securewave_account_name')->nullable();
            $table->string('securewave_bank_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('funding_options', function (Blueprint $table) {
            $table->dropColumn('virtual_account_id_number');
        });

        Schema::table('automation_wallet_fundings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_first_name',
                'customer_last_name',
                'customer_phone_number',
                'securewave_account_number',
                'securewave_account_name',
                'securewave_bank_name',
            ]);
        });
    }
};
