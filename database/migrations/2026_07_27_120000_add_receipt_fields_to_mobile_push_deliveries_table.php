<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_push_deliveries', function (Blueprint $table) {
            $table->timestamp('receipt_checked_at')->nullable()->after('attempted_at');
            $table->timestamp('delivered_at')->nullable()->after('receipt_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('mobile_push_deliveries', function (Blueprint $table) {
            $table->dropColumn(['receipt_checked_at', 'delivered_at']);
        });
    }
};
