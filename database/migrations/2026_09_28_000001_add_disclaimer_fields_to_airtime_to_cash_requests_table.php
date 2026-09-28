<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airtime_to_cash_requests', function (Blueprint $table): void {
            $table->timestamp('fraud_disclaimer_accepted_at')->nullable()->after('customer_note');
            $table->text('fraud_disclaimer_text')->nullable()->after('fraud_disclaimer_accepted_at');
            $table->string('fraud_disclaimer_ip')->nullable()->after('fraud_disclaimer_text');
            $table->text('fraud_disclaimer_user_agent')->nullable()->after('fraud_disclaimer_ip');
        });
    }

    public function down(): void
    {
        Schema::table('airtime_to_cash_requests', function (Blueprint $table): void {
            $table->dropColumn([
                'fraud_disclaimer_accepted_at',
                'fraud_disclaimer_text',
                'fraud_disclaimer_ip',
                'fraud_disclaimer_user_agent',
            ]);
        });
    }
};
